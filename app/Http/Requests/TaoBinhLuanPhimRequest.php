<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoBinhLuanPhimRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'noi_dung' => 'required|string|max:500',
            'id_tap_phim' => 'required|exists:tap_phims,id',
        ];
    }

    public function messages()
    {
        return [
            'noi_dung.required' => 'Nội dung bình luận là bắt buộc.',
            'noi_dung.max' => 'Nội dung bình luận không được vượt quá 500 ký tự.',
            'id_tap_phim.required' => 'ID tập phim là bắt buộc.',
            'id_tap_phim.exists' => 'ID tập phim không hợp lệ.',
        ];
    }
}
