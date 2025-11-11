<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductVariantService;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductVariantController extends Controller
{
    protected ProductVariantService $service;

    public function __construct(ProductVariantService $service)
    {
        $this->service = $service;
    }

    public function index(Product $product)
    {
        $variants = $product->variants()->with('stockItems')->get();
        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product)
    {
        return view('admin.products.variants.create', compact('product'));
    }

    public function store(ProductVariantRequest $request, Product $product)
    {
        try {
            $this->service->createVariant($product, $request->validated());
            return redirect()->route('admin.products.variants.index', $product)
                ->with('success', 'Tạo biến thể thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Có lỗi khi tạo biến thể.');
        }
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        $variant->load('stockItems');
        return view('admin.products.variants.edit', compact('product', 'variant'));
    }

    public function update(ProductVariantRequest $request, Product $product, ProductVariant $variant)
    {
        try {
            $this->service->updateVariant($variant, $request->validated());
            return redirect()->route('admin.products.variants.index', $product)
                ->with('success', 'Cập nhật biến thể thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Có lỗi khi cập nhật biến thể.');
        }
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        try {
            $this->service->deleteVariant($variant);
            return redirect()->route('admin.products.variants.index', $product)
                ->with('success', 'Xóa biến thể thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Không thể xóa biến thể.');
        }
    }

    public function stock(Product $product, ProductVariant $variant)
    {
        $variant->load('stockItems');
        return view('admin.products.variants.stock', compact('product', 'variant'));
    }

    public function updateStock(ProductVariantRequest $request, Product $product, ProductVariant $variant)
    {
        try {
            $this->service->updateStock($variant, $request->validated());
            return back()->with('success', 'Cập nhật tồn kho thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Không thể cập nhật tồn kho.');
        }
    }

    public function bulkCreate(ProductVariantRequest $request, Product $product)
    {
        try {
            $count = $this->service->bulkCreate($product, $request->validated()['variants']);
            return redirect()->route('admin.products.variants.index', $product)
                ->with('success', "Tạo $count biến thể thành công!");
        } catch (Exception $e) {
            return back()->with('error', 'Có lỗi khi tạo nhiều biến thể.');
        }
    }

    public function storeMany(ProductVariantRequest $request, Product $product)
    {
        try {
            $this->service->storeMany($product, $request->validated()['variants']);
            return redirect()->route('admin.products.variants.index', $product)
                ->with('success', 'Đã tạo biến thể tự động thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Không thể tạo biến thể tự động.');
        }
    }

    public function checkSku(Product $product, ProductVariantRequest $request)
    {
        $sku = $request->query('sku');
        $exists = $product->variants()->where('sku', $sku)->exists();
        return response()->json(['exists' => $exists]);
    }
}
