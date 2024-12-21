<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\ChucVu;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanQuyenController extends Controller
{
    public function getDataPhanQuyen(){
        $id_chuc_nang = 4;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                                ->where('id_chuc_nang', $id_chuc_nang)
                                ->first();
            if(!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
        $listChucVu       = ChucVu::select('chuc_vus.*')
                ->get(); // get là ra 1 danh sách
        $listChucNang       = Action::select('actions.*')
                ->get(); // get là ra 1 danh sách
        return response()->json([
        'listChucVu'  =>  $listChucVu,
        'listChucNang'  =>  $listChucNang,
        ]);
    }
    public function createPhanQuyen(Request $request)
    {
        $id_chuc_nang = 4;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                                ->where('id_chuc_nang', $id_chuc_nang)
                                ->first();
            if(!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
        $check = PhanQuyen::where('id_chuc_nang', $request->id_chuc_nang)
                          ->where('id_chuc_vu', $request->id_chuc_vu)->first();
        if($check) {
            return response()->json([
                'status'    =>  false,
                'message'   =>  'Chức vụ đã có chức năng này!'
            ]);
        } else {
            PhanQuyen::create([
                'id_chuc_nang'  =>  $request->id_chuc_nang,
                'id_chuc_vu'    =>  $request->id_chuc_vu
            ]);

            return response()->json([
                'status'    =>  true,
                'message'   =>  'Đã phân quyền thành công'
            ]);
        }
    }

    public function getChucNang(Request $request)
    {
        $id_chuc_nang = 4;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                                ->where('id_chuc_nang', $id_chuc_nang)
                                ->first();
            if(!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
        $data   = PhanQuyen::join('actions', 'id_chuc_nang', 'actions.id')
                                      ->where('id_chuc_vu', $request->id)
                                      ->select('phan_quyens.*', 'actions.ten_chuc_nang')
                                      ->get();
        // $data   = PhanQuyen::where('id_chuc_vu', $request->id)->get();

        return response()->json([
            'data'   =>  $data
        ]);
    }

    public function xoaPhanQuyen($id)
    {
        $id_chuc_nang = 4;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                                ->where('id_chuc_nang', $id_chuc_nang)
                                ->first();
            if(!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
        $phan_quyen = PhanQuyen::where('id', $id)->first();

        if($phan_quyen) {
            $phan_quyen->delete();

            return response()->json([
                'status'    =>  true,
                'message'   =>  'Đã xóa phân quyền thành công'
            ]);
        } else {
            return response()->json([
                'status'    =>  false,
                'message'   =>  'Đã có lỗi xảy ra!'
            ]);
        }
    }
}
