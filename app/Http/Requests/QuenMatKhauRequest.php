<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuenMatKhauRequest extends FormRequest
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
            'email'		        =>  'required|email|unique:khach_hangs,email',
        ];
    }
    public function messages()
    {
        return [
            'email.required'      =>  'email không được để trống',
            'email.email'      =>  'email không đúng định dang',
        ];
    }
}
