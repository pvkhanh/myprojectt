<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeParam = $this->route('user'); // route model binding
        $id = is_object($routeParam) ? $routeParam->id : $routeParam;

        return [
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_.]+$/',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'password' => $this->isMethod('post')
                ? ['required', 'string', 'min:8', 'confirmed']
                : ['nullable', 'string', 'min:8', 'confirmed'],
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9]{7,20}$/', // cho phép số + quốc gia
            ],
            'role' => ['required', Rule::in(['admin', 'buyer'])],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'birthday' => ['nullable', 'date', 'before:today'],
            'bio' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'email_verified_at' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB
            'send_welcome_email' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username chỉ chứa chữ, số, dấu _ hoặc .',
            'phone.regex' => 'Số điện thoại không hợp lệ',
            'birthday.before' => 'Ngày sinh phải trước hôm nay',
            'password.min' => 'Mật khẩu tối thiểu 8 ký tự',
            'avatar.max' => 'Ảnh đại diện không được quá 2MB',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg hoặc png',
        ];
    }
}