<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XoaBinhLuanPhimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:binh_luat_tap_phims,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID bình luận là bắt buộc.',
            'id.exists' => 'ID bình luận không hợp lệ.',
        ];
    }
}
