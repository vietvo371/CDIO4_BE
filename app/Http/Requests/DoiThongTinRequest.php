<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoiThongTinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_va_ten'		        =>  'required|min:6|max:50',
            'so_dien_thoai'         =>  'required|digits_between:10,12',
        ];
    }
    public function messages()
    {
        return [
            'ho_va_ten.*'      =>  ' Họ và tên phải trên 3 ký tự',
            'so_dien_thoai.*'  =>  'Số điện thoại từ 10 đến 12 số',

        ];
    }
}
