<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatTheLoaiRequest;
use App\Http\Requests\TaoTheLoaiRequest;
use App\Models\DanhMucWeb;
use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TheLoai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class TheLoaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $id_chuc_nang = 7;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin       = TheLoai::select('the_loais.*')
            ->paginate(6); // get là ra 1  sách


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
            'the_loai'  =>  $response,
        ]);
    }
    public function getDataHomeTLPhim(Request $request)
    {
        $checkSlug = TheLoai::where('slug_the_loai', $request->slug)->exists();
        if (!$checkSlug) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Slug không tồn tại'
            ]);
        }
        $the_loai               = TheLoai::where('the_loais.tinh_trang', 1)
            ->where('the_loais.slug_the_loai', $request->slug)
            ->select('the_loais.*')
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
                                            the_loais.slug_the_loai =  :slug_the_loai
                                        GROUP BY
                                            phims.id, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim,loai_phims.ten_loai_phim
                                        HAVING
                                            tong_tap > 0
                                    ) as subquery
                                '))
            ->setBindings(['slug_the_loai' => $request->slug])->paginate(9);

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

        $phim_9_obj = DB::table(DB::raw('
        (
            SELECT
                phims.id,
                phims.ten_phim,
                phims.hinh_anh,
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
                phims.id, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim
            HAVING
                tong_tap > 0
        ) as subquery
        '))
            ->take(7)->get();

        return response()->json([
            'the_loai'    =>  $the_loai,
            'phim'        =>  $response,
            'phim_9_obj'  =>  $phim_9_obj,
        ]);
    }
    public function sapxepHome($slug_the_loai, $catagory)
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
                the_loais.slug_the_loai = :slug_the_loai
            GROUP BY
                phims.id, phims.ten_phim, loai_phims.ten_loai_phim, phims.hinh_anh, phims.slug_phim, phims.tong_luot_xem, phims.mo_ta, phims.so_tap_phim
            HAVING
                tong_tap > 0
        ) as subquery
    '))->setBindings(['slug_the_loai' => $slug_the_loai])
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
            'phim1'  =>  $data,
        ]);
    }
    public function getDataHome()
    {
        $data   = TheLoai::where('the_loais.tinh_trang', 1)
            ->select('the_loais.*')
            ->get();
        $phims   = [];  // mảng chứa phim theo thể loại
        foreach ($data as $key  => $value) {
            $phim_theo_the_loai = Phim::join('loai_phims', 'id_loai_phim', 'loai_phims.id')
                ->where('phims.tinh_trang', 1)
                ->where('loai_phims.tinh_trang', 1)
                ->where('phims.id_the_loai', $value->id)
                ->select('phims.*', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim')
                //    ->orderBy('id', 'DESC') sắp xép giảm dần
                ->inRandomOrder()
                ->take(3)
                ->get();
            $phims = array_merge($phims, $phim_theo_the_loai->toArray());
        }
        return response()->json([
            'the_loai'                  =>  $data,
            'phim_theo_the_loai'        =>  $phims,
        ]);
    }



    public function taoTheLoai(TaoTheLoaiRequest $request)
    {
        try {
            $id_chuc_nang = 7;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            TheLoai::create([
                'ten_the_loai'      => $request->ten_the_loai,
                'slug_the_loai'     => $request->slug_the_loai,
                'id_danh_muc'        => $request->id_danh_muc,
                'tinh_trang'        => $request->tinh_trang,
            ]);
            return response()->json([

                'status'   => true,
                'message'  => 'Bạn thêm Thể Thể Loại thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Thể Thể Loại không thành công!!'
            ]);
        }
    }
    public function timTheLoai(Request $request)
    {
        $id_chuc_nang = 7;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin       = TheLoai::select('the_loais.*')
            ->where('ten_the_loai', 'like', $key)
            ->paginate(6); // get là ra 1  sách

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
            'the_loai'  =>  $response,
        ]);
    }
    public function capnhatTheLoai(CapNhatTheLoaiRequest $request)
    {
        try {
            $id_chuc_nang = 7;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            TheLoai::where('id', $request->id)
                ->update([
                    'ten_the_loai'      => $request->ten_the_loai,
                    'slug_the_loai'     => $request->slug_the_loai,
                    'id_danh_muc'        => $request->id_danh_muc,
                    'tinh_trang'        => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->ten_the_loai,
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Thể Loại không thành công!!'
            ]);
        }
    }
    public function xoaTheLoai($id)
    {
        try {
            $id_chuc_nang = 7;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            TheLoai::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Thể Loại thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Thể Loại không thành công!!'
            ]);
        }
    }
    public function thaydoiTrangThaiTheLoai(Request $request)
    {

        try {
            $id_chuc_nang = 7;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            TheLoai::where('id', $request->id)
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
    public function kiemTraSlugTheLoai(Request $request)
    {
        $tac_gia = TheLoai::where('slug_the_loai', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Thể Loại phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Thể Loại Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugTheLoaiUpdate(Request $request)
    {
        $mon_an = TheLoai::where('slug_the_loai', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Thể Loại phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Thể Loại Đã Tồn Tại!',
            ]);
        }
    }
}
