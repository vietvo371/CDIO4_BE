<?php

namespace App\Http\Controllers;

use App\Models\ConfigSlide;
use App\Models\Phim;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

use function Laravel\Prompts\select;

class ConfigSlideController extends Controller
{
    public function getSlideHomepage(Request $request)
    {
        $data = Phim::where("tinh_trang", 1)->where('is_slide',1)->get();
        return response()->json([
            'data' => $data
        ]);
    }
    public function getData()
    {
        $id_chuc_nang = 16;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }


        $dataAdmin = Phim::where("is_slide", 1)->where("tinh_trang", 1)->select("phims.id", "phims.ten_phim", "phims.poster_img", "phims.updated_at")->orderByDesc("updated_at")->paginate(6);
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin,

        ];
        return response()->json([
            'slide_admin'  =>  $response,
        ]);
    }
    public function getDataOpen(Request $request)
    {
        $id_chuc_nang = 16;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin = Phim::where("is_slide", 0)->where("tinh_trang", 1)->select("phims.id", "phims.ten_phim", "phims.poster_img")->paginate(6);
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin,

        ];
        return response()->json([
            'phim_admin'  =>  $response,
        ]);
    }
    public function taoSlide(Request $request)
    {
        $id_chuc_nang = 16;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        Phim::where('id', $request->id)->update([
            'is_slide' => 1
        ]);
        return response()->json([
            'status'     => true,
            'message'    => 'Đã Thêm Slide thành công!!'
        ]);
    }
    public function xoaSlide($id)
    {
        try {
            $id_chuc_nang = 16;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            Phim::where('id', $id)->update([
                'is_slide' => 0
            ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Slide thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá  Slide không thành công!!'
            ]);
        }
    }
    public function timSlide(Request $request)
    {
        $id_chuc_nang = 16;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin = Phim::where('ten_phim', 'like', '%' . $request->key . '%')->where('is_slide', 1)->where('tinh_trang', 1)->select("phims.id", "phims.ten_phim", "phims.poster_img")->paginate(6);
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin,

        ];
        return response()->json([
            'slide_admin'  =>  $response,
        ]);
    }
    public function timPhim(Request $request)
    {
        $id_chuc_nang = 16;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin = Phim::where('ten_phim', 'like', '%' . $request->key . '%')->where('is_slide', 0)->where('tinh_trang', 1)->select("phims.id", "phims.ten_phim", "phims.poster_img")->paginate(6);
        $response = [
            'pagination' => [
                'total' => $dataAdmin->total(),
                'per_page' => $dataAdmin->perPage(),
                'current_page' => $dataAdmin->currentPage(),
                'last_page' => $dataAdmin->lastPage(),
                'from' => $dataAdmin->firstItem(),
                'to' => $dataAdmin->lastItem()
            ],
            'dataAdmin' => $dataAdmin,

        ];
        return response()->json([
            'phim_admin'  =>  $response,
        ]);
    }
}
