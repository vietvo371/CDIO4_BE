<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThayDoiTrangThaiChuyenMucRequest extends FormRequest
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
            'id' => 'required|exists:chuyen_mucs,id',
            'tinh_trang' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID chuyên mục là bắt buộc.',
            'id.exists' => 'ID chuyên mục không tồn tại.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean' => 'Tình trạng phải là true hoặc false.',
        ];
    }
}
