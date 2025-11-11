<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ReviewStatus;

class ProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->isMethod('post') || $this->isMethod('put')) {
            $rules = [
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:10|max:1000',
                'status' => 'required|in:' . implode(',', array_column(ReviewStatus::cases(), 'value')),
            ];
        }

        if ($this->input('action') === 'bulk') {
            $rules = [
                'ids' => 'required|array',
                'action' => 'required|in:approve,reject,delete',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Vui lòng chọn số sao đánh giá',
            'rating.min' => 'Đánh giá tối thiểu là 1 sao',
            'rating.max' => 'Đánh giá tối đa là 5 sao',
            'comment.required' => 'Vui lòng nhập nội dung đánh giá',
            'comment.min' => 'Nội dung đánh giá phải có ít nhất 10 ký tự',
            'comment.max' => 'Nội dung đánh giá không được quá 1000 ký tự',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
