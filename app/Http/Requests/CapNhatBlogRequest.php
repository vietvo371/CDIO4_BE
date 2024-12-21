<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatBlogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'           => 'required|integer|exists:binh_luan_bai_viets,id',
            'id_bai_viet'  => 'required|integer|exists:bai_viets,id',
            'noi_dung'     => 'required|string|max:200',
        ];
    }

    public function messages()
    {
        return [
            'id.required'            => 'ID bình luận là bắt buộc.',
            'id.exists'              => 'Bình luận không tồn tại.',
            'id_bai_viet.required'   => 'ID bài viết là bắt buộc.',
            'id_bai_viet.exists'     => 'Bài viết không tồn tại.',
            'noi_dung.required'      => 'Nội dung không được để trống.',
        ];
    }
}
