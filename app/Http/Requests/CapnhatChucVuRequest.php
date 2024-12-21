<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapnhatChucVuRequest extends FormRequest
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
            'id'            => 'required|exists:chuc_vus,id',
            'ten_chuc_vu'   => 'required|string|max:255',
            'slug_chuc_vu'  => 'required|string|max:255|unique:chuc_vus,slug_chuc_vu,' . $this->id,
            'tinh_trang'    => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'id.required'               => 'ID là bắt buộc.',
            'id.exists'                 => 'ID không tồn tại trong bảng Chức Vụ.',
            'ten_chuc_vu.required'      => 'Tên chức vụ là bắt buộc.',
            'slug_chuc_vu.required'     => 'Slug chức vụ là bắt buộc.',
            'slug_chuc_vu.unique'       => 'Slug chức vụ đã tồn tại.',
            'tinh_trang.required'       => 'Tình trạng là bắt buộc.',
        ];
    }
}
