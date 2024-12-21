<?php

namespace App\Http\Controllers;

use App\Models\AdminAnime;
use App\Models\ChucVu;
use App\Models\PhanQuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class AdminAnimeController extends Controller
{
    public function getData()
    {
        $id_chuc_nang = 1;
        $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
        $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
        $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $chuc_vu_admin   = ChucVu::select('chuc_vus.*')
            ->get(); // get là ra 1  sách
        $dataAdmin   = AdminAnime::join('chuc_vus', 'id_chuc_vu', 'chuc_vus.id')
            ->select('admin_animes.*', 'chuc_vus.ten_chuc_vu')
            ->paginate(6); // get là ra 1  sách
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
            'admin'  =>  $response,
            'chuc_vu_admin'  =>  $chuc_vu_admin,
        ]);
    }
    public function getDataProfile(Request $request)
    {
        $user = AdminAnime::join('chuc_vus', 'id_chuc_vu', 'chuc_vus.id')
            ->select('admin_animes.*', 'chuc_vus.ten_chuc_vu')
            ->where('admin_animes.id', $request->id_admin)
            ->first();
        return response()->json([
            'obj_admin'  => $user,
        ]);
    }
    public function doiPass(Request $request)
    {
        $check = Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->old_pass,]);
        if ($check) {
            $user = Auth::guard('admin')->user();
            $user->update([
                'email'                 => $request->email,
                'ho_va_ten'             => $request->ho_va_ten,
                'password'              => bcrypt($request->new_pass),
                'hinh_anh'              => $request->hinh_anh,
            ]);

            return response()->json([
                'message'   => 'Đổi mật khẩu thành công!!',
                'status'    => true,

            ]);
        } else {
            return response()->json([
                'message'   => 'Mật khẩu cũ không hợp lệ!!',
                'status'    => 'false'
            ]);
        }
    }
    public function doiThongTin(Request $request)
    {
        try {
            AdminAnime::where('id', $request->id)
                ->update([
                    'email'                 => $request->email,
                    'ho_va_ten'             => $request->ho_va_ten,
                    'password'              => ($request->password),
                    'hinh_anh'              => $request->hinh_anh,
                    'id_chuc_vu'            => $request->id_chuc_vu,
                ]);

            return response()->json([
                'status'     => true,
                'ho_ten'     => $request->ho_va_ten,
                'message'    => 'Cập nhật tài khoản thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập nhật tài khoản không thành công!!'
            ]);
        }
    }
    public function taoAdmin(Request $request)
    {
        try {
            $id_chuc_nang = 1;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->first();
            if (!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            AdminAnime::create([
                'email'                 => $request->email,
                'ho_va_ten'             => $request->ho_va_ten,
                'password'              => bcrypt($request->password),
                'hinh_anh'              => $request->hinh_anh,
                'id_chuc_vu'            => $request->id_chuc_vu,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm admin thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá admin không thành công!!'
            ]);
        }
    }

    public function timAdmin(Request $request)
    {
        $key    = '%' . $request->key . '%';
        $chuc_vu_admin   = ChucVu::select('chuc_vus.*')
            ->get(); // get là ra 1  sách
        $dataAdmin   = AdminAnime::join('chuc_vus', 'id_chuc_vu', 'chuc_vus.id')
            ->select('admin_animes.*', 'chuc_vus.ten_chuc_vu')
            ->where('ho_va_ten', 'like', $key)
            ->paginate(6); // get là ra 1  sách
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
            'admin'  =>  $response,
            'chuc_vu_admin'  =>  $chuc_vu_admin,
        ]);
    }
    public function xoaAdmin($id)
    {
        try {
            $id_chuc_nang = 1;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->first();
            if (!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            AdminAnime::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá Admin thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Admin không thành công!!'
            ]);
        }
    }

    public function capnhatAdmin(Request $request)
    {
        try {
            $id_chuc_nang = 1;
            $user   = Auth::guard('sanctum')->user(); // Chính là người đang login
            $user_chuc_vu   = $user->id_chuc_vu;    // Giả sử
            $check  = PhanQuyen::where('id_chuc_vu', $user_chuc_vu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->first();
            if (!$check) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            AdminAnime::where('id', $request->id)
                ->update([
                    'email'                 => $request->email,
                    'ho_va_ten'             => $request->ho_va_ten,
                    'password'              => ($request->password),
                    'hinh_anh'              => $request->hinh_anh,
                    'id_chuc_vu'            => $request->id_chuc_vu,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật thành ' . $request->email,
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Admin không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiAdmin(Request $request)
    {

        try {
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            AdminAnime::where('id', $request->id)
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
    public function login(Request $request)
    {
        $check = Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password,]);
        if ($check) {
            $user = Auth::guard('admin')->user();
            return response()->json([
                'message'   => 'Đăng Nhập thành công!!',
                'status'    => true,
                'token'     => $user->createToken('api-token-admin')->plainTextToken,

            ]);
        } else {
            return response()->json([
                'message'   => 'Đăng Nhập không  thành công!!',
                'status'    => 'false'
            ]);
        }
    }

    public function register(Request $request)
    {
        AdminAnime::create([
            'email'         => $request->email,
            'ho_va_ten'     => $request->ho_va_ten,
            'password'      => bcrypt($request->password),
            'hinh_anh'      => $request->hinh_anh,
            'ngay_sinh'      => $request->ngay_sinh,
        ]);
        return response()->json([
            'message'   => 'Tạo tài khoản thành công!!',
            'status'    =>  true
        ]);
    }

    public function check(Request $request)
    {

        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $agent   = new Agent();
            $device  = $agent->device();
            $os      = $agent->platform();
            $browser = $agent->browser();
            DB::table('personal_access_tokens')
                ->where('id', $user->currentAccessToken()->id)
                ->update([
                    'ip'            =>  request()->ip(),
                    'device'        =>  $device,
                    'os'            =>  $os,
                    'trinh_duyet'   =>  $browser
                ]);
            return response()->json([
                'email'      => $user->email,
                'ho_ten'     => $user->ho_va_ten,
                'hinh_anh'   => $user->hinh_anh,
                'id_admin'   => $user->id,
                'list'       => $user->tokens,

            ], 200);
        } else {
            return response()->json([
                'message'   => 'Bạn cần đăng nhập hệ thống !!',
                'status'    => false
            ], 401);
        }
    }
    public function xoatoken($id)
    {
        try {
            DB::table('personal_access_tokens')
                ->where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá token thành công!!'
            ]);
        } catch (ExceptionEvent  $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá token không thành công!!'
            ]);
        }
    }
}
