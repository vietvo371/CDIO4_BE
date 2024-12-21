<?php

namespace App\Http\Controllers;

use App\Models\PhanQuyen;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function isUser()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user instanceof \App\Models\KhachHang) {
            return $user;
        }
        return false;
    }

    public function isAdmin()
    {
        $user = Auth::guard('sanctum')->user();

        if ($user instanceof \App\Models\AdminAnime) {
            return $user;
        }
        return false;
    }
    public function responseData($data)
    {
        return response()->json([
            'data' => $data
        ]);
    }

    public function responseSuccess($message)
    {
        return response()->json([
            'status'  => true,
            'message' => $message
        ]);
    }

    public function responseError($message)
    {
        return response()->json([
            'status'  => false,
            'message' => $message
        ]);
    }

    public function checkQuyen($id_chuc_nang)
    {
        $user = $this->isAdmin();
        if ($user) {
            if ($user->is_master == 1) {
                return true;
            }

            if ($user->id_chuc_vu) {
                $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
                    ->where('id_chuc_nang', $id_chuc_nang)
                    ->first();

                if ($check) {
                    return true;
                }

                return false;
            }
        }

        return false;
    }
}
