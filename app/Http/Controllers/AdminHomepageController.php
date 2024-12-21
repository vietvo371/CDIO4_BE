<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\LuotPhim;
use App\Models\Phim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminHomepageController extends Controller
{
    public function getDashboard()
    {
        $viewCount = Phim::sum('tong_luot_xem');
        $viewCountThangs = Phim::select(DB::raw("SUM(tong_luot_xem) as total_views"), DB::raw("MONTH(updated_at) as month"))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        $activeFilms = Phim::where('tinh_trang', 1)->count();
        $inactiveFilms = Phim::where('tinh_trang', 0)->count();
        $topphim = Phim::join('luot_xems', 'luot_xems.id_phim', '=', 'phims.id')
            ->select('phims.id', 'phims.ten_phim', DB::raw('SUM(luot_xems.so_luot_xem) as tong_luot_xem'))
            ->whereMonth('luot_xems.created_at', date('m'))
            ->whereYear('luot_xems.created_at', date('Y'))
            ->groupBy('phims.id', 'phims.ten_phim')
            ->orderBy('tong_luot_xem', 'DESC')
            ->limit(5)->get();

        $activeCustomers = KhachHang::where('is_active', 1)->count();
        $inactiveCustomers = KhachHang::where('is_active', 0)->count();

        $doanhThuCount = HoaDon::where('tinh_trang', 1)->sum('tong_tien');
        return response()->json([
            'viewCount'  =>  $viewCount,
            'viewCountThangs'  =>  $viewCountThangs,
            'activeFilms'  =>  $activeFilms,
            'inactiveFilms'  =>  $inactiveFilms,
            'topphim'  =>  $topphim,
            'activeCustomers'  =>  $activeCustomers,
            'inactiveCustomers'  =>  $inactiveCustomers,
            'doanhThuCount'  =>  $doanhThuCount,
        ]);
    }
    public function getDataThongkeDoanhThu($begin, $end) // Thống kê doanh thu bán gói VIP theo ngày
    {
        $data = HoaDon::whereDate('updated_at', '>=', $begin)
            ->whereDate('updated_at', '<=', $end)
            ->where('tinh_trang', 1) // Giả sử có trường `loai_giao_dich` để lọc hóa đơn VIP
            ->select(
                DB::raw("SUM(tong_tien) as total"),
                DB::raw("DATE_FORMAT(updated_at, '%d/%m/%Y') as lable")
            )
            ->groupBy('lable')
            ->orderBy(DB::raw("STR_TO_DATE(lable, '%d/%m/%Y')"), 'asc')
            ->get();

        $list_lable = [];
        $list_data  = [];

        foreach ($data as $value) {
            array_push($list_data, $value->total);
            array_push($list_lable, $value->lable);
        }

        return response()->json([
            'list_lable' => $list_lable,
            'list_data'  => $list_data,
        ]);
    }
    public function getDataThongkeLuotXem($begin, $end) // Thống kê lượt xem theo ngày
    {
        $data = LuotPhim::whereDate('updated_at', '>=', $begin)
            ->whereDate('updated_at', '<=', $end)
            ->select(
                DB::raw("COUNT(id) as total"), // Đếm số lượt xem
                DB::raw("DATE_FORMAT(updated_at, '%d/%m/%Y') as lable")
            )
            ->groupBy('lable')
            ->orderBy(DB::raw("STR_TO_DATE(lable, '%d/%m/%Y')"), 'asc')
            ->get();

        $list_lable = [];
        $list_data  = [];

        foreach ($data as $value) {
            array_push($list_data, $value->total);
            array_push($list_lable, $value->lable);
        }

        return response()->json([
            'list_lable' => $list_lable,
            'list_data'  => $list_data,
        ]);
    }
}
