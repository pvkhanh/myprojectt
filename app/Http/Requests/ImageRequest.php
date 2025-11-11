<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->getMethod();

        if ($method === 'POST' && $this->routeIs('admin.images.store')) {
            // store nhiều ảnh
            return [
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'type' => 'required|string|max:50',
                'alt_text.*' => 'nullable|string|max:255',
                'optimize' => 'boolean',
            ];
        }

        if (in_array($method, ['PUT', 'PATCH']) && $this->routeIs('admin.images.update')) {
            // update 1 ảnh
            return [
                'type' => 'required|string|max:50',
                'alt_text' => 'nullable|string|max:255',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ];
        }

        if ($this->routeIs('admin.images.bulk')) {
            return [
                'action' => 'required|in:delete,activate,deactivate,change_type',
                'image_ids' => 'required|array',
                'image_ids.*' => 'exists:images,id',
                'new_type' => 'required_if:action,change_type|string|max:50',
            ];
        }

        if ($this->routeIs('admin.images.upload')) {
            return [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'type' => 'required|string|max:50',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Loại ảnh là bắt buộc.',
            'type.max' => 'Loại ảnh không được vượt quá 50 ký tự.',

            'images.*.required' => 'Vui lòng chọn ít nhất một ảnh.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Chỉ chấp nhận jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Ảnh không được vượt quá 5MB.',

            'image.required' => 'Vui lòng chọn ảnh để tải lên.',
            'image.image' => 'Tệp phải là hình ảnh hợp lệ.',
            'image.mimes' => 'Ảnh chỉ chấp nhận jpeg, png, jpg, gif, webp.',
            'image.max' => 'Ảnh không được vượt quá 5MB.',

            'alt_text.max' => 'Mô tả alt không được vượt quá 255 ký tự.',

            'action.required' => 'Vui lòng chọn hành động.',
            'action.in' => 'Hành động không hợp lệ.',
            'image_ids.required' => 'Phải chọn ít nhất một ảnh.',
            'image_ids.*.exists' => 'Một hoặc nhiều ảnh không tồn tại.',
            'new_type.required_if' => 'Vui lòng nhập loại mới khi thay đổi loại ảnh.',
        ];
    }
}