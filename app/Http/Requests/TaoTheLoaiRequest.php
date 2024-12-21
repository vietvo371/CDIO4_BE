<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoTheLoaiRequest extends FormRequest
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
            'ten_the_loai' => 'required|string|max:255', // Validate the category name
            'slug_the_loai' => 'required|string|max:255|unique:the_loais,slug_the_loai', // Ensure unique slug
            'tinh_trang' => 'required|in:0,1', // Validate that status is either 0 (inactive) or 1 (active)
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
            'ten_the_loai.string' => 'Tên thể loại phải là chuỗi ký tự.',
            'ten_the_loai.max' => 'Tên thể loại không được vượt quá 255 ký tự.',
            'slug_the_loai.required' => 'Slug thể loại là bắt buộc.',
            'slug_the_loai.string' => 'Slug thể loại phải là chuỗi ký tự.',
            'slug_the_loai.max' => 'Slug thể loại không được vượt quá 255 ký tự.',
            'slug_the_loai.unique' => 'Slug thể loại đã tồn tại.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
            'tinh_trang.in' => 'Tình trạng phải là "0" (inactive) hoặc "1" (active).',
        ];
    }
}
