<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangNhapRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'                =>  'required|exists:khach_hangs,email',
            'password'                =>  'required|min:6|max:30',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Vui lòng nhập email',
            'email.exists' => 'Tài khoản không tồn tại',
            'password.*' => 'Vui lòng nhập mật khẩu'
        ];
    }
}
