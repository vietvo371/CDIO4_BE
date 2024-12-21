<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaoPhimRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ten_phim'         => 'required|string|max:255',
            'slug_phim'        => 'required|string|max:255|unique:phims,slug_phim',
            'hinh_anh'         => 'required',
            'poster_img'       => 'required',
            'mo_ta'            => 'required|string',
            'thoi_gian_chieu'  => 'required|integer',
            'nam_san_xuat'     => 'required|integer|min:1900|max:' . date('Y'),
            'quoc_gia'         => 'required|string|max:255',
            'id_loai_phim'     => 'required|integer|exists:loai_phims,id',
            'dao_dien'         => 'required|string|max:255',
            'so_tap_phim'      => 'required|integer|min:1',
            'tinh_trang'       => 'required|in:0,1', // 0 là chưa hoàn thành, 1 là đã hoàn thành
            'ngon_ngu' => 'required|string|max:255',
            'trailer_url'      => 'nullable|url',
            'chat_luong'       => 'required|string|max:50',
            'the_loais'        => 'required|string', // Có thể xác thực thêm nếu cần
        ];
    }

    public function messages()
    {
        return [
            'ten_phim.required'         => 'Tên phim là bắt buộc.',
            'ten_phim.string'           => 'Tên phim phải là chuỗi ký tự.',
            'ten_phim.max'              => 'Tên phim không được vượt quá 255 ký tự.',

            'slug_phim.required'        => 'Slug phim là bắt buộc.',
            'slug_phim.string'          => 'Slug phim phải là chuỗi ký tự.',
            'slug_phim.max'             => 'Slug phim không được vượt quá 255 ký tự.',
            'slug_phim.unique'          => 'Slug phim đã tồn tại, vui lòng chọn slug khác.',

            'hinh_anh.required'         => 'Hình ảnh là bắt buộc.',
            'hinh_anh.url'              => 'Hình ảnh phải là một URL hợp lệ.',
            'hinh_anh.mimes'            => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc gif.',
            'hinh_anh.max'              => 'Kích thước hình ảnh không được vượt quá 2048KB.',

            'poster_img.required'       => 'Ảnh poster là bắt buộc.',
            'poster_img.url'            => 'Ảnh poster phải là một URL hợp lệ.',
            'poster_img.mimes'          => 'Ảnh poster phải có định dạng jpeg, png, jpg, hoặc gif.',
            'poster_img.max'            => 'Kích thước ảnh poster không được vượt quá 2048KB.',

            'mo_ta.required'            => 'Mô tả phim là bắt buộc.',
            'mo_ta.string'              => 'Mô tả phim phải là chuỗi ký tự.',

            'thoi_gian_chieu.required'  => 'Thời gian chiếu là bắt buộc.',
            'thoi_gian_chieu.integer'   => 'Thời gian chiếu phải là số nguyên.',

            'nam_san_xuat.required'     => 'Năm sản xuất là bắt buộc.',
            'nam_san_xuat.integer'      => 'Năm sản xuất phải là số nguyên.',
            'nam_san_xuat.min'          => 'Năm sản xuất phải lớn hơn hoặc bằng 1900.',
            'nam_san_xuat.max'          => 'Năm sản xuất không được lớn hơn năm hiện tại.',

            'quoc_gia.required'         => 'Quốc gia là bắt buộc.',
            'quoc_gia.string'           => 'Quốc gia phải là chuỗi ký tự.',
            'quoc_gia.max'              => 'Quốc gia không được vượt quá 255 ký tự.',

            'id_loai_phim.required'     => 'Loại phim là bắt buộc.',
            'id_loai_phim.integer'      => 'Loại phim phải là số nguyên.',
            'id_loai_phim.exists'       => 'Loại phim không tồn tại.',

            'dao_dien.required'         => 'Đạo diễn là bắt buộc.',
            'dao_dien.string'           => 'Đạo diễn phải là chuỗi ký tự.',
            'dao_dien.max'              => 'Đạo diễn không được vượt quá 255 ký tự.',

            'so_tap_phim.required'      => 'Số tập phim là bắt buộc.',
            'so_tap_phim.integer'       => 'Số tập phim phải là số nguyên.',
            'so_tap_phim.min'           => 'Số tập phim phải lớn hơn hoặc bằng 1.',

            'tinh_trang.required'       => 'Tình trạng là bắt buộc.',
            'tinh_trang.in'             => 'Tình trạng phải là 0 (chưa hoàn thành) hoặc 1 (đã hoàn thành).',

            'ngon_ngu.required'   => 'Công ty sản xuất phải là bắt buộc.',
            'ngon_ngu.string'   => 'Công ty sản xuất phải là chuỗi ký tự.',
            'ngon_ngu.max'      => 'Công ty sản xuất không được vượt quá 255 ký tự.',

            'trailer_url.url'           => 'Trailer URL phải là một URL hợp lệ.',

            'chat_luong.required'       => 'Chất lượng là bắt buộc.',
            'chat_luong.string'         => 'Chất lượng phải là chuỗi ký tự.',
            'chat_luong.max'            => 'Chất lượng không được vượt quá 50 ký tự.',

            'the_loais.required'        => 'Thể loại là bắt buộc.',
            'the_loais.string'          => 'Thể loại phải là chuỗi ký tự.',
        ];
    }
}
