<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Repositories\Contracts\CategoryRepositoryInterface;
// use Illuminate\Support\Str;

// class CategoryController extends Controller
// {
//     public function __construct(protected CategoryRepositoryInterface $categoryRepository)
//     {
//     }

//     public function index(Request $request)
//     {
//         $keyword = $request->query('search');
//         $parentId = $request->query('parent_id');
//         $sortBy = $request->query('sort_by', 'position');

//         $query = $this->categoryRepository->newQuery();

//         // Tìm kiếm
//         if ($keyword) {
//             $query->where(function ($q) use ($keyword) {
//                 $q->where('name', 'like', "%{$keyword}%")
//                     ->orWhere('slug', 'like', "%{$keyword}%")
//                     ->orWhere('description', 'like', "%{$keyword}%");
//             });
//         }

//         // Lọc theo danh mục cha
//         if ($parentId !== null) {
//             if ($parentId === '0' || $parentId === 'root') {
//                 $query->whereNull('parent_id');
//             } else {
//                 $query->where('parent_id', $parentId);
//             }
//         }

//         // Sắp xếp
//         switch ($sortBy) {
//             case 'name':
//                 $query->orderBy('name', 'asc');
//                 break;
//             case 'latest':
//                 $query->orderBy('created_at', 'desc');
//                 break;
//             case 'products_count':
//                 $query->withCount('products')->orderBy('products_count', 'desc');
//                 break;
//             default:
//                 $query->orderBy('position', 'asc')->orderBy('id', 'asc');
//         }

//         $query->with(['parent', 'children'])->withCount(['products', 'children']);
//         $categories = $this->categoryRepository->paginateQuery($query, 15);

//         $parentCategories = $this->categoryRepository->getRootCategories();
//         $totalCategories = $this->categoryRepository->count();
//         $rootCategories = $this->categoryRepository->newQuery()->whereNull('parent_id')->count();
//         $withProducts = $this->categoryRepository->newQuery()->has('products')->count();
//         $emptyCategories = $this->categoryRepository->newQuery()->doesntHave('products')->count();

//         return view('admin.categories.index', compact(
//             'categories',
//             'keyword',
//             'parentId',
//             'sortBy',
//             'parentCategories',
//             'totalCategories',
//             'rootCategories',
//             'withProducts',
//             'emptyCategories'
//         ));
//     }

//     public function create()
//     {
//         $parentCategories = $this->categoryRepository->getTree();
//         return view('admin.categories.create', compact('parentCategories'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'name' => 'required|string|max:255|unique:categories,name',
//             'slug' => 'nullable|string|unique:categories,slug',
//             'description' => 'nullable|string|max:1000',
//             'parent_id' => 'nullable|exists:categories,id',
//             'position' => 'nullable|integer|min:1',
//         ]);

//         // Tạo slug nếu trống
//         if (empty($validated['slug'])) {
//             $validated['slug'] = Str::slug($validated['name']);
//         }

//         // Tính level
//         $validated['level'] = !empty($validated['parent_id'])
//             ? $this->categoryRepository->findOrFail($validated['parent_id'])->level + 1
//             : 0;

//         $parentId = $validated['parent_id'] ?? null;

//         // Nếu không nhập position → thêm cuối
//         if (empty($validated['position'])) {
//             $maxPosition = $this->categoryRepository->newQuery()
//                 ->where('parent_id', $parentId)
//                 ->max('position') ?? 0;
//             $validated['position'] = $maxPosition + 1;
//         } else {
//             // Nếu nhập vị trí cụ thể → dời các danh mục sau xuống
//             $this->categoryRepository->newQuery()
//                 ->where('parent_id', $parentId)
//                 ->where('position', '>=', $validated['position'])
//                 ->increment('position');
//         }

//         $this->categoryRepository->create($validated);

//         return redirect()->route('admin.categories.index')
//             ->with('success', 'Thêm danh mục thành công!');
//     }

//     public function show($id)
//     {
//         $category = $this->categoryRepository->newQuery()
//             ->with(['parent', 'children', 'products'])
//             ->withCount(['products', 'children'])
//             ->findOrFail($id);

//         $products = $category->products()->with('images')->paginate(12);
//         return view('admin.categories.show', compact('category', 'products'));
//     }

//     public function edit($id)
//     {
//         $category = $this->categoryRepository->findOrFail((int)$id);
//         $parentCategories = $this->categoryRepository->getTree()
//             ->filter(fn($cat) => $cat->id != $id);
//         return view('admin.categories.edit', compact('category', 'parentCategories'));
//     }

//     public function update(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'name' => 'required|string|max:255|unique:categories,name,' . $id,
//             'slug' => 'required|string|unique:categories,slug,' . $id,
//             'description' => 'nullable|string|max:1000',
//             'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
//             'position' => 'nullable|integer|min:1',
//         ]);

//         $validated['level'] = !empty($validated['parent_id'])
//             ? $this->categoryRepository->findOrFail($validated['parent_id'])->level + 1
//             : 0;

//         $parentId = $validated['parent_id'] ?? null;
//         $current = $this->categoryRepository->findOrFail($id);

//         if (isset($validated['position']) && $validated['position'] != $current->position) {
//             $this->categoryRepository->newQuery()
//                 ->where('parent_id', $parentId)
//                 ->where('id', '!=', $id)
//                 ->where('position', '>=', $validated['position'])
//                 ->increment('position');
//         }

//         $this->categoryRepository->update((int)$id, $validated);

//         return redirect()->route('admin.categories.index')
//             ->with('success', 'Cập nhật danh mục thành công!');
//     }

//     public function destroy($id)
//     {
//         $category = $this->categoryRepository->findOrFail((int)$id);

//         // Ngăn xóa nếu có con hoặc sản phẩm
//         if ($category->children()->count() > 0) {
//             return back()->with('error', 'Không thể xóa danh mục có danh mục con!');
//         }
//         if ($category->products()->count() > 0) {
//             return back()->with('error', 'Không thể xóa danh mục đang có sản phẩm!');
//         }

//         $parentId = $category->parent_id;

//         $this->categoryRepository->delete((int)$id);

//         // 🧩 Tự động sắp xếp lại vị trí sau khi xóa
//         $categories = $this->categoryRepository->newQuery()
//             ->where('parent_id', $parentId)
//             ->orderBy('position', 'asc')
//             ->get();

//         $position = 1;
//         foreach ($categories as $cat) {
//             $this->categoryRepository->update($cat->id, ['position' => $position]);
//             $position++;
//         }

//         return redirect()->route('admin.categories.index')
//             ->with('success', 'Xóa danh mục thành công và đã sắp xếp lại vị trí!');
//     }

//     public function bulkDelete(Request $request)
//     {
//         $ids = $request->input('ids', []);
//         if (empty($ids)) {
//             return response()->json(['success' => false, 'message' => 'Không có danh mục nào được chọn']);
//         }

//         $deleted = 0;
//         $errors = [];

//         foreach ($ids as $id) {
//             try {
//                 $category = $this->categoryRepository->findOrFail((int)$id);
//                 if ($category->children()->count() > 0 || $category->products()->count() > 0) {
//                     $errors[] = "Danh mục '{$category->name}' có danh mục con hoặc sản phẩm";
//                     continue;
//                 }

//                 $this->categoryRepository->delete((int)$id);
//                 $deleted++;
//             } catch (\Exception $e) {
//                 $errors[] = "Lỗi khi xóa ID {$id}: " . $e->getMessage();
//             }
//         }

//         // Sau khi xóa hàng loạt → sắp xếp lại vị trí
//         $this->categoryRepository->newQuery()
//             ->whereNull('parent_id')
//             ->orderBy('position', 'asc')
//             ->get()
//             ->each(function ($cat, $i) {
//                 $this->categoryRepository->update($cat->id, ['position' => $i + 1]);
//             });

//         $message = $deleted > 0
//             ? "Đã xóa {$deleted} danh mục và sắp xếp lại vị trí. " . (!empty($errors) ? 'Lỗi: ' . implode(', ', $errors) : '')
//             : implode(', ', $errors);

//         return response()->json(['success' => $deleted > 0, 'message' => $message]);
//     }

//     public function updatePosition(Request $request)
//     {
//         $positions = $request->input('positions', []);
//         foreach ($positions as $id => $position) {
//             $this->categoryRepository->update((int)$id, ['position' => (int)$position]);
//         }
//         return response()->json(['success' => true, 'message' => 'Cập nhật vị trí thành công']);
//     }

//     public function getCategories(Request $request)
//     {
//         $parentId = $request->query('parent_id');
//         $query = $this->categoryRepository->newQuery();

//         if ($parentId !== null) {
//             $parentId === '0'
//                 ? $query->whereNull('parent_id')
//                 : $query->where('parent_id', $parentId);
//         }

//         $categories = $query->orderBy('position')->get(['id', 'name', 'slug', 'parent_id']);
//         return response()->json($categories);
//     }
// }








namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected CategoryService $categoryService
    ) {}

    public function index(Request $request)
    {
        $keyword = $request->query('search');
        $parentId = $request->query('parent_id');
        $sortBy = $request->query('sort_by', 'position');

        $query = $this->categoryRepository->newQuery();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($parentId !== null) {
            if ($parentId === '0' || $parentId === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }

        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'products_count':
                $query->withCount('products')->orderBy('products_count', 'desc');
                break;
            default:
                $query->orderBy('position', 'asc')->orderBy('id', 'asc');
        }

        $query->with(['parent', 'children'])->withCount(['products', 'children']);
        $categories = $this->categoryRepository->paginateQuery($query, 15);

        $parentCategories = $this->categoryRepository->getRootCategories();
        $totalCategories = $this->categoryRepository->count();
        $rootCategories = $this->categoryRepository->newQuery()->whereNull('parent_id')->count();
        $withProducts = $this->categoryRepository->newQuery()->has('products')->count();
        $emptyCategories = $this->categoryRepository->newQuery()->doesntHave('products')->count();

        return view('admin.categories.index', compact(
            'categories', 'keyword', 'parentId', 'sortBy',
            'parentCategories', 'totalCategories', 'rootCategories', 'withProducts', 'emptyCategories'
        ));
    }

    public function create()
    {
        $parentCategories = $this->categoryRepository->getTree();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(CategoryRequest $request)
    {
        $this->categoryService->createCategory($request->validated());
        return redirect()->route('admin.categories.index')
            ->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $category = $this->categoryRepository->findOrFail((int)$id);
        $parentCategories = $this->categoryRepository->getTree()
            ->filter(fn($cat) => $cat->id != $id);
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(CategoryRequest $request, $id)
    {
        $this->categoryService->updateCategory((int)$id, $request->validated());
        return redirect()->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory((int)$id);
        return redirect()->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công và đã sắp xếp lại vị trí!');
    }

    public function bulkDelete(Request $request)
    {
        $result = $this->categoryService->bulkDelete($request->input('ids', []));
        $message = $result['deleted'] > 0
            ? "Đã xóa {$result['deleted']} danh mục." . (!empty($result['errors']) ? ' Lỗi: ' . implode(', ', $result['errors']) : '')
            : implode(', ', $result['errors']);

        return response()->json(['success' => $result['deleted'] > 0, 'message' => $message]);
    }

    public function updatePosition(Request $request)
    {
        $this->categoryService->updatePosition($request->input('positions', []));
        return response()->json(['success' => true, 'message' => 'Cập nhật vị trí thành công']);
    }

    public function show($id)
    {
        $category = $this->categoryRepository->newQuery()
            ->with(['parent', 'children', 'products'])
            ->withCount(['products', 'children'])
            ->findOrFail($id);

        $products = $category->products()->with('images')->paginate(12);
        return view('admin.categories.show', compact('category', 'products'));
    }

    public function getCategories(Request $request)
    {
        $parentId = $request->query('parent_id');
        $query = $this->categoryRepository->newQuery();

        if ($parentId !== null) {
            $parentId === '0'
                ? $query->whereNull('parent_id')
                : $query->where('parent_id', $parentId);
        }

        $categories = $query->orderBy('position')->get(['id', 'name', 'slug', 'parent_id']);
        return response()->json($categories);
    }
}