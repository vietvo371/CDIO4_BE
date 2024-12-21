<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoKhachHangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ho_va_ten'      => 'required|string|max:255',
            'email'          => 'required|email|unique:khach_hangs,email',
            'so_dien_thoai'  => 'required|numeric|digits_between:10,15',
            'password'       => 'required|string|min:6',
            'avatar'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // ảnh có thể null, chấp nhận JPG, JPEG, PNG, tối đa 2MB
        ];
    }
    public function messages()
    {
        return [
            'ho_va_ten.required'     => 'Vui lòng nhập họ và tên.',
            'email.required'         => 'Vui lòng nhập email.',
            'email.email'            => 'Email không hợp lệ.',
            'email.unique'           => 'Email đã tồn tại.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'so_dien_thoai.numeric'  => 'Số điện thoại không hợp lệ.',
            'password.required'      => 'Vui lòng nhập mật khẩu.',
            'password.min'           => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'avatar.image'           => 'File tải lên phải là hình ảnh.',
            'avatar.mimes'           => 'Ảnh phải có định dạng JPG, JPEG, hoặc PNG.',
            'avatar.max'             => 'Ảnh tối đa 2MB.',
        ];
    }
}
