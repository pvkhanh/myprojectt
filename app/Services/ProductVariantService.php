<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductVariantService
{
    public function createVariant(Product $product, array $data)
    {
        DB::beginTransaction();
        try {
            $variant = $product->variants()->create([
                'name' => $data['name'],
                'sku' => $data['sku'],
                'price' => $data['price'],
            ]);

            if (!empty($data['stock_quantity'])) {
                StockItem::create([
                    'variant_id' => $variant->id,
                    'location' => $data['stock_location'] ?? 'default',
                    'quantity' => $data['stock_quantity'],
                ]);
            }

            DB::commit();
            return $variant;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Lỗi tạo biến thể: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateVariant(ProductVariant $variant, array $data)
    {
        try {
            $variant->update([
                'name' => $data['name'],
                'sku' => $data['sku'],
                'price' => $data['price'],
            ]);
            return $variant;
        } catch (Exception $e) {
            Log::error("Lỗi cập nhật biến thể: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteVariant(ProductVariant $variant)
    {
        DB::beginTransaction();
        try {
            $variant->stockItems()->delete();
            $variant->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Lỗi xóa biến thể: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateStock(ProductVariant $variant, array $data)
    {
        DB::beginTransaction();
        try {
            $stockItem = StockItem::firstOrCreate(
                ['variant_id' => $variant->id, 'location' => $data['location']],
                ['quantity' => 0]
            );

            switch ($data['action']) {
                case 'set':
                    $stockItem->quantity = $data['quantity'];
                    break;
                case 'increase':
                    $stockItem->quantity += $data['quantity'];
                    break;
                case 'decrease':
                    $stockItem->quantity = max(0, $stockItem->quantity - $data['quantity']);
                    break;
            }

            $stockItem->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Lỗi cập nhật tồn kho: " . $e->getMessage());
            throw $e;
        }
    }

    public function bulkCreate(Product $product, array $variants)
    {
        DB::beginTransaction();
        try {
            foreach ($variants as $data) {
                $variant = $product->variants()->create([
                    'name' => $data['name'],
                    'sku' => $data['sku'],
                    'price' => $data['price'],
                ]);

                if (!empty($data['quantity'])) {
                    StockItem::create([
                        'variant_id' => $variant->id,
                        'location' => 'default',
                        'quantity' => $data['quantity'],
                    ]);
                }
            }
            DB::commit();
            return count($variants);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Lỗi tạo nhiều biến thể: " . $e->getMessage());
            throw $e;
        }
    }

    public function storeMany(Product $product, array $variants)
    {
        try {
            foreach ($variants as $data) {
                $product->variants()->create($data);
            }
        } catch (Exception $e) {
            Log::error("Lỗi storeMany: " . $e->getMessage());
            throw $e;
        }
    }
}
