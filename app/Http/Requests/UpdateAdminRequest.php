<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'            => 'required|exists:admin_animes,id',
            'ho_va_ten'     => 'required|string|max:255',
            'id_chuc_vu'    => 'required|integer|exists:chuc_vus,id',
            'so_dien_thoai' => 'required|numeric|digits_between:10,15',
            'tinh_trang'    => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'id.required'            => 'ID của admin là bắt buộc.',
            'id.exists'              => 'Admin không tồn tại.',
            'email.required'         => 'Email là bắt buộc.',
            'email.email'            => 'Email không hợp lệ.',
            'email.unique'           => 'Email đã tồn tại.',
            'ho_va_ten.required'     => 'Họ và tên là bắt buộc.',
            'hinh_anh.image'         => 'File ảnh không hợp lệ.',
            'hinh_anh.mimes'         => 'Ảnh phải có định dạng jpg, jpeg, hoặc png.',
            'hinh_anh.max'           => 'Dung lượng ảnh tối đa là 2MB.',
            'id_chuc_vu.required'    => 'ID chức vụ là bắt buộc.',
            'id_chuc_vu.integer'     => 'ID chức vụ phải là số nguyên.',
            'id_chuc_vu.exists'      => 'ID chức vụ không tồn tại.',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
            'so_dien_thoai.numeric'  => 'Số điện thoại chỉ được chứa số.',
            'so_dien_thoai.digits_between' => 'Số điện thoại phải có từ 10 đến 15 chữ số.',
            'tinh_trang.required'    => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean'     => 'Tình trạng phải là đúng hoặc sai.',
        ];
    }
}
