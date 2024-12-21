<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XoaTheLoaiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // You may implement authorization logic here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:the_loais,id', // Ensure the category exists
        ];
    }

    /**
     * Get the custom messages for the validator.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => 'ID thể loại là bắt buộc.',
            'id.exists' => 'Thể loại không tồn tại.',
        ];
    }
}
