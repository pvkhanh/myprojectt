<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


// ============ UPDATE PROFILE REQUEST ============
class UpdateProfileRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth('api')->id();

        return [
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'username' => 'nullable|string|max:50|unique:users,username,' . $userId,
            'email' => 'nullable|email|max:100|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:15',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date|before:today',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'Username đã tồn tại',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'gender.in' => 'Giới tính không hợp lệ',
            'birthday.before' => 'Ngày sinh phải trước ngày hôm nay',
            'avatar.image' => 'File phải là ảnh',
            'avatar.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif',
            'avatar.max' => 'Ảnh không được vượt quá 2MB',
        ];
    }
}