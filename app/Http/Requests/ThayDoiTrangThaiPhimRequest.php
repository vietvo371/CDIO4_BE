<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThayDoiTrangThaiPhimRequest extends FormRequest
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
            'id'            => 'required|exists:phims,id', // Ensure the 'id' exists in the 'phims' table
            'tinh_trang'    => 'required|boolean',         // Ensure 'tinh_trang' is a boolean (true/false)
        ];
    }
    public function messages()
    {
        return [
            'id.required'               => 'ID phim là bắt buộc.',
            'id.exists'                 => 'Phim không tồn tại.',
            'tinh_trang.required'       => 'Trạng thái là bắt buộc.',
            'tinh_trang.boolean'        => 'Trạng thái phải là true hoặc false.',
        ];
    }
}
