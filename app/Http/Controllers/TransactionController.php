<?php

namespace App\Http\Controllers;

use App\Jobs\MailQueue;
use App\Jobs\MailThanhToanLoiQueue;
use App\Jobs\MailThanhToanQueue;
use App\Models\GoiVip;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function index()
    {
        $payload = [
            "USERNAME"   => "0708585120",
            "PASSWORD"   => "01200120Vietvo371@",
            "DAY_BEGIN" => Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y'),
            "DAY_END"   => Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y'),
            "NUMBER_MB"  => "0708585120"
        ];

        try {
            // Gửi yêu cầu đến API
            $client = new Client();
            $response = $client->post("https://api-mb.dzmid.io.vn/mb", ['json' => $payload]);
            $data = json_decode($response->getBody(), true)['data'] ?? [];
            // dd($data);
            foreach ($data as $value) {
                // Bỏ qua nếu `creditAmount` <= 0 hoặc thiếu key cần thiết
                if (empty($value['creditAmount']) || $value['creditAmount'] <= 0) {
                    continue;
                }

                $ma_hoa_don = $this->convertToId($value['description']);
                $hoaDon = HoaDon::where('ma_hoa_don', $ma_hoa_don)->first();

                // Kiểm tra hóa đơn hợp lệ
                if (!$hoaDon || $hoaDon->tinh_trang == 1) {
                    continue;
                }

                $khachHang = KhachHang::find($hoaDon->id_khach_hang);
                $this->handleTransaction($hoaDon, $khachHang, $value, $ma_hoa_don);
            }
            return response()->json([
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi API: ' . $e->getMessage(), [
                'payload' => $payload,
            ]);
            return response()->json([
                'status' => false,
            ], 500);
        }
    }

    private function handleTransaction($hoaDon, $khachHang, $value, $ma_hoa_don)
    {
        try {
            // Tạo hoặc cập nhật giao dịch
            $transaction = Transaction::updateOrCreate(
                ['ma_hoa_don' => $ma_hoa_don],
                [
                    'creditAmount' => $value['creditAmount'],
                    'description'  => $value['description'],
                    'pos'          => $value['pos'],
                ]
            );

            // Kiểm tra nếu giao dịch đã tồn tại
            if (!$transaction->wasRecentlyCreated) {
                Log::info('Giao dịch đã tồn tại, không xử lý lại.', [
                    'ma_hoa_don' => $ma_hoa_don,
                ]);
                return;
            }

            // Cộng dồn số tiền đã thanh toán
            $hoaDon->so_tien_da_thanh_toan += $value['creditAmount'];
            $hoaDon->save();

            // Thông tin email cơ bản
            $data = [
                'ho_ten'                => $khachHang->ho_va_ten,
                'ma_hoa_don'            => $ma_hoa_don,
                'so_tien_chuyen_khoan'  => $value['creditAmount'],
                'so_tien_da_thanh_toan' => $hoaDon->so_tien_da_thanh_toan,
                'email'                 => $khachHang->email,
            ];

            // Kiểm tra trạng thái thanh toán
            if ($hoaDon->so_tien_da_thanh_toan >= $hoaDon->tong_tien) {
                // Đã thanh toán đủ
                $hoaDon->update(['tinh_trang' => true]);
                $data['so_tien_du'] = $hoaDon->so_tien_da_thanh_toan - $hoaDon->tong_tien; // Tiền thừa (nếu có)
                MailThanhToanQueue::dispatch($data);
            } else {
                // Chưa thanh toán đủ
                $soTienThieu = $hoaDon->tong_tien - $hoaDon->so_tien_da_thanh_toan;
                $data['so_tien_can_thanh_toan'] = $hoaDon->tong_tien;
                $data['tien_thieu'] = $soTienThieu;
                $data['qr_url'] = 'https://img.vietqr.io/image/mb-0708585120-compact2.jpg?amount='
                    . $soTienThieu
                    . '&addInfo=' . $hoaDon->ma_hoa_don
                    . '&accountName=VO_VAN_VIET';
                MailThanhToanLoiQueue::dispatch($data);
            }
        } catch (\Exception $e) {
            // Ghi log nếu không thể tạo giao dịch
            Log::error('Lỗi khi tạo giao dịch: ' . $e->getMessage(), [
                'ma_hoa_don' => $ma_hoa_don,
                'value'      => $value,
            ]);
        }
    }

    public function convertToId($description)
    {
        preg_match('/(Hd[a-zA-Z0-9]+)-/i', $description, $matches);
        return $matches[1] ?? null;
    }
}
