<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAdminRequest extends FormRequest
{

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
            'id' => 'required|exists:admins,id'
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID của admin là bắt buộc.',
            'id.exists'   => 'Admin không tồn tại.'
        ];
    }
}
