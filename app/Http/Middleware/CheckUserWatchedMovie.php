<?php

namespace App\Http\Middleware;

use App\Models\BinhLuanPhim;
use App\Models\LuotPhim;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserWatchedMovie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện chức năng này.'
            ], 401);
        }

        // Kiểm tra nếu người dùng đã xem phim
        $watched = LuotPhim::where('id_khach_hang', $user->id)
            ->where('id_phim', $request->id_phim) // Phim mà họ muốn bình luận
            ->exists();

        if (!$watched) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn cần xem phim trước khi bình luận.'
            ]);
        }
        // Giới hạn số bình luận trong 1 giờ
        $commentCount = BinhLuanPhim::where('id_khach_hang', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($commentCount >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn đã đăng quá nhiều bình luận. Vui lòng chờ để tiếp tục.'
            ]);
        }

        return $next($request);
    }
}
