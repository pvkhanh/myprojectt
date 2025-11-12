<?php

// namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;

// class BannerRequest extends FormRequest
// {
//     public function authorize(): bool
//     {
//         return true;
//     }

//     public function rules(): array
//     {
//         $bannerId = $this->route('id') ?? null;

//         return [
//             'title' => 'required|string|max:255',
//             'url' => 'nullable|url|max:500',
//             'is_active' => 'boolean',
//             'position' => 'nullable|integer',
//             'start_at' => 'nullable|date',
//             'end_at' => 'nullable|date|after:start_at',
//             'type' => 'nullable|string|max:50',
//             'image' => $bannerId ? 'nullable|image|max:2048' : 'required|image|max:2048',
//         ];
//     }
// }




namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    /**
     * Xác định người dùng có được authorize hay không
     */
    public function authorize(): bool
    {
        return true; // Nếu bạn muốn kiểm tra quyền, có thể viết logic ở đây
    }

    /**
     * Quy tắc validate
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // 2MB
            'image_id' => 'nullable|exists:images,id',
            'type' => 'nullable|string|in:hero,sidebar,popup,footer',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'start_at' => 'nullable|date|before_or_equal:end_at',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ];
    }

    /**
     * Message tiếng Việt
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề banner là bắt buộc.',
            'title.string' => 'Tiêu đề banner phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề banner tối đa 255 ký tự.',

            'url.url' => 'URL không hợp lệ.',
            'url.max' => 'URL tối đa 255 ký tự.',

            'image.image' => 'Ảnh không hợp lệ.',
            'image.mimes' => 'Ảnh phải có định dạng JPG, JPEG, PNG hoặc GIF.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',

            'image_id.exists' => 'Ảnh đã chọn không tồn tại.',

            'type.in' => 'Loại banner không hợp lệ.',

            'position.integer' => 'Vị trí phải là số nguyên.',
            'position.min' => 'Vị trí phải >= 0.',

            'is_active.boolean' => 'Trạng thái không hợp lệ.',

            'start_at.date' => 'Ngày bắt đầu không hợp lệ.',
            'start_at.before_or_equal' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.',

            'end_at.date' => 'Ngày kết thúc không hợp lệ.',
            'end_at.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ];
    }
}
