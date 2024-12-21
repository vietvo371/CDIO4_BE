<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThaydoiTrangThaiLoaiPhimRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:loai_phims,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID loại phim là bắt buộc.',
            'id.integer'  => 'ID loại phim phải là số nguyên.',
            'id.exists'   => 'ID loại phim không tồn tại.',
        ];
    }
}
