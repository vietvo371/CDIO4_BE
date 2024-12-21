<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePhanQuyenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_chuc_nang' => 'required|exists:actions,id',
            'id_chuc_vu'   => 'required|exists:chuc_vus,id',
        ];
    }

    public function messages()
    {
        return [
            'id_chuc_nang.required' => 'Chức năng ID là bắt buộc.',
            'id_chuc_nang.exists'   => 'Chức năng ID không hợp lệ.',
            'id_chuc_vu.required'   => 'Chức vụ ID là bắt buộc.',
            'id_chuc_vu.exists'     => 'Chức vụ ID không hợp lệ.',
        ];
    }
}
