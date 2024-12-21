<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XoaBinhLuanBlogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:binh_luan_bai_viets,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID bình luận là bắt buộc.',
            'id.exists'   => 'Bình luận không tồn tại.',
        ];
    }
}
