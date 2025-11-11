<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cho phép mọi user, phân quyền ở middleware nếu cần
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product'); // ID khi update, null khi create

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId)
            ],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($productId)
            ],
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image_ids' => 'nullable|array',
            'image_ids.*' => 'integer|exists:images,id',
            'primary_image_id' => 'nullable|integer|exists:images,id',
            'status' => 'required|in:active,draft,inactive',
            'stock' => 'nullable|array',
            'stock.*.location' => 'required_with:stock|string|max:100',
            'stock.*.quantity' => 'required_with:stock|integer|min:0',
        ];
    }

    /**
     * Chuẩn hóa dữ liệu trước khi trả về service
     */
    protected function prepareForValidation()
    {
        // Chuyển image_ids từ chuỗi sang mảng nếu cần
        $imageIds = $this->input('image_ids', []);
        if (is_string($imageIds)) {
            $imageIds = array_filter(explode(',', $imageIds));
        }
        $this->merge(['image_ids' => $imageIds]);

        // Thiết lập primary_image_id mặc định nếu chưa có
        $primaryImageId = $this->input('primary_image_id') ?? ($imageIds[0] ?? null);
        $this->merge(['primary_image_id' => $primaryImageId]);

        // Tạo slug nếu chưa có
        $slug = $this->input('slug');
        $name = $this->input('name');
        if (empty($slug) && !empty($name)) {
            $slug = strtolower($name);
            $slug = preg_replace('/[\s]+/', '-', $slug);
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
            $slug = trim($slug, '-');
            $this->merge(['slug' => $slug]);
        }
    }
}