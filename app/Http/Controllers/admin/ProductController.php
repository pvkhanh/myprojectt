<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Enums\ProductStatus;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepo;
    protected CategoryRepositoryInterface $categoryRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        CategoryRepositoryInterface $categoryRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
    }

    // ================== INDEX ==================
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'category_id', 'status', 'price_range', 'sort_by']);
        $perPage = $request->input('per_page', 15);

        $products = $this->productRepo->searchPaginated($filters, $perPage);

        $totalProducts = $this->productRepo->countByStatus(ProductStatus::Active->value)
            + $this->productRepo->countByStatus(ProductStatus::Draft->value)
            + $this->productRepo->countByStatus(ProductStatus::Inactive->value);

        $activeProducts = $this->productRepo->countByStatus(ProductStatus::Active->value);
        $hiddenProducts = $this->productRepo->countByStatus(ProductStatus::Inactive->value);
        $outOfStock = $this->productRepo->countOutOfStock();

        $categories = $this->categoryRepo->getRootCategories();
        $statuses = ProductStatus::cases();

        return view('admin.products.index', compact(
            'products', 'categories', 'totalProducts',
            'activeProducts', 'outOfStock', 'hiddenProducts', 'statuses'
        ));
    }

    // ================== CREATE ==================
    public function create(Request $request)
    {
        $categories = $this->categoryRepo->getTree();
        $statuses = ProductStatus::cases();
        $images = Image::all();
        $selectedCategoryId = $request->query('category_id');

        return view('admin.products.create', compact('categories', 'statuses', 'images', 'selectedCategoryId'));
    }

    // ================== STORE ==================
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        // --- Tạo slug nếu chưa có ---
        if (empty($validated['slug']) && isset($validated['name'])) {
            $slug = strtolower($validated['name']);
            $slug = preg_replace('/[\s]+/', '-', $slug);
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
            $validated['slug'] = trim($slug, '-');
        }

        // --- Kiểm tra trùng ---
        $existing = Product::where('name', $validated['name'])
            ->orWhere('slug', $validated['slug'])
            ->first();

        if ($existing) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Sản phẩm đã tồn tại!'], 422)
                : back()->withInput()->with('error', 'Sản phẩm đã tồn tại (tên hoặc slug trùng)!');
        }

        // --- Xử lý image_ids ---
        $imageIds = $validated['image_ids'] ?? [];
        if (is_string($imageIds)) $imageIds = array_filter(explode(',', $imageIds));
        $validated['image_ids'] = $imageIds;
        $validated['primary_image_id'] = $validated['primary_image_id'] ?? ($imageIds[0] ?? null);

        // --- Tạo sản phẩm ---
        $product = $this->productRepo->create($validated);

        return $request->ajax()
            ? response()->json(['success' => true, 'product' => $product])
            : redirect()->route('admin.products.index')->with('success', 'Tạo sản phẩm thành công!');
    }

    // ================== SHOW ==================
    public function show(int $id)
    {
        $product = $this->productRepo->find($id);
        $product->load(['categories', 'images', 'variants.stockItems', 'reviews.user']);
        return view('admin.products.show', compact('product'));
    }

    // ================== EDIT ==================
    public function edit(int $id)
    {
        $product = $this->productRepo->find($id);
        $product->load(['categories', 'images']);

        $categories = $this->categoryRepo->getTree();
        $statuses = ProductStatus::cases();
        $selectedImageIds = $product->images->pluck('id')->toArray();
        $primaryImage = $product->images->where('pivot.is_main', true)->first();

        return view('admin.products.edit', compact(
            'product', 'categories', 'statuses', 'selectedImageIds', 'primaryImage'
        ));
    }

    // ================== UPDATE ==================
    public function update(UpdateProductRequest $request, int $id)
    {
        $validated = $request->validated();

        // --- Xử lý image_ids ---
        $imageIds = $validated['image_ids'] ?? [];
        if (is_string($imageIds)) $imageIds = array_filter(explode(',', $imageIds));
        $validated['image_ids'] = $imageIds;
        $validated['primary_image_id'] = $validated['primary_image_id'] ?? ($imageIds[0] ?? null);

        $product = $this->productRepo->updateAndReturn($id, $validated);

        return $request->ajax()
            ? response()->json(['success' => true, 'product' => $product])
            : redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // ================== TRASH ==================
    public function trash()
    {
        $products = Product::onlyTrashed()->with('categories')->paginate(10);
        return view('admin.products.trash', compact('products'));
    }

    // ================== DESTROY ==================
    public function destroy(Request $request, int $id)
    {
        $deleted = $this->productRepo->delete($id);

        return $request->ajax()
            ? response()->json(['success' => $deleted])
            : redirect()->route('admin.products.index')->with(
                $deleted ? 'success' : 'error',
                $deleted ? 'Đã xóa mềm sản phẩm!' : 'Không tìm thấy sản phẩm để xóa!'
            );
    }

    // ================== FORCE DESTROY ==================
    public function forceDestroy(Request $request, int $id)
    {
        $deleted = $this->productRepo->forceDelete($id);

        return $request->ajax()
            ? response()->json(['success' => $deleted])
            : redirect()->route('admin.products.trash')->with(
                $deleted ? 'success' : 'error',
                $deleted ? 'Xóa vĩnh viễn sản phẩm thành công!' : 'Không tìm thấy sản phẩm để xóa!'
            );
    }

    // ================== RESTORE ==================
    public function restore(Request $request, int $id)
    {
        $restored = $this->productRepo->restore($id);

        return $request->ajax()
            ? response()->json(['success' => $restored])
            : redirect()->route('admin.products.trash')->with(
                $restored ? 'success' : 'error',
                $restored ? 'Khôi phục sản phẩm thành công!' : 'Không tìm thấy sản phẩm để khôi phục!'
            );
    }

    // ================== BULK DELETE ==================
    public function bulkDelete(Request $request)
    {
        $ids = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ])['ids'];

        $deletedCount = $this->productRepo->bulkDelete($ids);

        return $request->ajax()
            ? response()->json(['success' => true, 'deleted_count' => $deletedCount])
            : back()->with('success', "Xóa {$deletedCount} sản phẩm thành công!");
    }

    // ================== BULK UPDATE STATUS ==================
    public function bulkUpdateStatus(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'status' => ['required', 'in:' . implode(',', ProductStatus::values())]
        ]);

        $updatedCount = $this->productRepo->bulkUpdateStatus($data['ids'], $data['status']);

        return $request->ajax()
            ? response()->json(['success' => true, 'updated_count' => $updatedCount])
            : back()->with('success', "Cập nhật trạng thái {$updatedCount} sản phẩm thành công!");
    }

    // ================== RESTORE ALL ==================
    public function restoreAll(Request $request)
    {
        $restoredCount = $this->productRepo->restoreAll();

        return $request->ajax()
            ? response()->json(['success' => true, 'restored_count' => $restoredCount])
            : redirect()->route('admin.products.trash')->with('success', "Khôi phục {$restoredCount} sản phẩm thành công!");
    }

    // ================== FORCE DELETE ALL ==================
    public function forceDeleteAll(Request $request)
    {
        $deletedCount = $this->productRepo->forceDeleteAll();

        return $request->ajax()
            ? response()->json(['success' => true, 'deleted_count' => $deletedCount])
            : redirect()->route('admin.products.trash')->with('success', "Xóa vĩnh viễn {$deletedCount} sản phẩm thành công!");
    }
}


// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\ProductRequest;
// use App\Services\ProductService;
// use Illuminate\Http\Request;

// class ProductController extends Controller
// {
//     protected ProductService $service;

//     public function __construct(ProductService $service)
//     {
//         $this->service = $service;
//     }

//     public function index(Request $request)
//     {
//         $filters = $request->only(['keyword', 'category_id', 'status', 'price_range', 'sort_by']);
//         $perPage = $request->input('per_page', 15);
//         $data = $this->service->index($filters, $perPage);
//         return view('admin.products.index', $data);
//     }

//     public function create(Request $request)
//     {
//         $data = $this->service->create($request->query('category_id'));
//         return view('admin.products.create', $data);
//     }

//     public function store(ProductRequest $request)
//     {
//         $this->service->store($request->validated());
//         return redirect()->route('admin.products.index')->with('success', 'Tạo sản phẩm thành công!');
//     }

//     public function show(int $id)
//     {
//         $product = $this->service->show($id);
//         return view('admin.products.show', compact('product'));
//     }

//     public function edit(int $id)
//     {
//         $data = $this->service->edit($id);
//         return view('admin.products.edit', $data);
//     }

//     public function update(ProductRequest $request, int $id)
//     {
//         $this->service->update($id, $request->validated());
//         return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
//     }

//     public function trash()
//     {
//         $products = $this->service->trash();
//         return view('admin.products.trash', compact('products'));
//     }

//     public function destroy(int $id)
//     {
//         $this->service->destroy($id);
//         return redirect()->route('admin.products.index')->with('success', 'Đã xóa mềm sản phẩm!');
//     }

//     public function forceDestroy(int $id)
//     {
//         $this->service->forceDestroy($id);
//         return redirect()->route('admin.products.trash')->with('success', 'Xóa vĩnh viễn sản phẩm thành công!');
//     }

//     public function restore(int $id)
//     {
//         $this->service->restore($id);
//         return redirect()->route('admin.products.trash')->with('success', 'Khôi phục sản phẩm thành công!');
//     }

//     public function bulkDelete(Request $request)
//     {
//         $ids = $request->validate(['ids' => 'required|array'])['ids'];
//         $this->service->bulkDelete($ids);
//         return back()->with('success', 'Xóa nhiều sản phẩm thành công!');
//     }

//     public function bulkUpdateStatus(Request $request)
//     {
//         $data = $request->validate([
//             'ids' => 'required|array',
//             'status' => 'required|string',
//         ]);
//         $this->service->bulkUpdateStatus($data['ids'], $data['status']);
//         return back()->with('success', 'Cập nhật trạng thái sản phẩm thành công!');
//     }

//     public function restoreAll()
//     {
//         $this->service->restoreAll();
//         return redirect()->route('admin.products.trash')->with('success', 'Khôi phục tất cả sản phẩm thành công!');
//     }

//     public function forceDeleteAll()
//     {
//         $this->service->forceDeleteAll();
//         return redirect()->route('admin.products.trash')->with('success', 'Xóa vĩnh viễn tất cả sản phẩm thành công!');
//     }
// }