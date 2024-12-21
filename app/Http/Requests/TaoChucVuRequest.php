<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoChucVuRequest extends FormRequest
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
            'ten_chuc_vu'   => 'required|string|max:255',
            'slug_chuc_vu'  => 'required|string|max:255|unique:chuc_vus,slug_chuc_vu',
            'tinh_trang'    => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'ten_chuc_vu.required'   => 'Tên chức vụ là bắt buộc.',
            'ten_chuc_vu.string'     => 'Tên chức vụ phải là một chuỗi.',
            'ten_chuc_vu.max'        => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'slug_chuc_vu.required'  => 'Slug chức vụ là bắt buộc.',
            'slug_chuc_vu.string'    => 'Slug chức vụ phải là một chuỗi.',
            'slug_chuc_vu.max'       => 'Slug chức vụ không được vượt quá 255 ký tự.',
            'slug_chuc_vu.unique'    => 'Slug chức vụ đã tồn tại.',
            'tinh_trang.required'    => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean'     => 'Tình trạng phải là giá trị boolean.',
        ];
    }
}
