<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapnhatLoaiPhimRequest;
use App\Http\Requests\TaoLoaiPhimRequest;
use App\Http\Requests\ThaydoiTrangThaiLoaiPhimRequest;
use App\Models\DanhMucWeb;
use App\Models\LoaiPhim;
use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TheLoai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class LoaiPhimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $id_chuc_nang = 8;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }

        $dataAdmin   = LoaiPhim::select('loai_phims.*')
            ->paginate(9); // get là ra 1  sách
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin
        ];
        return response()->json([
            'loai_phim_admin'  =>  $response,
        ]);
    }
    public function getMenuHome()
    {
        // $data   = LoaiPhim::where('loai_phims.tinh_trang', 1)
        //     ->select('loai_phims.*')
        //     ->get(); // get là ra 1 danh sách

        // $data_1   = TheLoai::where('the_loais.tinh_trang', 1)
        //     ->select('the_loais.*')
        //     ->get(); // get là ra 1 danh sách
        $data_2   = DanhMucWeb::where('danh_muc_webs.tinh_trang', 1)
            ->select('danh_muc_webs.*')
            ->get(); // get là ra 1 danh sách

        return response()->json([
            // 'loai_phim'  =>  $data,
            // 'the_loai'  =>  $data_1,
            'danh_muc_webs'  =>  $data_2,
        ]);
    }
    public function getDataHomeLPhim($slug)
    {
        $loai_phim               = LoaiPhim::where('loai_phims.tinh_trang', 1)
            ->where('loai_phims.slug_loai_phim', $slug)
            ->select('loai_phims.*')
            ->first();

        $phim = DB::table(DB::raw('
            (
                SELECT
                    phims.id,
                    phims.ten_phim,
                    phims.hinh_anh,
                    phims.slug_phim,
                    phims.mo_ta,
                    phims.tong_luot_xem,
                    phims.so_tap_phim,
                    loai_phims.ten_loai_phim,
                    loai_phims.slug_loai_phim,
                    GROUP_CONCAT(DISTINCT the_loais.ten_the_loai SEPARATOR ", ") as ten_the_loais,
                    (
                        SELECT COUNT(tap_phims.id)
                        FROM tap_phims
                        WHERE tap_phims.id_phim = phims.id
                    ) as tong_tap
                FROM
                    phims
                JOIN
                    chi_tiet_the_loais ON chi_tiet_the_loais.id_phim = phims.id
                JOIN
                    loai_phims ON loai_phims.id = phims.id_loai_phim
                JOIN
                    the_loais ON chi_tiet_the_loais.id_the_loai = the_loais.id
                WHERE
                    phims.tinh_trang = 1
                AND
                    loai_phims.tinh_trang = 1
                AND
                    the_loais.tinh_trang = 1
                AND
                 slug_loai_phim = :slug
                GROUP BY
                    phims.id,loai_phims.ten_loai_phim,loai_phims.slug_loai_phim,phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim
                HAVING
                    tong_tap > 0
            ) as subquery
            '))->setBindings(['slug' => $slug])
            ->paginate(9);

        $response = [
            'pagination' => [
                'total' => $phim->total(),
                'per_page' => $phim->perPage(),
                'current_page' => $phim->currentPage(),
                'last_page' => $phim->lastPage(),
                'from' => $phim->firstItem(),
                'to' => $phim->lastItem()
            ],
            'dataPhim' => $phim
        ];
        $data_9 = DB::table(DB::raw('
            (
                SELECT
                    phims.id,
                    phims.ten_phim,
                    phims.hinh_anh,
                    loai_phims.ten_loai_phim,
                    phims.slug_phim,
                    phims.mo_ta,
                    phims.tong_luot_xem,
                    phims.so_tap_phim,
                    GROUP_CONCAT(DISTINCT the_loais.ten_the_loai SEPARATOR ", ") as ten_the_loais,
                    (
                        SELECT COUNT(tap_phims.id)
                        FROM tap_phims
                        WHERE tap_phims.id_phim = phims.id
                    ) as tong_tap
                FROM
                    phims
                JOIN
                    chi_tiet_the_loais ON chi_tiet_the_loais.id_phim = phims.id
                JOIN
                    loai_phims ON loai_phims.id = phims.id_loai_phim
                JOIN
                    the_loais ON chi_tiet_the_loais.id_the_loai = the_loais.id
                WHERE
                    phims.tinh_trang = 1
                AND
                    loai_phims.tinh_trang = 1
                AND
                    the_loais.tinh_trang = 1
                GROUP BY
                    phims.id,loai_phims.ten_loai_phim, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim
                HAVING
                    tong_tap > 0
            ) as subquery
        '))
            ->take(8)->inRandomOrder()->get();
        return response()->json([
            'loai_phim'    =>  $loai_phim,
            'phim'        =>  $response,
            'phim_9_obj'  =>  $data_9,
        ]);
    }
    public function sapxepHome($slug_loai_phim, $catagory)
    {
        $data = DB::table(DB::raw('
        (
            SELECT
                phims.id,
                phims.ten_phim,
                loai_phims.ten_loai_phim,
                phims.hinh_anh,
                phims.slug_phim,
                phims.tong_luot_xem,
                phims.mo_ta,
                phims.so_tap_phim,
                GROUP_CONCAT(DISTINCT the_loais.ten_the_loai SEPARATOR ", ") as ten_the_loais,
                (
                    SELECT COUNT(tap_phims.id)
                    FROM tap_phims
                    WHERE tap_phims.id_phim = phims.id
                ) as tong_tap
            FROM
                phims
            JOIN
                chi_tiet_the_loais ON chi_tiet_the_loais.id_phim = phims.id
            JOIN
                loai_phims ON loai_phims.id = phims.id_loai_phim
            JOIN
                the_loais ON chi_tiet_the_loais.id_the_loai = the_loais.id
            WHERE
                phims.tinh_trang = 1
            AND
                loai_phims.tinh_trang = 1
            AND
                the_loais.tinh_trang = 1
            AND
                loai_phims.slug_loai_phim = :slug_loai_phim
            GROUP BY
                phims.id, phims.ten_phim, loai_phims.ten_loai_phim, phims.hinh_anh, phims.slug_phim, phims.tong_luot_xem, phims.mo_ta, phims.so_tap_phim
            HAVING
                tong_tap > 0
        ) as subquery
    '))->setBindings(['slug_loai_phim' => $slug_loai_phim])
            ->orderBy('ten_phim', $catagory) // Sắp xếp theo tổng lượt xem giảm dần
            ->paginate(9);
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
            'phim'  =>  $response,
        ]);
    }

    public function taoLoaiPhim(TaoLoaiPhimRequest $request)
    {
        try {
            $id_chuc_nang = 8;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            LoaiPhim::create([
                'ten_loai_phim'          => $request->ten_loai_phim,
                'slug_loai_phim'         => $request->slug_loai_phim,
                'tinh_trang'             => $request->tinh_trang,
                'id_danh_muc'            => $request->id_danh_muc,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm Loại Phim thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Loại Phim không thành công!!'
            ]);
        }
    }
    public function timLoaiPhim(Request $request)
    {
        $id_chuc_nang = 8;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin   = LoaiPhim::select('loai_phims.*')
            ->where('ten_loai_phim', 'like', $key)
            ->paginate(9); // get là ra 1  sách
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin
        ];
        return response()->json([
            'loai_phim_admin'  =>  $response,
        ]);
    }
    public function capnhatLoaiPhim(CapnhatLoaiPhimRequest $request)
    {
        try {
            $id_chuc_nang = 8;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            LoaiPhim::where('id', $request->id)
                ->update([
                    'ten_loai_phim'          => $request->ten_loai_phim,
                    'slug_loai_phim'         => $request->slug_loai_phim,
                    'tinh_trang'             => $request->tinh_trang,
                    'id_danh_muc'            => $request->id_danh_muc,
                ]);
            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->ten_loai_phim,
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật  Loại Phim không thành công!!'
            ]);
        }
    }
    public function xoaLoaiPhim($id)
    {
        try {
            $id_chuc_nang = 8;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            LoaiPhim::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Loai Phim thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá  Loai Phim không thành công!!'
            ]);
        }
    }
    public function thaydoiTrangThaiLoaiPhim(ThaydoiTrangThaiLoaiPhimRequest $request)
    {

        try {
            $id_chuc_nang = 8;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            LoaiPhim::where('id', $request->id)
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
    public function kiemTraSlugLoaiPhim(Request $request)
    {
        $loai_phim = LoaiPhim::where('slug_loai_phim', $request->slug)->first();

        if (!$loai_phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Loại Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Loại Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugLoaiPhimUpdate(Request $request)
    {
        $mon_an = LoaiPhim::where('slug_loai_phim', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Loại Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Loại Phim Đã Tồn Tại!',
            ]);
        }
    }
}
