<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatPhimRequest;
use App\Http\Requests\TaoPhimRequest;
use App\Http\Requests\ThayDoiTrangThaiPhimRequest;
use App\Models\ChiTietTheLoai;
use App\Models\HoaDon;
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
        $the_loai = TheLoai::select('ten_the_loai')
            ->where('tinh_trang', 1)
            ->get();

        $phim = Phim::where('slug_phim', $request->slugMovie)->firstOrFail();
        $tap_phims = TapPhim::where('id_phim', $phim->id)->orderBy('so_tap', 'ASC')->get();
        $tap = TapPhim::where('slug_tap_phim', $request->slugEpisode)->firstOrFail();
        return response()->json([
            'the_loai'  =>  $the_loai,
            'phim'      =>  $phim,
            'tap_phims' =>  $tap_phims,
            'tap'       =>  $tap,
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
        $data = DB::table(DB::raw('
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
            ->paginate(9);

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
            'phim_9_obj'       =>  $data_9,
        ]);
    }
    public function getDataHome()
    {
        try {
            $top_view_thang = DB::table(DB::raw('
            (
                SELECT
                    phims.ten_phim,
                    phims.hinh_anh,
                    phims.slug_phim,
                    phims.mo_ta,
                    phims.tong_luot_xem,
                    phims.so_tap_phim,
                    loai_phims.ten_loai_phim,
                    GROUP_CONCAT(DISTINCT the_loais.ten_the_loai SEPARATOR ", ") AS ten_the_loais,  -- Lấy thể loại phim
                    COUNT(DISTINCT tap_phims.id) AS tong_tap,  -- Đếm số tập phim
                    SUM(luot_xems.so_luot_xem) AS tong_luot_xem_thang,  -- Tổng lượt xem trong tháng
                    DATE_FORMAT(luot_xems.ngay_xem, "%Y-%m") AS thang,
                    RANK() OVER (PARTITION BY DATE_FORMAT(luot_xems.ngay_xem, "%Y-%m") ORDER BY SUM(luot_xems.so_luot_xem) DESC) AS `rank`
                FROM
                    phims
                INNER JOIN
                    luot_xems ON luot_xems.id_phim = phims.id
                INNER JOIN
                    chi_tiet_the_loais ON chi_tiet_the_loais.id_phim = phims.id  -- Kết nối bảng chi tiết thể loại
                INNER JOIN
                    the_loais ON chi_tiet_the_loais.id_the_loai = the_loais.id  -- Kết nối bảng thể loại
                LEFT JOIN
                    tap_phims ON tap_phims.id_phim = phims.id  -- Kết nối với bảng tập phim
                LEFT JOIN
                    loai_phims ON loai_phims.id = phims.id_loai_phim  -- Kết nối với loại phim
                WHERE
                    phims.tinh_trang = 1 AND
                    the_loais.tinh_trang = 1 AND
                    loai_phims.tinh_trang = 1
                GROUP BY
                    phims.id, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim, loai_phims.ten_loai_phim, thang
            ) AS ranked_movies
        '))
                ->where('rank', 1)  // Chỉ lấy phim đứng đầu mỗi tháng
                ->groupBy(
                    'ten_phim',
                    'hinh_anh',
                    'slug_phim',
                    'mo_ta',
                    'tong_luot_xem',
                    'so_tap_phim',
                    'ten_loai_phim',
                    'thang'
                )  // Nhóm theo các thông tin cần thiết
                ->orderBy('tong_luot_xem_thang', 'desc')  // Sắp xếp theo tổng lượt xem trong tháng giảm dần
                ->take(6)  // Giới hạn kết quả trả về là 6 phim
                ->get();
            $phim_moi_cap_nhat = DB::table(DB::raw('
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
                        MAX(tap_phims.updated_at) as tap_moi_nhat,  -- Lấy thời gian cập nhật mới nhất của tập phim
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
                    LEFT JOIN
                        tap_phims ON tap_phims.id_phim = phims.id
                    WHERE
                        phims.tinh_trang = 1
                    AND
                        the_loais.tinh_trang = 1
                    AND
                        loai_phims.tinh_trang = 1
                    GROUP BY
                        phims.id, phims.ten_phim, loai_phims.ten_loai_phim, phims.hinh_anh, phims.slug_phim, phims.tong_luot_xem, phims.mo_ta, phims.so_tap_phim
                    HAVING
                        tong_tap > 0
                ) as subquery
            '))
                ->orderBy('tap_moi_nhat', 'DESC') // Sắp xếp theo tập mới cập nhật
                ->take(6)
                ->get();


            $tat_ca_phim = DB::table(DB::raw('
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
                ->take(6)
                ->get();



            $tat_ca_phim_hoan_thanh = DB::table(DB::raw('
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
                phims.id, loai_phims.ten_loai_phim, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim
            HAVING
                tong_tap > 0 AND phims.so_tap_phim = tong_tap
        ) as subquery
    '))
                ->take(6)
                ->get();

            $phim_xem_nhieu_nhat = DB::table(DB::raw('
                (
                    SELECT
                        phims.id,
                        phims.ten_phim,
                        loai_phims.ten_loai_phim,
                        phims.hinh_anh,
                        phims.poster_img,
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
                    GROUP BY
                        phims.id, phims.ten_phim, loai_phims.ten_loai_phim, phims.hinh_anh, phims.slug_phim, phims.tong_luot_xem, phims.mo_ta, phims.so_tap_phim,phims.poster_img
                    HAVING
                        tong_tap > 0
                ) as subquery
            '))
                ->orderBy('tong_luot_xem', 'DESC') // Sắp xếp theo tổng lượt xem giảm dần
                ->take(3) // Lấy 6 phim có lượt xem cao nhất
                ->get();
            $phim_hot = DB::table(DB::raw('
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
                    GROUP BY
                        phims.id, phims.ten_phim, loai_phims.ten_loai_phim, phims.hinh_anh, phims.slug_phim, phims.tong_luot_xem, phims.mo_ta, phims.so_tap_phim
                    HAVING
                        tong_tap > 0
                ) as subquery
            '))
                ->orderBy('tong_luot_xem', 'DESC')
                ->inRandomOrder() // Sắp xếp theo tổng lượt xem giảm dần
                ->take(6) // Lấy 6 phim có lượt xem cao nhất
                ->get();

            return response()->json([
                'phim_hot'                   =>  $phim_hot,
                'phim_moi_cap_nhats'         =>  $phim_moi_cap_nhat,
                'tat_ca_phim'                =>  $tat_ca_phim,
                'top_view_thang'             =>  $top_view_thang,
                'phim_xem_nhieu_nhat'        => $phim_xem_nhieu_nhat,
                'tat_ca_phim_hoan_thanh'     => $tat_ca_phim_hoan_thanh
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function getDataDelist(Request $request)
    {
        $phim = DB::table(DB::raw('
                    (
                     SELECT
                        phims.id,
                        phims.ten_phim,
                        phims.slug_phim,
                        phims.hinh_anh,
                        phims.mo_ta,
                        phims.thoi_gian_chieu,
                        phims.nam_san_xuat,
                        phims.quoc_gia,
                        phims.id_loai_phim,
                        phims.id_the_loai,
                        phims.dao_dien,
                        phims.so_tap_phim,
                        phims.tong_luot_xem,
                        phims.tinh_trang,
                        phims.chat_luong,
                        phims.ngon_ngu,
                        phims.trailer_url,
                        loai_phims.ten_loai_phim,
                        GROUP_CONCAT(DISTINCT the_loais.ten_the_loai SEPARATOR ", ") as ten_the_loais,
                        COUNT(tap_phims.id) as tong_tap
                    FROM phims
                    JOIN chi_tiet_the_loais ON chi_tiet_the_loais.id_phim = phims.id
                    JOIN the_loais ON chi_tiet_the_loais.id_the_loai = the_loais.id
                    JOIN loai_phims ON phims.id_loai_phim = loai_phims.id
                    LEFT JOIN tap_phims ON tap_phims.id_phim = phims.id
                    WHERE phims.slug_phim = :slug_phim
                    AND phims.tinh_trang = 1
                    GROUP BY
                        phims.id,
                        phims.ten_phim,
                        phims.slug_phim,
                        phims.hinh_anh,
                        phims.mo_ta,
                        phims.thoi_gian_chieu,
                        phims.nam_san_xuat,
                        phims.quoc_gia,
                        phims.id_loai_phim,
                        phims.id_the_loai,
                        phims.dao_dien,
                        phims.so_tap_phim,
                        phims.tong_luot_xem,
                        phims.tinh_trang,
                        phims.chat_luong,
                        phims.ngon_ngu,
                        phims.trailer_url,
                        loai_phims.ten_loai_phim
                    HAVING tong_tap > 0
                    LIMIT 1
                )
                    as phim
                '))->setBindings(['slug_phim' => $request->slug])->first();
        $isUserTurmed = false;
        $user = Auth::guard('sanctum')->user();
        $id_khach_hang = $user ? $user->id : null;
        if ($user instanceof \App\Models\KhachHang) {
            $goihientai = HoaDon::where('id_khach_hang', $id_khach_hang)
                ->where('tinh_trang', 1)
                ->where('ngay_bat_dau', '<=', now())
                ->where('ngay_ket_thuc', '>=', now())
                ->latest()
                ->first();

            $isUserTurmed = (bool) $goihientai; // Gán true nếu có gói hợp lệ
        }
        $select5film = DB::table(DB::raw('
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
                    AND
                    phims.slug_phim != :slug_phim
                    GROUP BY
                        phims.id, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim
                    HAVING
                        tong_tap > 0
                ) as subquery
'))->setBindings(['slug_phim' => $request->slug])
            ->take(5)->get();
        $tap = TapPhim::where('id_phim', $phim->id)->firstOrFail();

        return response()->json([
            'phim'          =>  $phim,
            'phim_5_obj'    =>  $select5film,
            'tap'           =>  $tap,
            'isUserTurmed'  =>  $isUserTurmed,
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
    public function sapxepAllPhim($catagory)
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
            GROUP BY
                phims.id, phims.ten_phim, loai_phims.ten_loai_phim, phims.hinh_anh, phims.slug_phim, phims.tong_luot_xem, phims.mo_ta, phims.so_tap_phim
            HAVING
                tong_tap > 0
        ) as subquery
    '))
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
            'response'  =>  $response,
        ]);
    }
    public function watchingFilm($slugMovie, $slugEpisode)
    {
        $the_loai = TheLoai::select('ten_the_loai')
            ->where('tinh_trang', 1)
            ->get();

        $phim = Phim::where('slug_phim', $slugMovie)->firstOrFail();
        $tap_phims = TapPhim::where('id_phim', $phim->id)->orderBy('so_tap', 'ASC')->get();
        $tap = TapPhim::where('slug_tap_phim', $slugEpisode)->firstOrFail();
        return response()->json([
            'the_loai'  =>  $the_loai,
            'phim'      =>  $phim,
            'tap_phims' =>  $tap_phims,
            'tap'       =>  $tap,
        ]);
    }

    public function taoPhim(TaoPhimRequest $request)
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
            if ($request->is_add_url && filter_var($request->hinh_anh, FILTER_VALIDATE_URL)) {
                $filePath = $request->hinh_anh;
            } else {
                if ($request->hasFile('hinh_anh')) {
                    $file = $request->file('hinh_anh');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/admin/phim/thumbail'), $fileName);
                    $filePath = asset('uploads/admin/phim/thumbail/' . $fileName);
                }
            }

            if ($request->is_add_url && filter_var($request->poster_img, FILTER_VALIDATE_URL)) {
                $filePathPoster = $request->poster_img;
            } else {
                if ($request->hasFile('poster_img')) {
                    $file = $request->file('poster_img');
                    $fileNamePoster = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/admin/phim/poster'), $fileNamePoster);
                    $filePathPoster = asset('uploads/admin/phim/poster/' . $fileNamePoster);
                }
            }

            $theloaisArray = explode(',', $request->the_loais);

            $phim = Phim::create([
                'ten_phim'                  => $request->ten_phim,
                'slug_phim'                 => $request->slug_phim,
                'hinh_anh'                  => $filePath,
                'poster_img'                => $filePathPoster,
                'mo_ta'                     => $request->mo_ta,
                'thoi_gian_chieu'           => $request->thoi_gian_chieu,
                'nam_san_xuat'              => $request->nam_san_xuat,
                'quoc_gia'                  => $request->quoc_gia,
                'id_loai_phim'              => $request->id_loai_phim,
                'dao_dien'                  => $request->dao_dien,
                'so_tap_phim'               => $request->so_tap_phim,
                'tinh_trang'                => $request->tinh_trang,
                'ngon_ngu'          => $request->ngon_ngu,
                'trailer_url'               => $request->trailer_url,
                'chat_luong'                => $request->chat_luong,
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
            ChiTietTheLoai::where('id_phim', $id)->delete();
            TapPhim::where('id_phim', $id)->delete();
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

    public function capnhatPhim(CapNhatPhimRequest $request)
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
            $phim = Phim::find($request->id);
            if (!$phim) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy Phim',
                ]);
            }

            if ($request->is_update_url && filter_var($request->hinh_anh, FILTER_VALIDATE_URL)) {
                $filePath = $request->hinh_anh;
            }  else {
                $filePath = $phim->hinh_anh; // Giữ nguyên ảnh cũ nếu không có ảnh mới
                if ($request->hasFile('hinh_anh')) {
                    $file = $request->file('hinh_anh');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/admin/phim/thumbnail'), $fileName);
                    $filePath = asset('uploads/admin/phim/thumbnail/' . $fileName);

                    // Xóa ảnh thumbnail cũ nếu tồn tại
                    if ($phim->hinh_anh && file_exists(public_path('uploads/admin/phim/thumbnail/' . basename($phim->hinh_anh)))) {
                        unlink(public_path('uploads/admin/phim/thumbnail/' . basename($phim->hinh_anh)));
                    }
                }
            }
            if ($request->is_update_url && filter_var($request->poster_img, FILTER_VALIDATE_URL)) {
                $filePathPoster = $request->poster_img;
            } else {
                // Xử lý ảnh Poster
                $filePathPoster = $phim->poster_img; // Giữ nguyên ảnh cũ nếu không có ảnh mới
                if ($request->hasFile('poster_img')) {
                    $filePoster = $request->file('poster_img');
                    $fileNamePoster = time() . '_' . $filePoster->getClientOriginalName();
                    $filePoster->move(public_path('uploads/admin/phim/poster'), $fileNamePoster);
                    $filePathPoster = asset('uploads/admin/phim/poster/' . $fileNamePoster);

                    // Xóa ảnh poster cũ nếu tồn tại
                    if ($phim->poster_img && file_exists(public_path('uploads/admin/phim/poster/' . basename($phim->poster_img)))) {
                        unlink(public_path('uploads/admin/phim/poster/' . basename($phim->poster_img)));
                    }
                }
            }


            // Cập nhật thông tin phim
            $phim->update([
                'ten_phim'           => $request->ten_phim,
                'slug_phim'          => $request->slug_phim,
                'hinh_anh'           => $filePath,
                'poster_img'         => $filePathPoster,
                'mo_ta'              => $request->mo_ta,
                'thoi_gian_chieu'    => $request->thoi_gian_chieu,
                'nam_san_xuat'       => $request->nam_san_xuat,
                'quoc_gia'           => $request->quoc_gia,
                'id_loai_phim'       => $request->id_loai_phim,
                'dao_dien'           => $request->dao_dien,
                'so_tap_phim'        => $request->so_tap_phim,
                'tinh_trang'         => $request->tinh_trang,
                'ngon_ngu'   => $request->ngon_ngu,
                'trailer_url'        => $request->trailer_url,
                'chat_luong'         => $request->chat_luong,
            ]);

            // Cập nhật thể loại
            ChiTietTheLoai::where('id_phim', $phim->id)->delete(); // Xóa các thể loại hiện tại
            $theloaisArray = explode(',', $request->the_loais);
            foreach ($theloaisArray as $value) {
                ChiTietTheLoai::create([
                    'id_phim' => $phim->id,
                    'id_the_loai' => (int) $value,
                ]);
            }


            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành công ' . $request->ten_phim,
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Phim không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiPhim(ThayDoiTrangThaiPhimRequest  $request)
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
            $data = DB::table(DB::raw('
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
        AND
            phims.ten_phim LIKE ?
        GROUP BY
            phims.id, loai_phims.ten_loai_phim, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim
        HAVING
            tong_tap > 0
    ) as subquery
'))
                ->setBindings([$key]) // Bind the key parameter
                ->get();
        }
        return response()->json([
            'phim'  =>  $data,
        ]);
    }
    public function loadTimPhimHome(Request $request)
    {
        $key    = '%' . $request->key . '%';
        if ($request->key == "") {
            $data = [];
        } else {
            $data = DB::table(DB::raw('
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
        AND
            phims.ten_phim LIKE ?
        GROUP BY
            phims.id, loai_phims.ten_loai_phim, phims.ten_phim, phims.hinh_anh, phims.slug_phim, phims.mo_ta, phims.tong_luot_xem, phims.so_tap_phim
        HAVING
            tong_tap > 0
    ) as subquery
'))
                ->setBindings([$key]) // Bind the key parameter
                ->paginate(12);
        }
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
