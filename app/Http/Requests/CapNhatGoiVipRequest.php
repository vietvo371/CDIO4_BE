<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatGoiVipRequest extends FormRequest
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
            'id'            => 'required|exists:goi_vips,id',
            'ten_goi'       => 'required|string|max:255',
            'slug_goi_vip'  => 'required|string|max:255|unique:goi_vips,slug_goi_vip,' . $this->id,
            'thoi_han'      => 'required|integer|min:1',
            'tien_goc'      => 'required|numeric|min:0',
            'tien_sale'     => 'nullable|numeric|min:0|lte:tien_goc',
            'tinh_trang'    => 'required|boolean',
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'id.required'           => 'ID là bắt buộc.',
            'id.exists'             => 'Gói VIP không tồn tại.',
            'ten_goi.required'      => 'Tên gói là bắt buộc.',
            'slug_goi_vip.required' => 'Slug gói VIP là bắt buộc.',
            'slug_goi_vip.unique'   => 'Slug gói VIP đã tồn tại.',
            'thoi_han.required'     => 'Thời hạn là bắt buộc.',
            'tien_goc.required'     => 'Tiền gốc là bắt buộc.',
            'tien_sale.lte'         => 'Tiền giảm giá không được lớn hơn tiền gốc.',
            'tinh_trang.required'   => 'Tình trạng là bắt buộc.',
        ];
    }
}
