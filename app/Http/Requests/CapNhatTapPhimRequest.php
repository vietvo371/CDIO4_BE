<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatTapPhimRequest extends FormRequest
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
            'id' => 'required|exists:tap_phims,id',  // Ensure the episode exists
            'url' => 'required|url',  // Validate URL format
            'tinh_trang' => 'required|boolean',  // Validate status (active or inactive)
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID tập phim là bắt buộc.',
            'id.exists' => 'Tập phim không tồn tại.',
            'url.required' => 'URL là bắt buộc.',
            'url.url' => 'URL không hợp lệ.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean' => 'Tình trạng phải là 0 hoặc 1',
        ];
    }
}
