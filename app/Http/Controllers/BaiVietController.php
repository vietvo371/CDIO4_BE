<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatBaiVietRequest;
use App\Http\Requests\TaoBaiVietRequest;
use App\Http\Requests\ThayDoiTrangThaiBaiVietRequest;
use App\Models\BaiViet;
use App\Models\ChuyenMuc;
use App\Models\PhanQuyen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class BaiVietController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $id_chuc_nang = 10;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $chuyen_muc_admin   = ChuyenMuc::select('chuyen_mucs.*')
            ->get();
        $dataAdmin   = BaiViet::join('chuyen_mucs', 'id_chuyen_muc', 'chuyen_mucs.id')
            ->select('bai_viets.*', 'chuyen_mucs.ten_chuyen_muc')
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
            'bai_viet_admin'  =>  $response,
            'chuyen_muc_admin'  =>  $chuyen_muc_admin,
        ]);
    }
    public function getDataHome()
    {
        $chuyenmuc   = ChuyenMuc::where('chuyen_mucs.tinh_trang', 1)->select('chuyen_mucs.*')->take(1)->first();
        $data   = BaiViet::join('chuyen_mucs', 'id_chuyen_muc', 'chuyen_mucs.id')
            ->where('bai_viets.tinh_trang', 1)
            ->where('chuyen_mucs.tinh_trang', $chuyenmuc->id)
            ->where('id_chuyen_muc', 1)
            ->select('bai_viets.*', 'chuyen_mucs.ten_chuyen_muc')
            ->orderBy('id', 'DESC') // sắp xếp giảm dần
            ->paginate(6); // get là ra 1  sách
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'dataAdmin' => $data
        ];
        return response()->json([
            'bai_viet'        =>  $response,
        ]);
    }
    public function changeChuyenMuc(Request $request)
    {
        $data   = BaiViet::join('chuyen_mucs', 'id_chuyen_muc', 'chuyen_mucs.id')
            ->where('bai_viets.tinh_trang', 1)
            ->where('chuyen_mucs.tinh_trang', 1)
            ->where('id_chuyen_muc', $request->id_chuyen_muc)
            ->select('bai_viets.*', 'chuyen_mucs.ten_chuyen_muc')
            // ->orderBy('id', 'DESC') // sắp xếp giảm dần
            ->paginate(6); // get là ra 1  sách
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'dataAdmin' => $data
        ];
        return response()->json([
            'bai_viet'        =>  $response,
        ]);
    }
    public function getDelistBlog(Request $request)
    {
        $data   = BaiViet::join('chuyen_mucs', 'id_chuyen_muc', 'chuyen_mucs.id')
            ->where('bai_viets.tinh_trang', 1)
            ->where('chuyen_mucs.tinh_trang', 1)
            ->where('bai_viets.slug_tieu_de', $request->slug)
            ->select('bai_viets.*', 'chuyen_mucs.ten_chuyen_muc')
            ->first(); // get là ra 1 danh sách

        return response()->json([
            'bai_viet'        =>  $data,
        ]);
    }




    public function taoBaiViet(TaoBaiVietRequest $request)
    {
        try {
            $id_chuc_nang = 10;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/admin/blogs'), $fileName);
                $filePath = asset('uploads/admin/blogs/' . $fileName);
            }
            BaiViet::create([
                'tieu_de'               => $request->tieu_de,
                'slug_tieu_de'          => $request->slug_tieu_de,
                'hinh_anh'              => $filePath,
                'mo_ta'                 => $request->mo_ta,
                'mo_ta_chi_tiet'        => $request->mo_ta_chi_tiet,
                'id_chuyen_muc'         => $request->id_chuyen_muc,
                'tinh_trang'            => $request->tinh_trang,
            ]);
            return response()->json([
                'status'   => true,
                'message'  => 'Bạn thêm bài viết thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Đã xảy ra lỗi'
            ]);
        }
    }

    public function timBaiViet(Request $request)
    {
        $id_chuc_nang = 10;
        $check = $this->checkQuyen($id_chuc_nang);
        if ($check == false) {
            return response()->json([
                'status'  =>  false,
                'message' =>  'Bạn không có quyền chức năng này'
            ]);
        }
        $key    = '%' . $request->key . '%';
        $dataAdmin   = BaiViet::select('bai_viets.*')
            ->where('tieu_de', 'like', $key)
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
            'bai_viet_admin'  =>  $response,
        ]);
    }
    public function xoaBaiViet($id)
    {
        try {
            $id_chuc_nang = 10;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $BaiViet = BaiViet::where('id', $id)->first();
            if ($BaiViet->hinh_anh && file_exists(public_path('uploads/admin/blogs/' . basename($BaiViet->hinh_anh)))) {
                unlink(public_path('uploads/admin/blogs/' . basename($BaiViet->hinh_anh)));
            }
            BaiViet::where('id', $id)->delete();

            return response()->json([
                'status'     => true,
                'message'    => 'Đã xoá bài viết thành công!!'
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Đã xảy ra lỗi'
            ]);
        }
    }

    public function capnhatBaiViet(CapNhatBaiVietRequest $request)
    {
        try {
            $id_chuc_nang = 10;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $baiViet = BaiViet::find($request->id);

            if (!$baiViet) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy bài viết',
                ]);
            }

            $filePath = $baiViet->hinh_anh; // Giữ nguyên đường dẫn ảnh cũ nếu không có file mới được gửi
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/admin/blogs'), $fileName);
                $filePath = asset('uploads/admin/blogs/' . $fileName);

                // Xóa ảnh cũ nếu có
                if ($baiViet->hinh_anh && file_exists(public_path('uploads/admin/blogs/' . basename($baiViet->hinh_anh)))) {
                    unlink(public_path('uploads/admin/blogs/' . basename($baiViet->hinh_anh)));
                }
            }
            BaiViet::where('id', $request->id)
                ->update([
                    'tieu_de'               => $request->tieu_de,
                    'slug_tieu_de'           => $request->slug_tieu_de,
                    'hinh_anh'              => $filePath,
                    'mo_ta'                 => $request->mo_ta,
                    'mo_ta_chi_tiet'        => $request->mo_ta_chi_tiet,
                    'id_chuyen_muc'         => $request->id_chuyen_muc,
                    'tinh_trang'            => $request->tinh_trang,
                ]);

            return response()->json([
                'status'     => true,
                'message'    => 'Đã Cập Nhật bài viết thành công!',
            ]);
        } catch (ExceptionEvent $e) {
            //throw $th;
            return response()->json([
                'status'     => false,
                'message'    => 'Cập Nhật bài viết không thành công!!'
            ]);
        }
    }

    public function thaydoiTrangThaiBaiViet(ThayDoiTrangThaiBaiVietRequest $request)
    {

        try {
            $id_chuc_nang = 10;
            $check = $this->checkQuyen($id_chuc_nang);
            if ($check == false) {
                return response()->json([
                    'status'  =>  false,
                    'message' =>  'Bạn không có quyền chức năng này'
                ]);
            }
            $tinh_trang_moi = !$request->tinh_trang;
            //   $tinh_trang_moi là trái ngược của $request->tinh_trangs
            BaiViet::where('id', $request->id)
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
    public function kiemTraSlugBaiViet(Request $request)
    {
        $tac_gia = BaiViet::where('slug_tieu_de', $request->slug)->first();

        if (!$tac_gia) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Bài Viết phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Bài Viết Đã Tồn Tại!',
            ]);
        }
    }
    public function kiemTraSlugBaiVietUpdate(Request $request)
    {
        $mon_an = BaiViet::where('slug_tieu_de', $request->slug)
            ->where('id', '<>', $request->id)
            ->first();

        if (!$mon_an) {
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Tên Bài Viết phù hợp!',
            ]);
        } else {
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Tên Bài Viết Đã Tồn Tại!',
            ]);
        }
    }
}
