<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangKyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'		        =>  'required|email|unique:khach_hangs,email',
            'password'          =>  'required|min:6|max:30',
            // 're_password'	    =>  'required|same:password',
            // 'so_dien_thoai'     =>  'required|digits_between:10,12',
            // 'ngay_sinh'         =>  'required|date',
            // 'dia_chi'           =>  'required|min:7|max:100',
            'ho_va_ten'         =>  'required|min:4|max:100',
        ];
    }

    public function messages()
    {
        return [
            'email.required'	  =>  'Email yêu cầu phải nhập',
            'email.email'	      =>  'Email không đúng định dạng',
            'email.unique'	      =>  'Email đã tồn tại trong hệ thống',
            'password.*'          =>  'Password yêu cầu phải từ 6 đến 30 ký tự',
            // 're_password.*'       =>  'Hai mật khẩu không trùng kìa ku!',
            // 'so_dien_thoai.*'     =>  'Số điện thoại không được để trống và từ 10 đến 12 số',
            // 'ngay_sinh.*'         =>  'Ngày sinh không được để trống và bắt buộc phải là định dạng ngày',
            // 'dia_chi.*'           =>  'Địa chỉ không được để trống và lớn hơn 7 kí tự',
            'ho_va_ten.*'         =>  'Họ và tên không được để trống và lớn hơn 4 kí tự',
        ];
    }
}
