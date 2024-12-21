<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginGoogleController extends Controller
{
    // public function getGoogleSignInUrl()
    // {
    //     try {
    //         $url = Socialite::driver('google')
    //             ->redirect()->getTargetUrl();
    //         return response()->json([
    //             'url' => $url,
    //         ]);
    //     } catch (\Exception $exception) {
    //         return $exception;
    //     }
    // }
    public function getGoogleSignInUrl()
    {
        // dd(config('services.google'));
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function loginCallback()
    {
        try {
            // Get the Google user information from Socialite
            $googleUser = Socialite::driver('google')->user();

            // Ensure the user exists and has an email
            if (!$googleUser || !$googleUser->email) {
                return redirect('http://localhost:5173/fasfasf/adadada');
            }

            // Check if the user already exists in the database
            $existingUser = KhachHang::where('google_id', $googleUser->id)->first();

            if ($existingUser) {
                // Log in the existing user
                Auth::guard('khach_hang')->login($existingUser);

                // Generate a token for the logged-in user
                $token = $existingUser->createToken('token_admin')->plainTextToken;


                // Redirect to frontend with success status and user ID
                return redirect('http://localhost:5173/home/auth-google/statute/' .  $token);
            } else {
                // Create a new user if not found
                $newUser = KhachHang::updateOrCreate(
                    ['email' => $googleUser->email],
                    [
                        'ho_va_ten' => $googleUser->name,
                        'avatar' => $googleUser->avatar,
                        'google_id' => $googleUser->id,
                        'so_dien_thoai' => '', // Assuming phone number is optional
                        'is_active' => 1,
                        'password' => bcrypt('123456vietdz'), // You may want to generate a random password or handle this better
                    ]
                );

                // Log in the newly created user
                Auth::guard('khach_hang')->login($newUser);

                $token = $newUser->createToken('token_admin')->plainTextToken;

                // Redirect to frontend with registration success status and user ID
                return redirect('http://localhost:5173/home/auth-google/statute/' .  $token);
            }
        } catch (\Exception $e) {
            // Handle errors and redirect to the login page with the error message
            return redirect('http://localhost:5173/login?error=' . urlencode($e->getMessage()));
        }
    }
    public function checkGoogleLogin(Request $request)
    {
        $user = $this->isUser();
        if ($user) {
            return response()->json([
                'status'    => true,
                'message'      => "Đăng nhập thành công",
                'email'                => $user->email,
                'id_user'              => $user->id,
                'ho_ten_user'          => $user->ho_va_ten,
                'hinh_anh_user'        => $user->avatar,
                'token'                =>   $user->createToken('token_khach_hang')->plainTextToken,

            ]);
        }
        return response()->json([
            'status'    => false,
            'message'      => "Xảy ra lỗi"
        ]);
    }
}
