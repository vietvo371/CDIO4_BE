<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatTheLoaiRequest extends FormRequest
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
            'ten_the_loai' => 'required|string|max:255',
            'slug_the_loai' => 'required|string|max:255|unique:the_loais,slug_the_loai,' . $this->id, // Exclude the current record
            'tinh_trang' => 'required|in:0,1', // Ensure status is either 0 or 1
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
            'ten_the_loai.required' => 'Tên thể loại là bắt buộc.',
            'slug_the_loai.required' => 'Slug thể loại là bắt buộc.',
            'slug_the_loai.unique' => 'Slug thể loại đã tồn tại.',
            'tinh_trang.required' => 'Trạng thái là bắt buộc.',
            'tinh_trang.in' => 'Trạng thái phải là 0 hoặc 1.',
        ];
    }
}
