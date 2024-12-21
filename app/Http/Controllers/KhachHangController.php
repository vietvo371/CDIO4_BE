<?php

namespace App\Http\Controllers;

use App\Http\Requests\DangKyRequest;
use App\Http\Requests\DangNhapRequest;
use App\Jobs\MailQuenMatKhau as JobsMailQuenMatKhau;
use App\Jobs\MailQueue;
use App\Mail\KichHoatTaiKhoan;
use App\Mail\mailQuenMatKhau;
use App\Models\KhachHang;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class KhachHangController extends Controller
{
    public function getData()
    {
        $id_chuc_nang = 2;
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
        $dataAdmin   = KhachHang::select('khach_hangs.*')
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
            'khach_hang'  =>  $response,
        ]);
    }
    public function getDataProfile(Request $request)
    {
        $user = KhachHang::where('id', $request->id_khach_hang)->first();
        return response()->json([
            'obj_user'  => $user,
        ]);
    }
    public function taoKhachHang(Request $request)
    {
        try {
            $id_chuc_nang = 2;
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
            KhachHang::create([
                'email'         => $request->email,
                'ho_va_ten'     => $request->ho_va_ten,
                'password'      => bcrypt($request->password),
                'hinh_anh'      => $request->hinh_anh,
                'ngay_sinh'     => $request->ngay_sinh,
                'is_done'       => $request->is_done,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm khách hàng thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá khách hàng không thành công!!'
            ]);
        }
    }
    public function doiThongTin(Request $request)
    {
        try {
            KhachHang::where('id', $request->id)
                ->update([
                    'email'         => $request->email,
                    'ho_va_ten'     => $request->ho_va_ten,
                    'password'      => bcrypt($request->password),
                    'hinh_anh'      => $request->hinh_anh,
                    'ngay_sinh'     => $request->ngay_sinh,
                    'is_done'       => $request->is_done,
                ]);

            return response()->json([
                'status'     => true,
                'ho_ten_user' => $request->ho_va_ten,
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
    public function doiPass(Request $request)
    {
        $check = Auth::guard('khach_hang')->attempt(['email' => $request->email, 'password' => $request->old_pass,]);
        if ($check) {
            $user = Auth::guard('khach_hang')->user();
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

    public function timKhachHang(Request $request)
    {
        $key    = '%' . $request->key . '%';
        $dataAdmin   = KhachHang::select('khach_hangs.*')
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
                'khach_hang'  =>  $response,
            ]);
    }
    public function xoaKhachHang($id)
    {
        try {
            $id_chuc_nang = 2;
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
            KhachHang::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá khách hàng thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá khách hàng không thành công!!'
            ]);
        }
    }

    public function capnhatKhachHang(Request $request)
    {
        try {
            $id_chuc_nang = 2;
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
            KhachHang::where('id', $request->id)
                ->update([
                    'email'                 => $request->email,
                    'ho_va_ten'             => $request->ho_va_ten,
                    'password'              => ($request->password),
                    'hinh_anh'              => $request->hinh_anh,
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


    public function login(DangNhapRequest $request)
    {
        $check = Auth::guard('khach_hang')->attempt(['email' => $request->email, 'password' => $request->password, 'is_done'    => 1]);
        if ($check) {
            $user = Auth::guard('khach_hang')->user();
            return response()->json([
                'message'   => 'Đăng nhập thành công!!',
                'status'    => true,
                'token'     => $user->createToken('api-token-khach')->plainTextToken,

            ]);
        } else {
            return response()->json([
                'message'   => 'Tài khoản của bạn chưa kích hoạt!!',
                'status'    => false
            ]);
        }
    }

    public function register(DangKyRequest $request)
    {
        KhachHang::create([
            'email'         => $request->email,
            'ho_va_ten'     => $request->ho_va_ten,
            'password'      => bcrypt($request->password),
            'hinh_anh'      => $request->hinh_anh,
            'ngay_sinh'     => $request->ngay_sinh,
            'is_done'       => $request->is_done,

        ]);
        return response()->json([
            'message'   => 'Tạo tài khoản thành công!!',
            'status'    =>  true
        ]);
    }
    public function kiemTraQuenMK(Request $request)
    {
        $check  = KhachHang::where('hash_quen_mat_khau', $request->hash_quen_mat_khau)->first();
        if ($check) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Vui lòng đặt lại mật khẩu!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Bạn không được đặt lại mật khẩu!',
            ]);
        }
    }
    public function kiemTraHashLogin(Request $request)
    {
        $khach_hang  = KhachHang::where('hash_kich_hoat', $request->hash_kich_hoat)
            ->first();
        if ($khach_hang) {
            $khach_hang->is_done   =   1;
            $khach_hang->hash_kich_hoat   =   null;
            $khach_hang->save();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Kích hoạt email thành công!',
                'email'             =>   $khach_hang->email,
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Bạn không được ở đây!',
            ]);
        }
    }
    public function kichHoatTK(Request $request)
    {
        // Gửi lên 1 thằng duy nhất $request->email
        $khach_hang   = KhachHang::where('email', $request->email)->first();
        if ($khach_hang) {
            // Tạo 1 mã hash_kich_hoat
            $hash_kich_hoat                      =   Str::uuid();
            $khach_hang->hash_kich_hoat   =   $hash_kich_hoat;
            $khach_hang->save();
            // Gửi Email
            $data['email']  =    $request->email;
            $data['name']  =    $khach_hang->ho_va_ten;
            $data['link']  =    'http://localhost:5173/home/kich-hoat-email/' . $hash_kich_hoat;
            MailQueue::dispatch($data);

            return response()->json([
                'status'            =>   true,
                'message'           =>   'Vui lòng kiểm tra email của bạn để kích hoạt!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tài khoản của bạn không tồn tại!',
            ]);
        }
    }
    public function datLaiMK(Request $request)
    {

        $khach_hang  = KhachHang::where('hash_quen_mat_khau', $request->hash_quen_mat_khau)->first();
        if ($khach_hang) {
            $khach_hang->password             =   bcrypt($request->password);
            $khach_hang->hash_quen_mat_khau   =   null;
            $khach_hang->save();

            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã đổi mật khẩu thành công!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Bạn không được phép ở đây!',
            ]);
        }
    }

    public function quenMK(Request $request)
    {
        // Gửi lên 1 thằng duy nhất $request->email
        $khach_hang   = KhachHang::where('email', $request->email)->first();
        if ($khach_hang) {
            // Tạo 1 mã hash_quen_mat_khau
            $hash_pass                      =   Str::uuid();
            $khach_hang->hash_quen_mat_khau   =   $hash_pass;
            $khach_hang->save();
            // Gửi Email
            $data['email']  =    $request->email;
            $data['name']  =    $khach_hang->ho_va_ten;
            $data['link']  =    'http://localhost:5173/home/reset-password/' . $hash_pass;
            JobsMailQuenMatKhau::dispatch($data);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Vui lòng kiểm tra email của bạn để đổi lại mật khẩu!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tài khoản của bạn không tồn tại!',
            ]);
        }
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
                'email'                => $user->email,
                'id_user'              => $user->id,
                'ho_ten_user'          => $user->ho_va_ten,
                'hinh_anh_user'        => $user->hinh_anh,
                'list'                 => $user->tokens,

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
                'message'    => 'Xoá Nha token không thành công!!'
            ]);
        }
    }
}
