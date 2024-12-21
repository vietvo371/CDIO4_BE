<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TapPhim;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class TapPhimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData(Request $request)
    {
        $id_chuc_nang = 6;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }

        $dataAdmin       = TapPhim::where('id_phim', $request->id_phim)
            ->orderBy('so_tap', 'DESC')
            ->select('tap_phims.*')
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
            'tap_phim_admin'  =>  $response,
        ]);
    }
    public function getDataHome()
    {
        $data       = TapPhim::join('phims', 'id_phim', 'phims.id')
            ->join('the_loais', 'phims.id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'phims.id_loai_phim', 'loai_phims.id')
            ->where('tap_phims.tinh_trang', 1)
            ->select('tap_phims.*', 'phims.ten_phim', 'the_loais.ten_the_loai', 'loai_phims.ten_loai_phim', 'phims.id_the_loai', 'phims.id_loai_phim')
            ->get(); // get là ra 1 danh sách
        return response()->json([
            'tap_phim'  =>  $data,
        ]);
    }
    public function taoTapPhim(Request $request)
    {
        try {
            $id_chuc_nang = 6;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tap_phim_exist = TapPhim::where('id_phim', $request->id)
                ->where('so_tap', $request->so_tap)
                ->first();
            $phim = Phim::find($request->id);

            if (!$tap_phim_exist) {
                if ($request->so_tap <= $phim->so_tap_phim) {
                    $data = $request->all();
                    $data['slug_tap_phim'] = 'tap-' . $data['so_tap'] . '-' . uniqid();
                    TapPhim::create([
                        'slug_tap_phim'         => $data['slug_tap_phim'],
                        'so_tap'                => $data['so_tap'],
                        'url'                   => $data['url'],
                        'id_phim'               => $data['id'],
                        'tinh_trang'            => $data['tinh_trang'],
                    ]);
                } else {
                    return response()->json([
                        'status'   => false,
                        'message'  => 'Tập phim khong vượt quá số lượng tập',
                    ]);
                }
            } else {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Tập phim đã tồn tại',
                ]);
            }
            return response()->json([
                'status'   => true,
                'message'  => ' thêm Tập Phim thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xảy ra lỗi!!'
            ]);
        }
    }
    public function timTapPhim(Request $request)
    {
        $id_chuc_nang = 6;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin   = TapPhim::join('phims', 'id_phim', 'phims.id')
            ->orderBy('slug_tap_phim', 'ASC')
            ->select('tap_phims.*', 'phims.ten_phim')
            ->where('phims.ten_phim', 'like', $key)
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
            'tap_phim_admin'  =>  $response,
        ]);
    }
    public function capnhatTapPhim(Request $request)
    {
        try {
            $id_chuc_nang = 6;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $data = $request->all();
            TapPhim::where('id', $request->id)->update([
                'url'                   => $data['url'],
                'tinh_trang'            => $data['tinh_trang'],
            ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Cập Nhật thành công',
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xảy ra lỗi!!'
            ]);
        }
    }
    public function xoaTapPhim($id)
    {
        try {
            $id_chuc_nang = 6;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            TapPhim::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Xoá Tập phim thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Tập phim không thành công!!'
            ]);
        }
    }
    public function xoaAllTapPhim($id)
    {
        try {
            $id_chuc_nang = 6;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }

            $tap_phim = TapPhim::where('id_phim', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Xoá Tất cả tập phim thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Tập phim không thành công!!'
            ]);
        }
    }
    public function thaydoiTrangThaiTapPhim(Request $request)
    {

        try {
            $id_chuc_nang = 6;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            TapPhim::where('id', $request->id)
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
    public function kiemTraSlugTapPhim(Request $request)
    {
        $tac_gia = TapPhim::where('slug_tap_phim', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Tập Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Tập Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugTapPhimUpdate(Request $request)
    {
        $tap_phim = TapPhim::where('slug_tap_phim', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$tap_phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tập Phim phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tập Phim Đã Tồn Tại!',
            ]);
        }
    }
    public function layTenPhim(Request $request)
    {
        $ten_phim =     Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.id', $request->id_phim)
            ->first();

        if ($ten_phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'lấy tên phim thành công!',
                'ten_phim'          => $ten_phim,
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'lấy tên phim không thành công!',
            ]);
        }
    }
    public function layTenPhimUpdate(Request $request)
    {
        $ten_phim =     Phim::join('the_loais', 'id_the_loai', 'the_loais.id')
            ->join('loai_phims', 'id_loai_phim', 'loai_phims.id')
            ->join('tac_gias', 'id_tac_gia', 'tac_gias.id')
            ->where('phims.id', $request->id_phim)
            ->first();

        if ($ten_phim) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'lấy tên phim thành công!',
                'ten_phim'          => $ten_phim,
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'lấy tên phim không thành công!',
            ]);
        }
    }
}
