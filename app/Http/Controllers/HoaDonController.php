<?php

namespace App\Http\Controllers;

use App\Models\GoiVip;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HoaDonController extends Controller
{

    public function getDataHoaDon()
    {
        $id_chuc_nang = 17;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dataAdmin = HoaDon::join('goi_vips', 'hoa_dons.id_goi', 'goi_vips.id')
            ->join('khach_hangs', 'hoa_dons.id_khach_hang', 'khach_hangs.id')
            ->select('hoa_dons.*', 'goi_vips.ten_goi', 'khach_hangs.ho_va_ten')
            ->paginate(8);
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
            'data' => $response
        ]);
    }
    public function getTrensactionOpen(Request $request)
    {
        $user = $this->isUser();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin người dùng.'
            ]);
        }

        $data = HoaDon::leftJoin('goi_vips', 'hoa_dons.id_goi', 'goi_vips.id')
            ->where('hoa_dons.id_khach_hang', $user->id)
            ->where('hoa_dons.tinh_trang', 1)
            ->select('hoa_dons.*', 'goi_vips.ten_goi')
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Không có dữ liệu.'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
    public function getDataCheckOut(Request $request)
    {
        $goi = GoiVip::where('tinh_trang', 1)->find($request->id_goi);
        if (!$goi) {
            return response()->json([
                'status' => false,
                'message' => 'Gói VIP không tồn tại hoặc không khả dụng.'
            ]);
        }
        $user = $this->isUser();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin người dùng.'
            ]);
        }
        $check = false;
        $existingVip = HoaDon::where('id_khach_hang', $user->id)
            ->where('ngay_ket_thuc', '>=', Carbon::now('Asia/Ho_Chi_Minh'))
            ->where('tinh_trang', 1) // Đã thanh toán
            ->first();

        if ($existingVip) {
            $check = true;
        }

        return response()->json([
            'status' => true,
            'user'   => $user,
            'goi'    => $goi,
            'check'  => $check,
        ]);
    }
    public function getQrPayMent(Request $request)
    {
        $goi = GoiVip::where('tinh_trang', 1)->find($request->id_goi);
        if (!$goi) {
            return response()->json([
                'status' => false,
                'message' => 'Gói VIP không tồn tại hoặc không khả dụng.'
            ]);
        }

        $user = $this->isUser();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin người dùng.'
            ]);
        }

        // Kiểm tra xem user đã có gói VIP hoạt động chưa
        $existingVip = HoaDon::where('id_khach_hang', $user->id)
            ->where('ngay_ket_thuc', '>=', Carbon::now('Asia/Ho_Chi_Minh'))
            ->where('tinh_trang', 1) // Đã thanh toán
            ->first();

        if ($existingVip) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn đã có gói VIP đang hoạt động. Vui lòng chờ đến khi hết hạn để đăng ký gói mới.'
            ]);
        }

        $hoaDon = $this->createHoaDon($goi, $user);
        $link = $this->generatePaymentLink($hoaDon);

        return response()->json([
            'status' => true,
            'link' => $link,
            'hoaDon' => $hoaDon,
            'user' => $user,
        ]);
    }

    private function createHoaDon($goi, $user)
    {
        $hoaDon = HoaDon::create([
            'id_goi'        => $goi->id,
            'id_khach_hang' => $user->id,
            'tong_tien'     => $goi->tien_sale > 0 ? $goi->tien_sale : $goi->tien_goc,
            'ngay_bat_dau'  => Carbon::now('Asia/Ho_Chi_Minh'),
            'ngay_ket_thuc' => Carbon::now('Asia/Ho_Chi_Minh')->addMonths($goi->thoi_han),
            'tinh_trang'    => 0, // Mặc định là chưa thanh toán
        ]);

        $hoaDon->ma_hoa_don = 'HD0' . substr(md5($hoaDon->id . time()), 0, 5);
        $hoaDon->save();

        return $hoaDon;
    }

    private function generatePaymentLink($hoaDon)
    {
        return 'https://img.vietqr.io/image/mb-0708585120-compact2.jpg?amount='
            . $hoaDon->tong_tien
            . '&addInfo=' . $hoaDon->ma_hoa_don
            . '&accountName=VO_VAN_VIET';
    }
}
