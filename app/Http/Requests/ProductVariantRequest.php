<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->route()->getActionMethod();
        $variantId = $this->route('variant')->id ?? null;

        return match ($action) {
            'store', 'storeMany' => [
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:product_variants,sku',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'stock_location' => 'nullable|string|max:255',
            ],

            'update' => [
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:product_variants,sku,' . $variantId,
                'price' => 'required|numeric|min:0',
            ],

            'updateStock' => [
                'location' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'action' => 'required|in:set,increase,decrease',
            ],

            'bulkCreate' => [
                'variants' => 'required|array|min:1',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.sku' => 'required|string|max:100|unique:product_variants,sku',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.quantity' => 'nullable|integer|min:0',
            ],

            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên biến thể không được để trống.',
            'sku.required' => 'Mã SKU là bắt buộc.',
            'sku.unique' => 'Mã SKU này đã tồn tại.',
            'price.required' => 'Giá là bắt buộc.',
            'price.numeric' => 'Giá phải là số.',
            'variants.required' => 'Phải có ít nhất một biến thể.',
            'variants.*.name.required' => 'Tên biến thể là bắt buộc.',
            'variants.*.sku.required' => 'SKU biến thể là bắt buộc.',
        ];
    }
}