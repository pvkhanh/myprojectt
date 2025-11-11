<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category') ?? null;

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'slug' => $id
                ? 'nullable|string|unique:categories,slug,' . $id
                : 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id' . ($id ? '|not_in:' . $id : ''),
            'position' => 'nullable|integer|min:1',
        ];
    }
}