<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeechTapPhimRequest extends FormRequest
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
            'slug' => 'required|string|exists:phims,slug_phim',
        ];
    }
    public function messages(): array
    {
        return [
            'slug.required' => 'slug phim phải tồn tại',
            'slug.string' => 'Slug phim là chuỗi',
            'slug.exists' => 'Phim chưa được thêm',
        ];
    }
}
