<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoiPassRequest extends FormRequest
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
            'old_pass'          =>  'required|min:6|max:30',
            'new_pass'          =>  'required|min:6|max:30',
            're_new_pass'       =>  'required|same:new_pass',
        ];
    }
    public function messages()
    {
        return [
            'old_pass.*'          =>  'Mật khẩu yêu cầu phải từ 6 đến 30 ký tự',
            'new_pass.*'          =>  'Mật khẩu mới yêu cầu phải từ 6 đến 30 ký tự',
            're_new_pass.*'    =>  'Mật khẩu mới không trùng khớp',
        ];
    }
}
