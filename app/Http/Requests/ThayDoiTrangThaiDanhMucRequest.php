<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThayDoiTrangThaiDanhMucRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'          => 'required|integer|exists:danh_muc_webs,id',
            'tinh_trang'  => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'id.required'         => 'ID danh mục là bắt buộc.',
            'id.integer'          => 'ID danh mục phải là số nguyên.',
            'id.exists'           => 'Danh mục không tồn tại.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean'  => 'Tình trạng phải là kiểu boolean.',
        ];
    }
}
