<?php

namespace App\Http\Controllers;

use App\Http\Requests\DangKyRequest;
use App\Http\Requests\DangNhapRequest;
use App\Http\Requests\DoiPassRequest;
use App\Http\Requests\DoiThongTinRequest;
use App\Http\Requests\QuenMatKhauRequest;
use App\Http\Requests\TaoKhachHangRequest;
use App\Http\Requests\UpdateKhachHangRequest;
use App\Jobs\MailQuenMatKhau as JobsMailQuenMatKhau;
use App\Jobs\MailQueue;
use App\Mail\KichHoatTaiKhoan;
use App\Mail\mailQuenMatKhau;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\PhanQuyen;
use Exception;
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
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
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
        $user   = Auth::guard('sanctum')->user();
        $user = KhachHang::where('id', $user->id)->first();
        return response()->json([
            'status'    => true,
            'obj_user'  => $user,
        ]);
    }
    public function taoKhachHang(TaoKhachHangRequest $request)
    {
        try {
            $id_chuc_nang = 2;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            // Handle file upload
            $filePath = 'https://lh3.googleusercontent.com/a/ACg8ocLquh3rkU8ZbqJlyVij28Ss12yYGqYhzP4MJ29ulErlW-_9lg=s96-c';
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars/users'), $fileName);
                $filePath = asset('uploads/avatars/users/' . $fileName);
            }
            $data               = $request->all();
            $data['password']   = bcrypt($request->password);
            $data['avatar']   = $filePath;
            KhachHang::create($data);
            return response()->json([
                'status'   => true,
                'message'  => 'Thêm Khách Hàng thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá khách hàng không thành công!!'
            ]);
        }
    }
    public function doiThongTin(DoiThongTinRequest $request)
    {
        try {

            KhachHang::where('id', $request->id)
                ->update([
                    'ho_va_ten'     => $request->ho_va_ten,
                    'avatar'        => $request->avatar,
                    'so_dien_thoai' => $request->so_dien_thoai,
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
    public function doiThongTinUser(DoiThongTinRequest $request)
    {
        try {

            KhachHang::where('id', $request->id)
                ->update([
                    'ho_va_ten'     => $request->ho_va_ten,
                    'avatar'        => $request->avatar,
                    'so_dien_thoai' => $request->so_dien_thoai,
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
    public function getDataProfileUser(Request $request)
    {
        $user   = Auth::guard('sanctum')->user();
        $user = KhachHang::where('id', $user->id)->first();
        return response()->json([
            'status'    => true,
            'obj_user'  => $user,
        ]);
    }
    public function doiPassUser(DoiPassRequest $request)
    {

        $check = Auth::guard('khach_hang')->attempt(['email' => $request->email, 'password' => $request->old_pass,]);
        if ($check) {
            $user = Auth::guard('khach_hang')->user();
            $user->update([
                // 'email'                 => $request->email,
                // 'ho_va_ten'             => $request->ho_va_ten,
                'password'              => bcrypt($request->new_pass),
                // 'hinh_anh'              => $request->hinh_anh,
            ]);

            return response()->json([
                'message'   => 'Đổi mật khẩu thành công!!',
                'status'    => true,

            ]);
        } else {
            return response()->json([
                'message'   => 'Mật khẩu cũ không hợp lệ!!',
                'status'    => false
            ]);
        }
    }
    public function doiAvatarUser(Request $request)
    {
        try {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Fetch the current user
                $check = Auth::guard('sanctum')->user();
                $user = KhachHang::where('id', $check->id)->first();

                // Delete old avatar if it exists
                if ($user->avatar) {
                    $oldFilePath = public_path(parse_url($user->avatar, PHP_URL_PATH));
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // Move the new file to the Avatar_user directory
                $file->move(public_path('uploads/avatars/users'), $fileName);
                $filePath = asset('uploads/avatars/users/' . $fileName);

                // Update user avatar path
                $user->avatar = $filePath;
                $user->save();
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Đổi ảnh đại diện thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'đã xảy ra lỗi!'
            ]);
        }
    }
    public function thaydoiTrangThaiKhachHang(Request $request)
    {
        try {
            $id_chuc_nang = 2;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->is_block;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            KhachHang::where('id', $request->id)
                ->update([
                    'is_block'    => $tinh_trang_moi
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
    public function thaydoiKichHoatKhachHang(Request $request)
    {
        try {
            $id_chuc_nang = 2;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->is_active;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            KhachHang::where('id', $request->id)
                ->update([
                    'is_active'    => $tinh_trang_moi
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Kích hoạt thành công!! '
            ]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Kích hoạt không thành công!!'
            ]);
        }
    }
    public function doiPass(DoiPassRequest $request)
    {
        $id_chuc_nang = 2;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $check = Auth::guard('khach_hang')->attempt(['email' => $request->email, 'password' => $request->old_pass,]);
        if ($check) {
            $user = Auth::guard('khach_hang')->user();
            $user->update([
                // 'email'                 => $request->email,
                // 'ho_va_ten'             => $request->ho_va_ten,
                'password'              => bcrypt($request->new_pass),
                // 'hinh_anh'              => $request->hinh_anh,
            ]);

            return response()->json([
                'message'   => 'Đổi mật khẩu thành công!!',
                'status'    => true,

            ]);
        } else {
            return response()->json([
                'message'   => 'Mật khẩu cũ không hợp lệ!!',
                'status'    => false
            ]);
        }
    }
    public function doiAvatar(Request $request)
    {
        try {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Fetch the current user
                $check = Auth::guard('sanctum')->user();
                $user = KhachHang::where('id', $check->id)->first();

                // Delete old avatar if it exists
                if ($user->avatar) {
                    $oldFilePath = public_path(parse_url($user->avatar, PHP_URL_PATH));
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // Move the new file to the Avatar_user directory
                $file->move(public_path('uploads/avatars/users'), $fileName);
                $filePath = asset('uploads/avatars/users/' . $fileName);

                // Update user avatar path
                $user->avatar = $filePath;
                $user->save();
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Đổi ảnh đại diện thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'đã xảy ra lỗi!'
            ]);
        }
    }

    public function timKhachHang(Request $request)
    {
        $id_chuc_nang = 2;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
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
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
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

    public function capnhatKhachHang(UpdateKhachHangRequest $request)
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
            $user = KhachHang::find($request->id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy tài khoan',
                ]);
            }
            $filePath = $user->avatar; // Giữ nguyên đường dẫn ảnh cũ nếu không có file mới được gửi
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars/users'), $fileName);
                $filePath = asset('uploads/avatars/users/' . $fileName);

                // Xóa ảnh cũ nếu có
                if ($user->avatar && file_exists(public_path('KhachHangAdmin/' . basename($user->avatar)))) {
                    unlink(public_path('KhachHangAdmin/' . basename($user->avatar)));
                }
            }
            KhachHang::where('id', $request->id)
                ->update([
                    'ho_va_ten'          => $request->ho_va_ten,
                    'so_dien_thoai'      => $request->so_dien_thoai,
                    'avatar'             => $filePath,
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
        $check = Auth::guard('khach_hang')->attempt([
            'email'         => $request->email,
            'password'      => $request->password
        ]);

        if ($check) {
            $user = Auth::guard('khach_hang')->user();
            if ($user->is_active) {
                if ($user->is_block) {
                    return response()->json([
                        'message'   =>   'Đã đăng nhập thành công!',
                        'status'    =>   true,
                        'token' =>   $user->createToken('token_khach_hang')->plainTextToken,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "tài khoản bạn đã bị khoá!"
                    ]);
                }
            } else {
                Auth::guard('khach_hang')->logout();
                return response()->json([
                    'status' => false,
                    'message' => "Tài khoản chưa được kích hoạt!"
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => "Thông tin đăng nhập không chính xác!"
            ]);
        }
    }

    public function register(DangKyRequest $request)
    {
        $filePath = 'https://lh3.googleusercontent.com/a/ACg8ocLquh3rkU8ZbqJlyVij28Ss12yYGqYhzP4MJ29ulErlW-_9lg=s96-c';
        KhachHang::create([
            'ho_va_ten'   => $request->ho_va_ten,
            'email'       => $request->email,
            'avatar'       => $filePath,
            'password'    => bcrypt($request->password),
        ]);
        return response()->json([
            'message'   => 'Tạo tài khoản thành công!!',
            'status'    =>  true
        ]);
    }
    public function kiemTraQuenMK(Request $request)
    {
        $check  = KhachHang::where('hash_reset', $request->hash_quen_mat_khau)->first();
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
        $khach_hang  = KhachHang::where('hash_active', $request->hash_active)
            ->first();
        if ($khach_hang) {
            $khach_hang->is_active   =   1;
            $khach_hang->hash_active   =   null;
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
            $hash_active                      =   Str::uuid();
            $khach_hang->hash_active   =   $hash_active;
            $khach_hang->save();
            // Gửi Email
            $data['email']  =    $request->email;
            $data['name']  =    $khach_hang->ho_va_ten;
            $data['link']  =    'http://localhost:5173/home/kich-hoat-email/' . $hash_active;
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

        $khach_hang  = KhachHang::where('hash_reset', $request->hash_quen_mat_khau)->first();
        if ($khach_hang) {
            $khach_hang->password             =   bcrypt($request->password);
            $khach_hang->hash_reset   =   null;
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

    public function quenMK(QuenMatKhauRequest $request)
    {
        // Gửi lên 1 thằng duy nhất $request->email
        $khach_hang   = KhachHang::where('email', $request->email)->first();
        if ($khach_hang) {
            // Tạo 1 mã hash_quen_mat_khau
            $hash_pass                      =   Str::uuid();
            $khach_hang->hash_reset   =   $hash_pass;
            $khach_hang->save();
            // Gửi Email
            $data['email']  =    $request->email;
            $data['name']   =    $khach_hang->ho_va_ten;
            $data['link']   =    'http://localhost:5173/home/reset-password/' . $hash_pass;
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
            return response()->json([
                'email'                => $user->email,
                'id_user'              => $user->id,
                'ho_ten_user'          => $user->ho_va_ten,
                'hinh_anh_user'        => $user->avatar,

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

    public function checkUserTerm()
    {
        $userId = Auth::guard('sanctum')->user()->id;

        // Kiểm tra nếu người dùng chưa đăng nhập
        // if (!$userId) {
        //     return response()->json([
        //         'status'  => 0,
        //         'message' => 'Chức năng này yêu cầu đăng nhập!',
        //     ]);
        // }
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
                'status'  => 2,
                'message' => 'Bạn chưa đăng ký gói hoặc gói của bạn đã hết hạn vui lòng mua thêm để tiếp tục!',
            ]);
        }
        return response()->json([
            'status'  => 1,
            'message' => 'Hợp lệ!',
        ]);
    }
}
