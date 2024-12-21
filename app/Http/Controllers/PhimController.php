<?php

namespace App\Http\Controllers;

use App\Models\ChiTietTheLoai;
use App\Models\LoaiPhim;
use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TacGia;
use App\Models\TapPhim;
use App\Models\TheLoai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class PhimController extends Controller
{

    public function getData()
    {
        $id_chuc_nang = 5;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $the_loai_admin   = TheLoai::where('tinh_trang', 1)->select('the_loais.*')
            ->get();
        $loai_phim_admin   = LoaiPhim::where('tinh_trang', 1)->select('loai_phims.*')
            ->get();
        $dataAdmin   = Phim::join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->select('phims.*',  'loai_phims.ten_loai_phim')
            ->orderBy('created_at', 'DESC')
            ->paginate(6); // get là ra 1  sách
        $theloais = ChiTietTheLoai::join('the_loais', 'chi_tiet_the_loais.id_the_loai', 'the_loais.id')
            ->select('chi_tiet_the_loais.*', 'the_loais.id', 'the_loais.ten_the_loai', 'chi_tiet_the_loais.id_phim')
            ->get();
        $pagination = [
            'total' => $dataAdmin->total(),
            'per_page' => $dataAdmin->perPage(),
            'current_page' => $dataAdmin->currentPage(),
            'last_page' => $dataAdmin->lastPage(),
            'from' => $dataAdmin->firstItem(),
            'to' => $dataAdmin->lastItem()
        ];

        $phimsArray = $dataAdmin->toArray();

        foreach ($phimsArray['data'] as &$phim) {
            $the_loais = [];
            foreach ($theloais as $theLoai) {
                if ($theLoai['id_phim'] == $phim['id']) {
                    array_push($the_loais, $theLoai->toArray());
                }
            }
            $phim['the_loais'] = $the_loais;
        }
        unset($phim);

        $dataAdmin = $phimsArray;

        $response = [
            'dataAdmin' => $dataAdmin,
            'pagination' => $pagination
        ];

        return response()->json([
            'phim_admin'  =>  $response,
            'the_loai_admin'  =>  $the_loai_admin,
            'loai_phim_admin'  =>  $loai_phim_admin,
        ]);
    }
    public function getDataTheoTap()
    {
        $phims = Phim::leftJoin('tap_phims', 'tap_phims.id_phim', 'phims.id')
            ->select('phims.id', 'phims.ten_phim', 'phims.hinh_anh', 'phims.thoi_gian_chieu', 'phims.nam_san_xuat', 'phims.so_tap_phim', 'phims.tinh_trang', DB::raw('COUNT(tap_phims.id) as tong_tap'))
            ->groupBy('phims.id', 'phims.ten_phim', 'phims.hinh_anh', 'phims.thoi_gian_chieu', 'phims.nam_san_xuat', 'phims.so_tap_phim', 'phims.tinh_trang')
            ->orderBy('phims.created_at', 'DESC')
            ->paginate(6);
        $response = [
            'pagination' => [
                'total' => $phims->total(),
                'per_page' => $phims->perPage(),
                'current_page' => $phims->currentPage(),
                'last_page' => $phims->lastPage(),
                'from' => $phims->firstItem(),
                'to' => $phims->lastItem()
            ],
            'dataAdmin' => $phims
        ];
        return response()->json([
            'phim_admin'  =>  $response,
        ]);
    }
    public function timPhimTheoTap(Request $request)
    {
        $id_chuc_nang = 5;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $phims = Phim::leftJoin('tap_phims', 'tap_phims.id_phim', 'phims.id')
            ->where('phims.ten_phim', 'like', $key)
            ->select('phims.id', 'phims.ten_phim', 'phims.hinh_anh', 'phims.thoi_gian_chieu', 'phims.nam_san_xuat', 'phims.so_tap_phim', 'phims.tinh_trang', DB::raw('COUNT(tap_phims.id) as tong_tap'))
            ->groupBy('phims.id', 'phims.ten_phim', 'phims.hinh_anh', 'phims.thoi_gian_chieu', 'phims.nam_san_xuat', 'phims.so_tap_phim', 'phims.tinh_trang')
            ->orderBy('phims.created_at', 'DESC')
            ->paginate(6);
        $response = [
            'pagination' => [
                'total' => $phims->total(),
                'per_page' => $phims->perPage(),
                'current_page' => $phims->currentPage(),
                'last_page' => $phims->lastPage(),
                'from' => $phims->firstItem(),
                'to' => $phims->lastItem()
            ],
            'dataAdmin' => $phims
        ];
        return response()->json([
            'phim_admin'  =>  $response,
        ]);
    }
    public function getDataXemPhim(Request $request)
    {
        $data   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.slug_phim', $request->slug)
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
            ->first(); // get là ra 1 danh sách

        return response()->json([
            'phim' => $data,
        ]);
    }
    public function dataTheoTL(Request $request)
    {
        $id_tl    = $request->id_tl;
        $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('id_the_loai', $id_tl)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
            ->get();
        return response()->json([
            'phim_theo_tl'  =>  $data,
        ]);
    }
    public function getAllPhim()
    {
        $data   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
            ->paginate(6); // get là ra 1  sách

        $data9   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
            ->take(9)
            ->get(); // get là ra 1  sách
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'dataPhim' => $data
        ];
        return response()->json([
            'phim'             =>  $response,
            'phim_9_obj'       =>  $data9,
        ]);
    }
    public function getDataHome()
    {
        $phim_moi_cap_nhat = Phim::join('chi_tiet_the_loais', 'chi_tiet_the_loais.id_phim', 'phims.id')
            ->join('the_loais', 'chi_tiet_the_loais.id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->where('phims.tinh_trang', 1)
            ->where('phims.so_tap_phim', '>', 1)
            ->select('chi_tiet_the_loais.id_phim', 'phims.ten_phim', 'loai_phims.ten_loai_phim', 'phims.hinh_anh', 'phims.slug_phim', 'phims.tong_luong_xem', 'phims.mo_ta', 'phims.so_tap_phim', DB::raw('group_concat(the_loais.ten_the_loai) as ten_the_loais'))
            ->groupBy('chi_tiet_the_loais.id_phim', 'phims.ten_phim', 'loai_phims.ten_loai_phim',  'phims.hinh_anh', 'phims.slug_phim', 'phims.tong_luong_xem', 'phims.mo_ta', 'phims.so_tap_phim')
            ->orderBy('phims.created_at', 'DESC') // sắp xếp giảm dần
            ->get();
        $so_luong_tap = TapPhim::join('phims', 'tap_phims.id_phim', 'phims.id')
            ->where('phims.tinh_trang', 1)
            ->select('phims.ten_phim', DB::raw('COUNT(tap_phims.id_phim) as tong_so_tap'))
            ->groupBy('phims.ten_phim')
            ->take(9)
            ->get();

        return response()->json([
            'phim_moi_cap_nhats'  =>  $phim_moi_cap_nhat,
            'so_luong_tap'  =>  $so_luong_tap,

        ]);
    }
    public function getDataDelist(Request $request)
    {
        $phim                   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->where('phims.slug_phim', $request->slug)
            ->select('phims.*', 'the_loais.ten_the_loai', 'the_loais.id as id_tl', 'the_loais.slug_the_loai', 'loai_phims.ten_loai_phim')
            ->first();
        $data5   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.tinh_trang', 1)
            ->where('the_loais.tinh_trang', 1)
            ->where('loai_phims.tinh_trang', 1)
            ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
            ->inRandomOrder() // Lấy ngẫu nhiên
            ->take(5)
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'phim'        =>  $phim,
            'phim_5_obj'  =>  $data5,
        ]);
    }
    public function sapxepHome(Request $request)
    {
        $catagory = $request->catagory;
        if ($catagory === 'az') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->orderBy('ten_phim', 'ASC')  // tăng dần
                ->get();
        } else if ($catagory === 'za') {
            $data = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->orderBy('ten_phim', 'DESC')  // giảm dần
                ->get();
        }
        return response()->json([
            'phim'  =>  $data,
        ]);
    }

    public function taoPhim(Request $request)
    {
        try {
            $id_chuc_nang = 5;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/admin/phim'), $fileName);
                $filePath = asset('uploads/admin/phim/' . $fileName);
            }
            $theloais = $request->the_loais;
            $theloaisArray = explode(',', $theloais);
            // dd($theloais);
            $phim  = Phim::create([
                'ten_phim'                  => $request->ten_phim,
                'slug_phim'                 => $request->slug_phim,
                'hinh_anh'                  => $filePath,
                'mo_ta'                     => $request->mo_ta,
                'thoi_gian_chieu'           => $request->thoi_gian_chieu,
                'nam_san_xuat'              => $request->nam_san_xuat,
                'quoc_gia'                  => $request->quoc_gia,
                'id_loai_phim'              => $request->id_loai_phim,
                'dao_dien'                  => $request->dao_dien,
                'so_tap_phim'               => $request->so_tap_phim,
                'tinh_trang'                => $request->tinh_trang,
                'cong_ty_san_xuat'          => $request->cong_ty_san_xuat,
            ]);
            if ($phim) {
                foreach ($theloaisArray as $value) {
                    ChiTietTheLoai::create([
                        'id_phim' => $phim->id,
                        'id_the_loai' => (int) $value,
                    ]);
                }
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm Phim thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'thêm Phim không thành công!!'
            ]);
        }
    }

    public function timPhim(Request $request)
    {
        $id_chuc_nang = 5;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $the_loai_admin   = TheLoai::where('tinh_trang', 1)->select('the_loais.*')
            ->get();
        $loai_phim_admin   = LoaiPhim::where('tinh_trang', 1)->select('loai_phims.*')
            ->get();
        $dataAdmin   = Phim::join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->where('phims.ten_phim', 'like', $key)
            ->select('phims.*',  'loai_phims.ten_loai_phim')
            ->orderBy('created_at', 'DESC')
            ->paginate(6); // get là ra 1  sách
        $theloais = ChiTietTheLoai::join('the_loais', 'chi_tiet_the_loais.id_the_loai', 'the_loais.id')
            ->select('chi_tiet_the_loais.*', 'the_loais.id', 'the_loais.ten_the_loai', 'chi_tiet_the_loais.id_phim')
            ->get();
        $pagination = [
            'total' => $dataAdmin->total(),
            'per_page' => $dataAdmin->perPage(),
            'current_page' => $dataAdmin->currentPage(),
            'last_page' => $dataAdmin->lastPage(),
            'from' => $dataAdmin->firstItem(),
            'to' => $dataAdmin->lastItem()
        ];

        $phimsArray = $dataAdmin->toArray();

        foreach ($phimsArray['data'] as &$phim) {
            $the_loais = [];
            foreach ($theloais as $theLoai) {
                if ($theLoai['id_phim'] == $phim['id']) {
                    array_push($the_loais, $theLoai->toArray());
                }
            }
            $phim['the_loais'] = $the_loais;
        }
        unset($phim);

        $dataAdmin = $phimsArray;

        $response = [
            'dataAdmin' => $dataAdmin,
            'pagination' => $pagination
        ];

        return response()->json([
            'phim_admin'  =>  $response,
            'the_loai_admin'  =>  $the_loai_admin,
            'loai_phim_admin'  =>  $loai_phim_admin,
        ]);
    }
    public function xoaPhim($id)
    {
        try {
            $id_chuc_nang = 5;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $phim = Phim::where('id', $id)->first();
            if ($phim->hinh_anh && file_exists(public_path('uploads/admin/phim/' . basename($phim->hinh_anh)))) {
                unlink(public_path('uploads/admin/phim/' . basename($phim->hinh_anh)));
            }
            Phim::where('id', $id)->delete();
            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Phim thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Phim không thành công!!'
            ]);
        }
    }

    public function capnhatPhim(Request $request)
    {
        try {
            $id_chuc_nang = 5;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $phim = Phim::where('id', $request->id)->first();

            if (!$phim) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy Phim',
                ]);
            }
            $filePath = $phim->hinh_anh; // Giữ nguyên đường dẫn ảnh cũ nếu không có file mới được gửi
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/admin/phim'), $fileName);
                $filePath = asset('uploads/admin/phim/' . $fileName);

                // Xóa ảnh cũ nếu có
                if ($phim->hinh_anh && file_exists(public_path('uploads/admin/phim/' . basename($phim->hinh_anh)))) {
                    unlink(public_path('uploads/admin/phim/' . basename($phim->hinh_anh)));
                }
            }
            $phimud = Phim::where('id', $request->id)
                ->update([
                    'ten_phim'                  => $request->ten_phim,
                    'slug_phim'                 => $request->slug_phim,
                    'hinh_anh'                  => $filePath,
                    'mo_ta'                     => $request->mo_ta,
                    'thoi_gian_chieu'           => $request->thoi_gian_chieu,
                    'nam_san_xuat'              => $request->nam_san_xuat,
                    'quoc_gia'                  => $request->quoc_gia,
                    'id_loai_phim'              => $request->id_loai_phim,
                    'dao_dien'                  => $request->dao_dien,
                    'so_tap_phim'               => $request->so_tap_phim,
                    'tinh_trang'                => $request->tinh_trang,
                    'cong_ty_san_xuat'          => $request->cong_ty_san_xuat,
                ]);
            ChiTietTheLoai::where('id_phim', $phim->id)->delete(); // Xóa các thể loại hiện tại
            $theloais = $request->the_loais;
            $theloaisArray = explode(',', $theloais);
            if ($phimud) {
                foreach ($theloaisArray as $value) {
                    ChiTietTheLoai::create([
                        'id_phim' => $phim->id,
                        'id_the_loai' => (int) $value,
                    ]);
                }
            }

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->ten_phim,
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Phim không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiPhim(Request $request)
    {

        try {
            $id_chuc_nang = 5;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            Phim::where('id', $request->id)
                ->update([
                    'tinh_trang'    => $tinh_trang_moi
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Cập Nhật Trạng Thái thành công!! '
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Trạng Thái không thành công!!'
            ]);
        }
    }
    public function timPhimHome(Request $request)
    {
        $key    = '%' . $request->key . '%';
        if ($request->key == "") {
            $data = [];
        } else {
            $data   = Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
                ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                ->where('ten_phim', 'like', $key)
                ->get(); // get là ra 1 danh sách
        }
        return response()->json([
            'phim'  =>  $data,
        ]);
    }
    public function kiemTraSlugPhim(Request $request)
    {
        $id_chuc_nang = 5;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $phim = Phim::where('slug_phim', $request->slug)->first();

        if (!$phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugPhimUpdate(Request $request)
    {
        $id_chuc_nang = 5;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $mon_an = Phim::where('slug_phim', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Phim Đã Tồn Tại!',
            ]);
        }
    }
}
