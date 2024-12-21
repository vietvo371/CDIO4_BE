<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeechRequest;
use App\Http\Requests\CreateLeechReuqest;
use App\Http\Requests\CreateLeechTapPhimRequest;
use App\Models\ChiTietTheLoai;
use App\Models\LoaiPhim;
use App\Models\Phim;
use App\Models\TapPhim;
use App\Models\TheLoai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class LeechPhimController extends Controller
{
    public function PhimData()
    {
        $id_chuc_nang = 15;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $data = Phim::select('phims.slug_phim')->get();
        return response()->json([
            'status'  =>  true,
            'data'    =>  $data
        ]);
    }
    public function leechStore(CreateLeechReuqest $request)
    {
        $id_chuc_nang = 15;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $request = Http::get("https://ophim1.com/phim/" . $request->slug)->json();
        if ($request['status'] == true) {
            // Get data from JSON
            $movieData = $request['movie'];
            $typeMapping = [
                'single' => 1,  // Phim lẻ
                'series' => 2,  // Phim bộ
                'hoathinh' => 3  // Phim Hoatinh
            ];

            // Determine movie type ID based on the JSON type
            $id_loai_phim = (isset($typeMapping[$movieData['type']]) && !$movieData['chieurap']) ? $typeMapping[$movieData['type']] : 2;



            // Determine movie type ID based on the JSON type
            $id_loai_phim = isset($typeMapping[$movieData['type']]) ? $typeMapping[$movieData['type']] : null;

            // Create the movie
            $check_phim = Phim::where('slug_phim', $movieData['slug'])->first();
            if ($check_phim) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Phim Đã Tồn Tại'
                ]);
            }
            $phim = Phim::create([
                'ten_phim'          => $movieData['name'],
                'slug_phim'         => $movieData['slug'],
                'chat_luong'        => $movieData['quality'],
                'poster_img'        => $movieData['poster_url'],
                'trailer_url'       => empty($movieData['trailer_url']) ? 'https://youtu.be/ox8zEHQBN84' : $movieData['trailer_url'],
                'hinh_anh'          => $movieData['thumb_url'],
                'mo_ta'             => strip_tags($movieData['content']),
                'thoi_gian_chieu'   => (int)preg_replace('/\D/', '', $movieData['time'] ?? '1') ?: 20,
                'nam_san_xuat'      => $movieData['year'],
                'quoc_gia'          => $movieData['country'][0]['name'] ?? 'Đang cập nhật', // Or map country slug if needed
                'id_loai_phim'      => $id_loai_phim,
                'so_tap_phim'       => (int)preg_replace('/\D/', '', $movieData['episode_total'] ?? '1') ?: 1,
                'dao_dien'          => !empty($movieData['director'][0]) ? $movieData['director'][0] : 'Đang cập nhật',
                'tinh_trang'        => 1,
                'ngon_ngu'          => $movieData['lang']
            ]);

            // Map categories based on slug
            if ($phim) {
                foreach ($movieData['category'] as $category) {
                    $categorySlug = $category['slug'];

                    // Find the corresponding category ID in your database by slug
                    $theLoai = TheLoai::where('slug_the_loai', $categorySlug)->first();

                    if ($theLoai) {
                        ChiTietTheLoai::create([
                            'id_phim' => $phim->id,
                            'id_the_loai' => $theLoai->id
                        ]);
                    }
                }
            }

            return response()->json([
                'status'  =>  true,
                'message' =>  'Thêm Phim Thành Công',
            ]);
        } else {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Phim Không Tốn Tại',
            ]);
        }
    }

    public function leechTapPhimStore(CreateLeechTapPhimRequest $request)
    {
        try {
            $id_chuc_nang = 15;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $phim = Phim::where('slug_phim', $request->slug)->first();
            $res = Http::get("https://ophim1.com/phim/" . $request->slug)->json();

            if ($res['status'] == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Phim không tồn tại',
                ]);
            }

            $episodes = $res['episodes'];
            $createdEpisodes = [];

            foreach ($episodes as $episode) {
                // Kiểm tra và lặp qua server_data trong mỗi episode
                foreach ($episode['server_data'] as $data) {
                    // Xác định số tập an toàn
                    if ($res['movie']['type'] == 'single') {
                        $so_tap = 1;
                    } else {
                        // Kiểm tra xem explode có đủ phần tử không
                        $so_tap = (int)preg_replace('/\D/', '', $data['name'] ?? '1') ?: 1;
                    }

                    // Lấy URL, ưu tiên link_embed, nếu không có thì dùng link_m3u8
                    $url = $data['link_embed'] ?? $data['link_m3u8'] ?? null;

                    // Kiểm tra URL có hợp lệ không
                    if (!$url) {
                        return response()->json([
                            'status' => false,
                            'message' => "Không tìm thấy URL hợp lệ cho tập $so_tap"
                        ]);
                    }

                    // Kiểm tra số tập không vượt quá tổng số tập của phim
                    if ($so_tap <= $phim->so_tap_phim) {
                        $slug_tap_phim = 'tap-' . $so_tap . '-' . uniqid();
                        TapPhim::updateOrCreate(
                            [
                                'id_phim' => $phim->id,
                                'url' => $url
                            ],
                            [
                                'slug_tap_phim' => $slug_tap_phim,
                                'so_tap' => $so_tap,
                                'tinh_trang' => 1 // trạng thái phù hợp
                            ]
                        );
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => "Số tập $so_tap vượt quá số lượng tập của phim"
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Các tập phim đã được thêm thành công',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Xảy ra lỗi trong quá trình tạo tập phim'
            ]);
        }
    }
    public function xoaPhim(Request $request)
    {
        try {
            $id_chuc_nang = 15;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $phim = Phim::where('slug_phim', $request->slug)->first();
            if ($phim->hinh_anh && file_exists(public_path('uploads/admin/phim/' . basename($phim->hinh_anh)))) {
                unlink(public_path('uploads/admin/phim/' . basename($phim->hinh_anh)));
            }
            ChiTietTheLoai::where('id_phim', $phim->id)->delete();
            TapPhim::where('id_phim', $phim->id)->delete();
            Phim::where('id', $phim->id)->delete();
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
}
