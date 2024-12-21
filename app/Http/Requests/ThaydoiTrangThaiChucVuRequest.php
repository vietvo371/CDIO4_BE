<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThaydoiTrangThaiChucVuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:chuc_vus,id',
            'tinh_trang' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID là bắt buộc.',
            'id.exists' => 'ID không tồn tại trong bảng Chức Vụ.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
        ];
    }
}
