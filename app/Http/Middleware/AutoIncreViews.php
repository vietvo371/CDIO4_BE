<?php

namespace App\Http\Middleware;

use App\Jobs\IncreaseViewCount;
use App\Models\Phim;
use App\Models\TapPhim;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoIncreViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slugMovie = $request->slugMovie;
        $slugEpisode = $request->slugEpisode;
        $user = Auth::guard('sanctum')->user();

        $phim = Phim::where('slug_phim', $slugMovie)->first();
        $tap = TapPhim::where('slug_tap_phim', $slugEpisode)->first();

        if ($phim && $tap) {
            // Dispatch Job để tăng lượt xem trong hàng đợi
            IncreaseViewCount::dispatch($phim->id, $tap->id,$user->id);
        }

        return $next($request);
    }
}
