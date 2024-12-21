<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoBinhLuanBlogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'noi_dung'    => 'required|string|max:200',
            'id_bai_viet' => 'required|integer|exists:bai_viets,id'
        ];
    }

    public function messages()
    {
        return [
            'noi_dung.required'      => 'Nội dung không được để trống.',
            'id_bai_viet.required'   => 'ID bài viết là bắt buộc.',
            'id_bai_viet.exists'     => 'Bài viết không tồn tại.',
        ];
    }
}
