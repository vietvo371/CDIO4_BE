<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDanhGiaPhimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        return [
            'noi_dung' => 'required|string|max:1000', // Tối đa 1000 ký tự
            'id_phim' => 'required|integer|exists:phims,id', // Kiểm tra phim tồn tại
            'so_sao' => 'required|integer|between:1,5', // Đánh giá từ 1 đến 5 sao
        ];
    }

    public function messages()
    {
        return [
            'noi_dung.required' => 'Nội dung không được để trống.',
            'id_phim.required' => 'ID phim là bắt buộc.',
            'id_phim.exists' => 'Phim không tồn tại.',
            'so_sao.required' => 'Số sao là bắt buộc.',
            'so_sao.between' => 'Số sao phải từ 1 đến 5.',
        ];
    }
}
