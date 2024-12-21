<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleAdminStatusRequest extends FormRequest
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
            'id'         => 'required|exists:admin_animes,id',
            'tinh_trang' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'id.required'         => 'ID của admin là bắt buộc.',
            'id.exists'           => 'Admin không tồn tại.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean'  => 'Tình trạng phải là đúng hoặc sai.',
        ];
    }
}
