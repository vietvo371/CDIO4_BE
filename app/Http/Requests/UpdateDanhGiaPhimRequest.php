<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDanhGiaPhimRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Bạn có thể thực hiện xác thực ở đây nếu cần
    }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:binh_luan_phims,id', // ID bình luận phải tồn tại
            'noi_dung' => 'required|string|max:1000',
            'id_phim' => 'required|integer|exists:phims,id',
            'so_sao' => 'required|integer|between:1,5',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID bình luận là bắt buộc.',
            'id.exists' => 'Bình luận không tồn tại.',
            'noi_dung.required' => 'Nội dung không được để trống.',
            'id_phim.required' => 'ID phim là bắt buộc.',
            'id_phim.exists' => 'Phim không tồn tại.',
            'so_sao.required' => 'Số sao là bắt buộc.',
            'so_sao.between' => 'Số sao phải từ 1 đến 5.',
        ];
    }
}
