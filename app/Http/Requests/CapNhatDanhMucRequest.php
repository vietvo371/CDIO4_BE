<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatDanhMucRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ten_danh_muc'     => 'required|string|max:255',
            'slug_danh_muc'    => 'required|string|max:255|unique:danh_muc_webs,slug_danh_muc,' . $this->id,
            'id_danh_muc_cha'  => 'nullable|integer|exists:danh_muc_webs,id',
            'tinh_trang'       => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'ten_danh_muc.required'    => 'Tên danh mục là bắt buộc.',
            'ten_danh_muc.string'      => 'Tên danh mục phải là chuỗi ký tự.',
            'slug_danh_muc.required'   => 'Slug danh mục là bắt buộc.',
            'slug_danh_muc.unique'     => 'Slug danh mục đã tồn tại.',
            'id_danh_muc_cha.integer'  => 'ID danh mục cha phải là số nguyên.',
            'id_danh_muc_cha.exists'   => 'Danh mục cha không tồn tại.',
            'tinh_trang.required'      => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean'       => 'Tình trạng phải là kiểu boolean.',
        ];
    }
}
