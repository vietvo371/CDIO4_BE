<?php

namespace App\Http\Middleware;

use App\Models\HoaDon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkUserTerm
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Auth::guard('sanctum')->user()->id;

        // Kiểm tra nếu người dùng chưa đăng nhập
        if (!$userId) {
            return response()->json([
                'status'  => 0,
                'message' => 'Chức năng này yêu cầu đăng nhập!',
            ]);
        }

        // Tìm gói dịch vụ hợp lệ cho người dùng hiện tại
        $goihientai = HoaDon::where('id_khach_hang', $userId)
            ->where('tinh_trang', 1)
            ->where('ngay_bat_dau', '<=', now())
            ->where('ngay_ket_thuc', '>=', now())
            ->latest()
            ->first();

        // Kiểm tra nếu người dùng không có gói hợp lệ
        if (!$goihientai) {
            return response()->json([
                'status'  => 0,
                'message' => 'Bạn chưa đăng ký gói hoặc gói của bạn đã hết hạn vui lòng mua thêm để tiếp tục!',
            ]);
        }

        // Nếu người dùng có gói hợp lệ, tiếp tục xử lý request
        return $next($request);
    }
}
