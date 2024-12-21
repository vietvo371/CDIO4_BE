<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKhachHangRequest extends FormRequest
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
            'ho_va_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'required|numeric|digits_between:10,15',
            // 'email'     => 'required|email|unique:khach_hangs,email,' . $this->id,
            // 'avatar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'ho_va_ten.required' => 'Vui lòng nhập họ và tên.',
            'so_dien_thoai.required' => 'Vui lòng nhập số đien thoai.',
            'so_dien_thoai.numeric' => 'só điện thoại phải được chúa số.',
            'so_dien_thoai.digits_between' => 'só điện thoại phải có tu 10 đến 15 chữ số.',
        ];
    }
}
