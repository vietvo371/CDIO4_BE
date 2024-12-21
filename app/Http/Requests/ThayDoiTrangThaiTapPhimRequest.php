<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThayDoiTrangThaiTapPhimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'id' => 'required|exists:tap_phims,id',  // Ensure the episode exists
            'tinh_trang' => 'required|boolean',  // Validate that the status is a boolean
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID tập phim là bắt buộc.',
            'id.exists' => 'Tập phim không tồn tại.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean' => 'Tình trạng phải là true hoặc false.',
        ];
    }
}
