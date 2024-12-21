<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyen;
use App\Models\Phim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    public function getDataThongke1(Request $request) // Thống kê so tap theo loại phim
    {
        $id_chuc_nang = 12;
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
        $data = Phim::join('the_loais', 'phims.id_the_loai', '=', 'the_loais.id')
                    ->join('loai_phims', 'phims.id_loai_phim', '=', 'loai_phims.id')
                    ->join('tac_gias', 'phims.id_tac_gia', '=', 'tac_gias.id')
                    ->where('phims.tinh_trang', 1)
                    ->whereDate('phims.created_at', ">=", $request->begin)
                    ->whereDate('phims.created_at', "<=", $request->end)
                    ->select(
                        DB::raw("COUNT(phims.id) as total"),
                        'loai_phims.ten_loai_phim'
                    )
                    ->groupBy('loai_phims.ten_loai_phim')
                    ->get();

            $list_label = [];
            $list_data = [];

            foreach ($data as $value) {
            $list_data[] = $value->total;
            $list_label[] = $value->ten_loai_phim;
            }
        return response()->json([
            'list_label' => $list_label,
            'list_data'  => $list_data,
        ]);
    }
}
