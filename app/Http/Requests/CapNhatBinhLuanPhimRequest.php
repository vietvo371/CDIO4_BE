<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatBinhLuanPhimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:binh_luat_tap_phims,id',
            'noi_dung' => 'required|string|max:100',
            'id_tap_phim' => 'required|exists:tap_phims,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID bình luận là bắt buộc.',
            'id.exists' => 'ID bình luận không hợp lệ.',
            'noi_dung.required' => 'Nội dung bình luận là bắt buộc.',
            'noi_dung.max' => 'Nội dung bình luận không được vượt quá 100 ký tự.',
            'id_tap_phim.required' => 'ID tập phim là bắt buộc.',
            'id_tap_phim.exists' => 'ID tập phim không hợp lệ.',
        ];
    }
}
