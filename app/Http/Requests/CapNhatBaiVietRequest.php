<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatBaiVietRequest extends FormRequest
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
            'id' => 'required|integer|exists:bai_viets,id',
            'tieu_de' => 'required|string|max:255',
            'slug_tieu_de' => 'required|string|max:255|unique:bai_viets,slug_tieu_de,' . $this->id,
            'mo_ta' => 'nullable|string|max:500',
            'mo_ta_chi_tiet' => 'nullable|string',
            'id_chuyen_muc' => 'required|integer|exists:chuyen_mucs,id',
            'tinh_trang' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID bài viết là bắt buộc.',
            'id.exists' => 'Bài viết không tồn tại.',
            'tieu_de.required' => 'Tiêu đề là bắt buộc.',
            'slug_tieu_de.required' => 'Slug tiêu đề là bắt buộc.',
            'slug_tieu_de.unique' => 'Slug tiêu đề đã tồn tại.',
            'hinh_anh.image' => 'Hình ảnh phải là một tệp hình ảnh.',
            'hinh_anh.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc gif.',
            'hinh_anh.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'id_chuyen_muc.required' => 'ID chuyên mục là bắt buộc.',
            'id_chuyen_muc.exists' => 'ID chuyên mục không tồn tại.',
            'tinh_trang.required' => 'Tình trạng là bắt buộc.',
            'tinh_trang.boolean' => 'Tình trạng phải là true hoặc false.',
        ];
    }
}
