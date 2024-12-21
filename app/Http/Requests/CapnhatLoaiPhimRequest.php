<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapnhatLoaiPhimRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'              => 'required|integer|exists:loai_phims,id',
            'ten_loai_phim'   => 'required|string|max:255',
            'slug_loai_phim'  => 'required|string|max:255|unique:loai_phims,slug_loai_phim,' . $this->id,
            'tinh_trang'      => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'id.required'              => 'ID loại phim là bắt buộc.',
            'id.integer'               => 'ID loại phim phải là số nguyên.',
            'id.exists'                => 'ID loại phim không tồn tại.',
            'ten_loai_phim.required'   => 'Tên loại phim là bắt buộc.',
            'ten_loai_phim.string'     => 'Tên loại phim phải là chuỗi ký tự.',
            'slug_loai_phim.required'  => 'Slug loại phim là bắt buộc.',
            'slug_loai_phim.string'    => 'Slug loại phim phải là chuỗi ký tự.',
            'slug_loai_phim.unique'    => 'Slug loại phim đã tồn tại.',
            'tinh_trang.required'      => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean'       => 'Tình trạng phải là kiểu boolean.',
        ];
    }
}
