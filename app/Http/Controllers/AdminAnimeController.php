<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\DoiPassAdminReuqest;
use App\Http\Requests\DoiPassRequest;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Requests\ToggleAdminStatusRequest;
use App\Http\Requests\UpdateAdminRequest;
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
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
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
        $admin = Auth::guard('sanctum')->user();

        $user = AdminAnime::join('chuc_vus', 'id_chuc_vu', 'chuc_vus.id')
            ->select('admin_animes.*', 'chuc_vus.ten_chuc_vu')
            ->where('admin_animes.id', $admin->id)
            ->first();
        return response()->json([
            'obj_admin'  => $user,
            // 'admin'  => $admin,
        ]);
    }
    public function doiPass(DoiPassAdminReuqest $request)
    {
        $id_chuc_nang = 1;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $dangLogin = $this->isAdmin();

        $admin = AdminAnime::find($request->id);

        if ($admin->is_master == 1 && $admin->id != $dangLogin->id) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không thể cập nhật Tài Khoản Có Quyền Hạn Cao'
            ]);
        }
        AdminAnime::where('id', $request->id)
            ->update([
                'password'              => bcrypt($request->password),
            ]);
        return response()->json([
            'status'  =>  true,
            'message' =>  'Đổi mật khẩu thành công'
        ]);
    }
    public function doiPassProfile(DoiPassRequest $request)
    {
        $check = Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->old_pass,]);
        if ($check) {
            $user = Auth::guard('admin')->user();
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
    public function doiThongTin(Request $request)
    {
        try {
            AdminAnime::where('id', $request->id)
                ->update([
                    'email' => $request->email,
                    'ho_va_ten' => $request->ho_va_ten,
                    'so_dien_thoai' => $request->so_dien_thoai,
                ]);

            $user = AdminAnime::where('id', $request->id)->first();
            // $user = Auth::guard('sanctum')->user();
            return response()->json([
                'status'     => true,
                'message'    => 'Cập nhật tài khoản thành công!',
                'name_admin'    => $user->ho_va_ten,
                'avt_admin'     => $user->hinh_anh,
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập nhật tài khoản không thành công!!'
            ]);
        }
    }
    public function doiAvatar(Request $request)
    {
        try {
            $filePath = null;

            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();

                $check = Auth::guard('sanctum')->user();
                $admin = AdminAnime::where('id', $check->id)->first();

                if ($admin->hinh_anh) {
                    $oldFilePath = public_path(parse_url($admin->hinh_anh, PHP_URL_PATH));
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);  // Delete the old file
                    }
                }
                $file->move(public_path('uploads/avatars/admins'), $fileName);
                $filePath = asset('uploads/avatars/admins/' . $fileName);

                $admin->hinh_anh = $filePath;
                $admin->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật ảnh đại diện thành công!',
                'avatar' => $admin->hinh_anh,
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'đã xảy ra lỗi!'
            ]);
        }
    }
    public function taoAdmin(CreateAdminRequest  $request)
    {
        try {
            $id_chuc_nang = 1;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            // Handle file upload
            $filePath = 'https://lh3.googleusercontent.com/a/ACg8ocLquh3rkU8ZbqJlyVij28Ss12yYGqYhzP4MJ29ulErlW-_9lg=s96-c';
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars/admins'), $fileName);
                $filePath = asset('uploads/avatars/admins/' . $fileName);
            }
            $data               = $request->all();
            $data['password']   = bcrypt('123456');
            $data['hinh_anh']   = $filePath;
            AdminAnime::create($data);
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
        $id_chuc_nang = 1;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
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
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $admin = AdminAnime::find($id);

            if ($admin->is_master == 1) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không thể cập nhật Tài Khoản Có Quyền Hạn Cao'
                ]);
            }
            AdminAnime::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'xoá Admin thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Xoá Admin không thành công!!'
            ]);
        }
    }

    public function capnhatAdmin(UpdateAdminRequest $request)
    {
        try {
            $id_chuc_nang = 1;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $dangLogin = $this->isAdmin();

            $admin = AdminAnime::find($request->id);

            if ($admin->is_master == 1 && $admin->id != $dangLogin->id) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không thể cập nhật Tài Khoản Có Quyền Hạn Cao'
                ]);
            }

            // Xác định nếu hinh_anh là file upload hoặc URL
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/avatars/admins'), $fileName);
                $filePath = asset('uploads/avatars/admins/' . $fileName);

                // Xóa ảnh cũ nếu có
                if ($admin->hinh_anh && file_exists(public_path('uploads/avatars/admins/' . basename($admin->hinh_anh)))) {
                    unlink(public_path('uploads/avatars/admins/' . basename($admin->hinh_anh)));
                }
            } else {
                $filePath = $request->hinh_anh; // Nếu không phải file, dùng URL hiện có
            }

            AdminAnime::where('id', $request->id)
                ->update([
                    'ho_va_ten'             => $request->ho_va_ten,
                    'hinh_anh'              => $filePath,
                    'id_chuc_vu'            => $request->id_chuc_vu,
                    'so_dien_thoai'         => $request->so_dien_thoai,
                    'tinh_trang'            => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Cập Nhật thành công ',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật Admin không thành công!!'
            ]);
        }
    }


    public function thaydoiTrangThaiAdmin(ToggleAdminStatusRequest $request)
    {

        try {
            $id_chuc_nang = 1;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $admin = AdminAnime::find($request->id);
            if ($admin->is_master == 1) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không thể cập nhật Tài Khoản Có Quyền Hạn Cao'
                ]);
            }
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
    public function login(LoginAdminRequest $request)
    {
        $check = Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password,]);
        if ($check) {
            $user = Auth::guard('admin')->user();
            if ($user->tinh_trang) {
                $chuc_vu = ChucVu::find($user->id_chuc_vu);
                return response()->json([
                    'message'       => 'Đã đăng nhập thành công!',
                    'status'        =>  true,
                    'token'         =>  $user->createToken('token_admin')->plainTextToken,
                    'name_admin'    =>  $user->ho_va_ten,
                    'avt_admin'     => $user->hinh_anh,
                    'chuc_vu'       => $chuc_vu->ten_chuc_vu
                ]);
            } else {
                Auth::guard('admin')->logout();
                if ($user->tinh_trang == 0) {
                    return response()->json([
                        'status' => false,
                        'message' => "Tài khoản của bạn đã bị khóa!"
                    ]);
                }
            }
        } else {
            return response()->json([
                'message'   => 'Thông tin đăng nhập không chính xác!!',
                'status'    => false
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = $this->isAdmin();
        if ($user) {
            DB::table('personal_access_tokens')
                ->where('id', $user->currentAccessToken()->id)->delete();

            return response()->json([
                'message'   => 'Đăng xuất thành công!!',
                'status'    => true
            ]);
        }
        return response()->json([
            'message'   => 'Bạn chưa đăng nhập tài khoản admin!',
            'status'    => false
        ]);
    }
    public function logoutAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $tokens = $user->tokens;
            foreach ($tokens as $key => $value) {
                $value->delete();
            }

            return response()->json([
                'message'   =>  'Đã đăng xuất tất cả thành công!',
                'status'    =>  true,
            ]);
        } else {
            return response()->json([
                'message'   =>  'Bạn cần đăng nhập hệ thống',
                'status'    =>  false,
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
            return response()->json([
                'email'      => $user->email,
                'ho_ten'     => $user->ho_va_ten,
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
                'message'    => 'xoá token thành công!!'
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
