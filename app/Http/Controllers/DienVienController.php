<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDienVien;
use App\Models\DienVien;
use App\Models\Phim;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class DienVienController extends Controller
{
    public function getData()
    {
        $id_chuc_nang = 14;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataPhim       = Phim::get();

        $chiTietDienVien       = ChiTietDienVien::join('phims', 'id_phim', 'phims.id')->join('dien_viens', 'id_dien_vien', 'dien_viens.id')
            ->select('chi_tiet_dien_viens.*', 'dien_viens.ten_dv', 'phims.ten_phim')->get();


        $dataDienVien   = DienVien::paginate(6);

        $pagination = [
            'total' => $dataDienVien->total(),
            'per_page' => $dataDienVien->perPage(),
            'current_page' => $dataDienVien->currentPage(),
            'last_page' => $dataDienVien->lastPage(),
            'from' => $dataDienVien->firstItem(),
            'to' => $dataDienVien->lastItem()
        ];

        $dienviensArray = $dataDienVien->toArray();

        foreach ($dienviensArray['data'] as &$dien_vien) {
            $dien_viens = [];
            foreach ($chiTietDienVien as $dienVien) {
                if ($dienVien['id_dien_vien'] == $dien_vien['id']) {
                    array_push($dien_viens, $dienVien->toArray());
                }
            }
            $dien_vien['phims'] = $dien_viens;
        }
        unset($dien_vien);

        $dataAdmin = $dienviensArray;

        $response = [
            'dataAdmin' => $dataAdmin,
            'pagination' => $pagination
        ];

        return response()->json([
            'dien_vien_admin'  =>  $response,
            'dataPhim'         =>  $dataPhim,
        ]);
    }
    public function taoDienVien(Request $request)
    {
        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }

            $phims = $request->phims;
            // $phimsArray = explode(',', $phims);
            // dd($theloais);
            $dienvien  = DienVien::create([
                'ten_dv'                  => $request->ten_dv,
                'mo_ta'                   => $request->mo_ta,
                'nam_sinh'                => $request->nam_sinh,
                'tinh_trang'              => $request->tinh_trang,
            ]);
            if ($dienvien) {
                foreach ($phims as $value) {
                    ChiTietDienVien::create([
                        'id_dien_vien' => $dienvien->id,
                        'id_phim' => (int) $value,
                    ]);
                }
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm Diên Viên thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'thêm Diên Viên không thành công!!'
            ]);
        }
    }
    public function xoaDienVien($id)
    {
        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            DienVien::where('id', $id)->delete();
            ChiTietDienVien::where('id_dien_vien', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Diễn Viên thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Diễn Viên không thành công!!'
            ]);
        }
    }
    public function capnhatDienVien(Request $request)
    {
        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $DienVien = DienVien::where('id', $request->id)->first();

            if (!$DienVien) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy Diễn Viên',
                ]);
            }
            $DienVienUd = DienVien::where('id', $request->id)
                ->update([
                    'ten_dv'                  => $request->ten_dv,
                    'mo_ta'                   => $request->mo_ta,
                    'nam_sinh'                => $request->nam_sinh,
                    'tinh_trang'              => $request->tinh_trang,
                ]);
            ChiTietDienVien::where('id_dien_vien', $DienVien->id)->delete(); // Xóa các thể loại hiện tại
            $phims = $request->phims;
            if ($DienVienUd) {
                foreach ($phims as $value) {
                    ChiTietDienVien::create([
                        'id_dien_vien' => $DienVien->id,
                        'id_phim' => (int) $value,
                    ]);
                }
            }
            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành công',
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Diên Viên không thành công!!'
            ]);
        }
    }
    public function thaydoiTrangThaiDienVien(Request $request)
    {

        try {
            $id_chuc_nang = 14;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            DienVien::where('id', $request->id)
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
    public function timDienVien(Request $request)
    {
        $id_chuc_nang = 14;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataPhim       = Phim::get();

        $chiTietDienVien       = ChiTietDienVien::join('phims', 'id_phim', 'phims.id')->join('dien_viens', 'id_dien_vien', 'dien_viens.id')
            ->select('chi_tiet_dien_viens.*', 'dien_viens.ten_dv', 'phims.ten_phim')->get();


        $dataDienVien   = DienVien::where('dien_viens.ten_dv', 'like', $key)->paginate(6);

        $pagination = [
            'total' => $dataDienVien->total(),
            'per_page' => $dataDienVien->perPage(),
            'current_page' => $dataDienVien->currentPage(),
            'last_page' => $dataDienVien->lastPage(),
            'from' => $dataDienVien->firstItem(),
            'to' => $dataDienVien->lastItem()
        ];

        $dienviensArray = $dataDienVien->toArray();

        foreach ($dienviensArray['data'] as &$dien_vien) {
            $dien_viens = [];
            foreach ($chiTietDienVien as $dienVien) {
                if ($dienVien['id_dien_vien'] == $dien_vien['id']) {
                    array_push($dien_viens, $dienVien->toArray());
                }
            }
            $dien_vien['phims'] = $dien_viens;
        }
        unset($dien_vien);

        $dataAdmin = $dienviensArray;

        $response = [
            'dataAdmin' => $dataAdmin,
            'pagination' => $pagination
        ];

        return response()->json([
            'dien_vien_admin'  =>  $response,
            'dataPhim'         =>  $dataPhim,
        ]);
    }
    public function kiemTraSlugDienVien(Request $request)
    {
        $id_chuc_nang = 14;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $phim = DienVien::where('slug_phim', $request->slug)->first();

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
    public function kiemTraSlugDienVienUpdate(Request $request)
    {
        $id_chuc_nang = 14;
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
