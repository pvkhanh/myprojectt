<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bannerId = $this->route('id') ?? null;

        return [
            'title' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'position' => 'nullable|integer',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'type' => 'nullable|string|max:50',
            'image' => $bannerId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ];
    }
}