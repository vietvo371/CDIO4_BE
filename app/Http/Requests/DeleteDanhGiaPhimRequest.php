<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDanhGiaPhimRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Bạn có thể thực hiện xác thực ở đây nếu cần
    }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:binh_luan_phims,id', // ID bình luận phải tồn tại
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID bình luận là bắt buộc.',
            'id.exists' => 'Bình luận không tồn tại.',
        ];
    }
}
