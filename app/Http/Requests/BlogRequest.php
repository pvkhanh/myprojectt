<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\BlogStatus;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $blogId = $this->route('id') ?? null;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blogs,slug' . ($blogId ? ',' . $blogId : ''),
            'content' => 'required|string',
            'status' => 'required|in:' . implode(',', BlogStatus::values()),
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'primary_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }
}