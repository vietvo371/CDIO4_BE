<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeechReuqest extends FormRequest
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
            'slug' => 'required|string|unique:phims,slug_phim',
            // Add other fields and their validation rules as needed
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.required' => 'slug phim phải tồn tại',
            'slug.string' => 'Slug phim là chuỗi',
            'slug.unique' => 'Phim đã tồn tại',
        ];
    }
}
