<?php

use App\Http\Controllers\AdminAnimeController;
use App\Http\Controllers\BaiVietController;
use App\Http\Controllers\BinhLuanBaiVietController;
use App\Http\Controllers\BinhLuanPhimController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\ChuyenMucController;
use App\Http\Controllers\DanhMucWebController;
use App\Http\Controllers\DienVienController;
use App\Http\Controllers\GoiVipController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\LoaiPhimController;
use App\Http\Controllers\PhanQuyenController;
use App\Http\Controllers\PhimController;
use App\Http\Controllers\TacGiaController;
use App\Http\Controllers\TapPhimController;
use App\Http\Controllers\TheLoaiController;
use App\Http\Controllers\ThongKeController;
use App\Http\Controllers\YeuThichController;
use App\Models\AdminAnime;
use App\Models\BinhLuanPhim;
use App\Models\GoiVip;
use App\Models\Phim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




///      ADMIN
Route::post('/login', [AdminAnimeController::class, 'login']);
Route::post('/logout', [AdminAnimeController::class, 'logout']);
Route::post('/register', [AdminAnimeController::class, 'register']);
Route::post('/check', [AdminAnimeController::class, 'check']);
Route::delete('/thong-tin-xoa/{id}', [AdminAnimeController::class, 'xoatoken']);
/// Khach hàng
Route::post('/khach-hang/login', [KhachHangController::class, 'login']);
Route::post('/khach-hang/register', [KhachHangController::class, 'register']);
Route::post('/kiem-tra-quen-hash-pass', [KhachHangController::class, 'kiemTraQuenMK']);
Route::post('/kiem-tra-hash-kich-hoat', [KhachHangController::class, 'kiemTraHashLogin']);
Route::post('/gui-mail-kich-hoat', [KhachHangController::class, 'kichHoatTK']);
Route::post('/dat-lai-mat-khau', [KhachHangController::class, 'datLaiMK']);
Route::post('/quen-mat-khau', [KhachHangController::class, 'quenMK']);
Route::post('/khach-hang/check', [KhachHangController::class, 'check']);
Route::delete('/khach-hang/thong-tin-xoa/{id}', [KhachHangController::class, 'xoatoken']);



Route::group(['prefix'  =>  '/admin', 'middleware' => 'adminAnime'], function () {
    //quản lý admin
    Route::group(['prefix'  =>  '/admin'], function () {
        //  Tài Khoản Admin
        Route::get('/lay-du-lieu', [AdminAnimeController::class, 'getData']);
        Route::post('/thong-tin-tao', [AdminAnimeController::class, 'taoAdmin']);
        Route::delete('/thong-tin-xoa/{id}', [AdminAnimeController::class, 'xoaAdmin']);
        Route::post('/thong-tin-cap-nhat', [AdminAnimeController::class, 'capnhatAdmin']);
        Route::put('/thong-tin-thay-doi-trang-thai', [AdminAnimeController::class, 'thaydoiTrangThaiAdmin']);
        Route::post('/thong-tin-tim', [AdminAnimeController::class, 'timAdmin']);
        Route::get('/lay-du-lieu-profile', [AdminAnimeController::class, 'getDataProfile']);
        // Route::put('/doi-thong-tin', [AdminAnimeController::class, 'doiThongTin']);
        Route::put('/check-email', [AdminAnimeController::class, 'checkEmail']);
        Route::put('/doi-mat-khau', [AdminAnimeController::class, 'doiPass']);

        Route::put('/doi-mat-khau-profile', [AdminAnimeController::class, 'doiPassProfile']);
        Route::post('/doi-avatar', [AdminAnimeController::class, 'doiAvatar']);
        Route::put('/doi-thong-tin', [AdminAnimeController::class, 'doiThongTin']);
        Route::get('/lay-du-lieu-profile', [AdminAnimeController::class, 'getDataProfile']);
    });
    //quản lý Khách hàng

    Route::group(['prefix'  =>  '/khach-hang'], function () {
        //  Tài Khoản Khách Hàng
        Route::get('/lay-du-lieu', [KhachHangController::class, 'getData']);
        Route::post('/thong-tin-tao', [KhachHangController::class, 'taoKhachHang']);
        Route::delete('/thong-tin-xoa/{id}', [KhachHangController::class, 'xoaKhachHang']);
        Route::post('/thong-tin-cap-nhat', [KhachHangController::class, 'capnhatKhachHang']);
        Route::post('/thong-tin-thay-doi-trang-thai', [KhachHangController::class, 'thaydoiTrangThaiKhachHang']);
        Route::post('/kich-hoat-tai-khoan', [KhachHangController::class, 'thaydoiKichHoatKhachHang']);
        Route::post('/thong-tin-tim', [KhachHangController::class, 'timKhachHang']);
        Route::put('/doi-mat-khau', [KhachHangController::class, 'doiPass']);
        Route::put('/doi-thong-tin', [KhachHangController::class, 'doiThongTin']);
        Route::post('/doi-avatar', [KhachHangController::class, 'doiAvatar']);
        Route::get('/lay-du-lieu-profile', [KhachHangController::class, 'getDataProfile']);
    });
    //quản lý Chức vụ

    Route::group(['prefix'  =>  '/chuc-vu'], function () {
        // The Loai Chức vụ
        Route::get('/lay-du-lieu', [ChucVuController::class, 'getData']);
        Route::post('/thong-tin-tao', [ChucVuController::class, 'taoChucVu']);
        Route::delete('/thong-tin-xoa/{id}', [ChucVuController::class, 'xoaChucVu']);
        Route::put('/thong-tin-cap-nhat', [ChucVuController::class, 'capnhatChucVu']);
        Route::put('/thong-tin-thay-doi-trang-thai', [ChucVuController::class, 'thaydoiTrangThaiChucVu']);
        Route::post('/thong-tin-tim', [ChucVuController::class, 'timChucVu']);
        Route::post('/kiem-tra-slug', [ChucVuController::class, 'kiemTraSlugChucVu']);
        Route::post('/kiem-tra-slug-update', [ChucVuController::class, 'kiemTraSlugChucVuUpdate']);
    });
    //quản lý Phân quyền

    Route::group(['prefix'  =>  '/phan-quyen'], function () {
        Route::get('/lay-du-lieu', [PhanQuyenController::class, 'getDataPhanQuyen']);
        Route::post('/create', [PhanQuyenController::class, 'createPhanQuyen']);
        Route::post('/get-chuc-nang', [PhanQuyenController::class, 'getChucNang']);
        Route::delete('/xoa-phan-quyen/{id}', [PhanQuyenController::class, 'xoaPhanQuyen']);
    });
    //quản lý Diễn Viên
    Route::group(['prefix'  =>  '/dien-vien'], function () {

        Route::get('/lay-du-lieu', [DienVienController::class, 'getData']);
        Route::post('/thong-tin-tao', [DienVienController::class, 'taoDienVien']);
        Route::delete('/thong-tin-xoa/{id}', [DienVienController::class, 'xoaDienVien']);
        Route::post('/thong-tin-cap-nhat', [DienVienController::class, 'capnhatDienVien']);
        Route::put('/thong-tin-thay-doi-trang-thai', [DienVienController::class, 'thaydoiTrangThaiDienVien']);
        Route::post('/thong-tin-tim', [DienVienController::class, 'timDienVien']);
        Route::post('/kiem-tra-slug', [DienVienController::class, 'kiemTraSlugDienVien']);
        Route::post('/kiem-tra-slug-update', [DienVienController::class, 'kiemTraSlugDienVienUpdate']);
    });
    //quản lý Phim
    Route::group(['prefix'  =>  '/phim'], function () {

        Route::get('/lay-du-lieu', [PhimController::class, 'getData']);
        Route::get('/lay-du-lieu-theo-tap', [PhimController::class, 'getDataTheoTap']);
        Route::post('/thong-tin-tim-theo-tap', [PhimController::class, 'timPhimTheoTap']);
        Route::post('/thong-tin-tao', [PhimController::class, 'taoPhim']);
        Route::delete('/thong-tin-xoa/{id}', [PhimController::class, 'xoaPhim']);
        Route::post('/thong-tin-cap-nhat', [PhimController::class, 'capnhatPhim']);
        Route::put('/thong-tin-thay-doi-trang-thai', [PhimController::class, 'thaydoiTrangThaiPhim']);
        Route::post('/thong-tin-tim', [PhimController::class, 'timPhim']);
        Route::post('/kiem-tra-slug', [PhimController::class, 'kiemTraSlugPhim']);
        Route::post('/kiem-tra-slug-update', [PhimController::class, 'kiemTraSlugPhimUpdate']);
    });
    //quản lý Tập phim
    Route::group(['prefix'  =>  '/tap-phim'], function () {
        //  Tập Phim
        Route::post('/lay-du-lieu', [TapPhimController::class, 'getData']);
        Route::post('/thong-tin-tao', [TapPhimController::class, 'taoTapPhim']);
        Route::delete('/thong-tin-xoa/{id}', [TapPhimController::class, 'xoaTapPhim']);
        Route::delete('/thong-tin-xoa-all-tap/{id}', [TapPhimController::class, 'xoaAllTapPhim']);
        Route::put('/thong-tin-cap-nhat', [TapPhimController::class, 'capnhatTapPhim']);
        Route::put('/thong-tin-thay-doi-trang-thai', [TapPhimController::class, 'thaydoiTrangThaiTapPhim']);
        Route::post('/thong-tin-tim', [TapPhimController::class, 'timTapPhim']);
        Route::post('/kiem-tra-slug', [TapPhimController::class, 'kiemTraSlugTapPhim']);
        Route::post('/kiem-tra-slug-update', [TapPhimController::class, 'kiemTraSlugTapPhimUpdate']);
        Route::post('/lay-ten-phim', [TapPhimController::class, 'layTenPhim']);
        Route::post('/lay-ten-phim-update', [TapPhimController::class, 'layTenPhimUpdate']);
    });
    //quản lý Thể Loại
    Route::group(['prefix'  =>  '/the-loai'], function () {

        // The Loai Phim
        Route::get('/lay-du-lieu', [TheLoaiController::class, 'getData']);
        Route::post('/thong-tin-tao', [TheLoaiController::class, 'taoTheLoai']);
        Route::delete('/thong-tin-xoa/{id}', [TheLoaiController::class, 'xoaTheLoai']);
        Route::put('/thong-tin-cap-nhat', [TheLoaiController::class, 'capnhatTheLoai']);
        Route::put('/thong-tin-thay-doi-trang-thai', [TheLoaiController::class, 'thaydoiTrangThaiTheLoai']);
        Route::post('/thong-tin-tim', [TheLoaiController::class, 'timTheLoai']);
        Route::post('/kiem-tra-slug', [TheLoaiController::class, 'kiemTraSlugTheLoai']);
        Route::post('/kiem-tra-slug-update', [TheLoaiController::class, 'kiemTraSlugTheLoaiUpdate']);
    });
    //quản lý Loại Phim
    Route::group(['prefix'  =>  '/loai-phim'], function () {
        Route::get('/lay-du-lieu', [LoaiPhimController::class, 'getData']);
        Route::post('/thong-tin-tao', [LoaiPhimController::class, 'taoLoaiPhim']);
        Route::delete('/thong-tin-xoa/{id}', [LoaiPhimController::class, 'xoaLoaiPhim']);
        Route::put('/thong-tin-cap-nhat', [LoaiPhimController::class, 'capnhatLoaiPhim']);
        Route::put('/thong-tin-thay-doi-trang-thai', [LoaiPhimController::class, 'thaydoiTrangThaiLoaiPhim']);
        Route::post('/thong-tin-tim', [LoaiPhimController::class, 'timLoaiPhim']);
        Route::post('/kiem-tra-slug', [LoaiPhimController::class, 'kiemTraSlugLoaiPhim']);
        Route::post('/kiem-tra-slug-update', [LoaiPhimController::class, 'kiemTraSlugLoaiPhimUpdate']);
    });
    // Danh Mục
    Route::group(['prefix'  =>  '/danh-muc'], function () {
        Route::get('/lay-du-lieu', [DanhMucWebController::class, 'getData']);
        Route::post('/thong-tin-tao', [DanhMucWebController::class, 'taoDanhMuc']);
        Route::delete('/thong-tin-xoa/{id}', [DanhMucWebController::class, 'xoaDanhMuc']);
        Route::put('/thong-tin-cap-nhat', [DanhMucWebController::class, 'capnhatDanhMuc']);
        Route::put('/thong-tin-thay-doi-trang-thai', [DanhMucWebController::class, 'thaydoiTrangThaiDanhMuc']);
        Route::post('/thong-tin-tim', [DanhMucWebController::class, 'timDanhMuc']);
        Route::post('/kiem-tra-slug', [DanhMucWebController::class, 'kiemTraSlugDanhMuc']);
        Route::post('/kiem-tra-slug-update', [DanhMucWebController::class, 'kiemTraSlugDanhMucUpdate']);
    });
    //quản lý Tác giả
    Route::group(['prefix'  =>  '/tac-gia'], function () {

        // Tác Giả
        Route::get('/lay-du-lieu', [TacGiaController::class, 'getData']);
        Route::post('/thong-tin-tao', [TacGiaController::class, 'taoTacGia']);
        Route::delete('/thong-tin-xoa/{id}', [TacGiaController::class, 'xoaTacGia']);
        Route::put('/thong-tin-cap-nhat', [TacGiaController::class, 'capnhatTacGia']);
        Route::put('/thong-tin-thay-doi-trang-thai', [TacGiaController::class, 'thaydoiTrangThaiTacGia']);
        Route::post('/thong-tin-tim', [TacGiaController::class, 'timTacGia']);
        Route::post('/kiem-tra-slug', [TacGiaController::class, 'kiemTraSlugTacGia']);
        Route::post('/kiem-tra-slug-update', [TacGiaController::class, 'kiemTraSlugTacGiaUpdate']);
    });
    //quản lý Bài Viết
    Route::group(['prefix'  =>  '/bai-viet'], function () {
        // Bài Viết Blog
        Route::get('/lay-du-lieu', [BaiVietController::class, 'getData']);
        Route::post('/thong-tin-tao', [BaiVietController::class, 'taoBaiViet']);
        Route::delete('/thong-tin-xoa/{id}', [BaiVietController::class, 'xoaBaiViet']);
        Route::post('/thong-tin-cap-nhat', [BaiVietController::class, 'capnhatBaiViet']);
        Route::put('/thong-tin-thay-doi-trang-thai', [BaiVietController::class, 'thaydoiTrangThaiBaiViet']);
        Route::post('/thong-tin-tim', [BaiVietController::class, 'timBaiViet']);
        Route::post('/kiem-tra-slug', [BaiVietController::class, 'kiemTraSlugBaiViet']);
        Route::post('/kiem-tra-slug-update', [BaiVietController::class, 'kiemTraSlugBaiVietUpdate']);
    });
    //quản lý Chuyên Mục Bài Viết
    Route::group(['prefix'  =>  '/chuyen-muc'], function () {
        // Chuyên Mục Blog
        Route::get('/lay-du-lieu', [ChuyenMucController::class, 'getData']);
        Route::post('/thong-tin-tao', [ChuyenMucController::class, 'taoChuyenMuc']);
        Route::delete('/thong-tin-xoa/{id}', [ChuyenMucController::class, 'xoaChuyenMuc']);
        Route::put('/thong-tin-cap-nhat', [ChuyenMucController::class, 'capnhatChuyenMuc']);
        Route::put('/thong-tin-thay-doi-trang-thai', [ChuyenMucController::class, 'thaydoiTrangThaiChuyenMuc']);
        Route::post('/thong-tin-tim', [ChuyenMucController::class, 'timChuyenMuc']);
        Route::post('/kiem-tra-slug', [ChuyenMucController::class, 'kiemTraSlugChuyenMuc']);
        Route::post('/kiem-tra-slug-update', [ChuyenMucController::class, 'kiemTraSlugChuyenMucUpdate']);
    });
    //quản lý Yêu thích phim
    Route::group(['prefix'  =>  '/yeu-thich'], function () {
        // Yêu Thich
        Route::get('/lay-du-lieu', [YeuThichController::class, 'getData']);
        Route::post('/thong-tin-tao', [YeuThichController::class, 'taoYeuThich']);
        Route::post('/kiem-tra', [YeuThichController::class, 'checkYeuThich']);
        Route::post('/thong-tin-xoa', [YeuThichController::class, 'xoaYeuThich']);
        Route::put('/thong-tin-cap-nhat', [YeuThichController::class, 'capnhatYeuThich']);
        Route::put('/thong-tin-thay-doi-trang-thai', [YeuThichController::class, 'thaydoiTrangThaiYeuThich']);
        Route::post('/thong-tin-tim', [YeuThichController::class, 'timYeuThich']);
    });
    //quản lý Bình luận phim
    Route::group(['prefix'  =>  '/binh-luan-phim'], function () {
        // Bình luận Phim
        Route::get('/lay-du-lieu', [BinhLuanPhimController::class, 'getData']);
        Route::post('/thong-tin-tao', [BinhLuanPhimController::class, 'taoBinhLuanPhim']);
        Route::delete('/thong-tin-xoa/{id}', [BinhLuanPhimController::class, 'xoaBinhLuanPhim']);
    });
    //quản lý Bình luận Blog
    Route::group(['prefix'  =>  '/binh-luan-blog'], function () {
        // Bình luận Blog
        Route::get('/lay-du-lieu', [BinhLuanBaiVietController::class, 'getData']);
        Route::post('/thong-tin-tao', [BinhLuanBaiVietController::class, 'taoBinhLuanBlog']);
        Route::delete('/thong-tin-xoa/{id}', [BinhLuanBaiVietController::class, 'xoaBinhLuanBlog']);
    });
    // Quản lý Thống Kê
    Route::group(['prefix'  =>  '/thong-ke'], function () {
        Route::post('/data-thong-ke-1', [ThongKeController::class, 'getDataThongke1']);
    });
    // Quản lý Gói Vip
    Route::group(['prefix'  =>  '/goi-vip'], function () {
        Route::get('lay-du-lieu', [GoiVipController::class, 'getData']);
        Route::post('/thong-tin-tao', [GoiVipController::class, 'taoGoiVip']);
        Route::delete('/thong-tin-xoa/{id}', [GoiVipController::class, 'xoaGoiVip']);
        Route::put('/thong-tin-cap-nhat', [GoiVipController::class, 'capnhatGoiVip']);
        Route::put('/thong-tin-thay-doi-trang-thai', [GoiVipController::class, 'thaydoiTrangThaiGoiVip']);
        Route::post('/thong-tin-tim', [GoiVipController::class, 'timGoiVip']);
        Route::post('/kiem-tra-slug', [GoiVipController::class, 'kiemTraSlugGoiVip']);
        Route::post('/kiem-tra-slug-update', [GoiVipController::class, 'kiemTraSlugGoiVipUpdate']);
    });
});

Route::group(['prefix'  =>  '/khach-hang', 'middleware' => 'khach_hang'], function () {
    Route::put('/doi-mat-khau', [KhachHangController::class, 'doiPassUser']);
    Route::put('/doi-thong-tin', [KhachHangController::class, 'doiThongTinUser']);
    Route::post('/doi-avatar', [KhachHangController::class, 'doiAvatarUser']);
    Route::get('/lay-du-lieu-profile', [KhachHangController::class, 'getDataProfileUser']);
});



// Show data ở Home
Route::group(['prefix'  =>  '/phim'], function () {
    //  Phim
    Route::get('/lay-du-lieu-show', [PhimController::class, 'getDataHome']);
    Route::get('/lay-data-delist', [PhimController::class, 'getDataDelist']);
});
Route::group(['prefix'  =>  '/tap-phim'], function () {
    //  Phim
    Route::get('/lay-du-lieu-show', [TapPhimController::class, 'getDataHome']);
});
Route::group(['prefix'  =>  '/the-loai'], function () {
    // The Loai Phim
    Route::get('/lay-du-lieu-show', [TheLoaiController::class, 'getDataHome']);
    Route::get('/lay-du-lieu/{slug}', [TheLoaiController::class, 'getDataHomeTLPhim']);
});
Route::group(['prefix'  =>  '/loai-phim'], function () {
    // Loại Phim
    Route::get('/lay-du-lieu-show', [LoaiPhimController::class, 'getDataHome']);
    Route::get('/lay-du-lieu-show-tat-ca/{slug}', [LoaiPhimController::class, 'getDataHomeLPhim']);
});
Route::group(['prefix'  =>  '/tac-gia'], function () {
    // Tác Giả
    Route::get('/lay-du-lieu-show', [TacGiaController::class, 'getDataHome']);
});
Route::group(['prefix'  =>  '/bai-viet'], function () {
    // Bài Viết Blog
    Route::get('/lay-du-lieu-show', [BaiVietController::class, 'getDataHome']);
    Route::post('/change-chuyen-muc', [BaiVietController::class, 'changeChuyenMuc']);
    Route::post('/lay-du-lieu-delist-blog', [BaiVietController::class, 'getDelistBlog']);
});
Route::group(['prefix'  =>  '/chuyen-muc'], function () {
    // Chuyên Mục Blog
    Route::get('/lay-du-lieu-show', [ChuyenMucController::class, 'getDataHome']);
});
Route::group(['prefix'  =>  '/binh-luan-phim'], function () {
    // Bình luận Phim
    Route::get('/lay-du-lieu-show', [BinhLuanPhimController::class, 'getData']);
});
Route::group(['prefix'  =>  '/binh-luan-blog'], function () {
    // Bình luận Blog
    Route::get('/lay-du-lieu-show', [BinhLuanBaiVietController::class, 'getData']);
});

Route::post('/lay-data-theo-the-loai', [PhimController::class, 'dataTheoTL']);
Route::post('/phim/thong-tin-tim', [PhimController::class, 'timPhimHome']);
Route::get('/the-loai/sap-xep/{id_tl}/{catagory}', [TheLoaiController::class, 'sapxepHome']);
Route::get('/loai-phim/sap-xep/{id_lp}/{catagory}', [LoaiPhimController::class, 'sapxepHome']);
Route::get('/list-phim/sap-xep', [PhimController::class, 'sapxepHome']);
Route::post('/lay-data-watch', [PhimController::class, 'getDataXemPhim']);
Route::get('/lay-tat-ca-phim', [PhimController::class, 'getAllPhim']);
Route::get('lay-tat-ca-phim/sap-xep/{catagory}', [PhimController::class, 'getAllPhim']);
