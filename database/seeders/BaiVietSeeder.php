<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaiVietSeeder extends Seeder
{
    public function run()
    {
        DB::table('bai_viets')->truncate();

        DB::table('bai_viets')->insert([
            [
                'tieu_de' => 'One Piece: Giả thuyết đen tối của fan tiết lộ Luffy sẽ ra đi trẻ?',
                'slug_tieu_de' => 'one-piece-gia-thuyet-den-toi-cua-fan-tiet-lo-luffy-se-ra-di-tre',
                'hinh_anh' => 'https://gamek.mediacdn.vn/133514250583805952/2023/12/27/photo-1703651571559-17036515716502019562140.png',
                'mo_ta' => 'Người hâm mộ One Piece đưa ra suy đoán về tương lai của Luffy, so sánh anh với Gol D. Roger.',
                'mo_ta_chi_tiet' => 'Monkey D. Luffy là nhân vật chính mang tính biểu tượng của One Piece, cậu đã phải đối mặt với vô số thử thách trong suốt các phần khác nhau của bộ truyện. Những cuộc phiêu lưu thường xuyên nguy hiểm này khiến mạng sống của Luffy gặp nguy hiểm hết lần này đến lần khác. Xem xét lối sống mạo hiểm của cậu ta và tính tàn bạo của các trận chiến, nhiều fan đặt ra câu hỏi là "Liệu Luffy có thể sống được bao lâu?"',
                'id_chuyen_muc' => 1, 'tinh_trang' => 1
            ],
            [
                'tieu_de' => 'Tại sao Rồng thần Shenron lại sợ Beerus trong Dragon Ball Super?',
                'slug_tieu_de' => 'tai-sao-rong-than-shenron-lai-so-beerus-trong-dragon-ball-super',
                'hinh_anh' => 'https://gamek.mediacdn.vn/133514250583805952/2023/12/26/base64-1703570256670530666608.png',
                'mo_ta' => 'Nguyên nhân khiến Shenron sợ hãi trở nên rõ ràng khi chúng ta xem xét sức mạnh khổng lồ của Beerus với tư cách là Thần hủy diệt.',
                'mo_ta_chi_tiet' => 'Shenron là là Rồng thần trái đất, xuất hiện trong suốt chiều dài lịch sử của series Dragon Ball. Nó nổi tiếng với khả năng ban điều ước và là trung tâm của nhiều cuộc phiêu lưu sử thi trong loạt phim.',
                'id_chuyen_muc' => 1, 'tinh_trang' => 1
            ],
            [
                'tieu_de' => 'Tại sao mắt của Tobirama Senju trong Naruto lại có màu đỏ?',
                'slug_tieu_de' => 'tai-sao-mat-cua-tobirama-senju-trong-naruto-lai-co-mau-do',
                'hinh_anh' => 'https://gamek.mediacdn.vn/133514250583805952/2023/12/29/photo-1703825363855-17038253642581779386748.png',
                'mo_ta' => 'Tobirama Senju có đôi mắt đỏ màu đỏ trong anime Naruto, khiến nhiều người đặt ra giả thuyết về đặc điểm ngoại hình này.',
                'mo_ta_chi_tiet' => 'Trong câu chuyện của Naruto, nhân vật Hokage đệ nhị Tobirama Senju có một đặc điểm hấp dẫn: đó là đôi mắt đỏ. Điểm đặc biệt về mặt hình ảnh này là nguồn gốc của nhiều suy đoán khác nhau giữa những người hâm mộ bộ truyện. Có một giả thuyết được đặt ra là Tobirama mắc bệnh bạch tạng, vì trên thực tế, những người mắc bệnh này thường có mắt đỏ hoặc hồng do thiếu sắc tố.',
                'id_chuyen_muc' => 1, 'tinh_trang' => 1
            ],
            [
                'tieu_de' => '8 sự ra đi buồn nhất của nhân vật fan yêu mến trong anime năm 2023',
                'slug_tieu_de' => '8-su-ra-di-buon-nhat-cua-nhan-vat-fan-yeu-men-trong-anime-nam-2023',
                'hinh_anh' => 'https://gamek.mediacdn.vn/thumb_w/640/133514250583805952/2023/12/29/base64-17038237692711199395634.png',
                'mo_ta' => 'Những nhân vật đã rời bỏ khán giả đã tại ra nỗi mất mát khó quên của anime năm 2023.',
                'mo_ta_chi_tiet' => 'Khi năm 2023 sắp kết thúc, người hâm mộ anime trên toàn thế giới đã tạm biệt nhiều câu chuyện cảm động. Đồng thETIME, kỳ vọng ngày càng tăng đối với các bộ phim phát hành mới vào năm 2024, bao gồm cả những bộ phim được chờ đợi từ lâu như Solo Leveling và Suicide Squad Isekai',
                'id_chuyen_muc' => 1, 'tinh_trang' => 1
            ],
            [
                'tieu_de' => 'Họa sĩ One Punch Man tái hiện trang bìa Dragon Ball',
                'slug_tieu_de' => 'hoa-si-one-punch-man-tai-hien-trang-bia-dragon-ball',
                'hinh_anh' => 'https://gamek.mediacdn.vn/thumb_w/690/133514250583805952/2023/12/28/photo-1703742089806-1703742089969604534140-0-83-675-1163-crop-1703742309550355505991.png',
                'mo_ta' => 'Nhiều trang bìa manga do một số tác giả nổi tiếng của Shonen Jump vẽ sẽ được trưng bày để kỉ niệm 40 năm Dragon Bal ra mắt.',
                'mo_ta_chi_tiet' => 'Vào năm 2024, Dragon Ball - bộ truyện tranh mang tính biểu tượng của Akira Toriyama sẽ đạt được một cột mốc lịch sử: kỷ niệm 40 năm thành lập. Kể từ khi xuất hiện lần đầu trên các trang tạp chí Weekly Shonen Jump của Shueisha vào năm 1984, Dragon Ball đã trở thành một hiện tượng toàn cầu. Để đánh dấu sự kiện này, Shueisha đang lên kế hoạch tổ chức lễ kỷ niệm quy mô lớn, bao gồm một triển lãm nghệ thuật độc đáo tại Nhật Bản.',
                'id_chuyen_muc' => 1, 'tinh_trang' => 1
            ],
            [
                'tieu_de' => 'Không phải KnY hay One Piece, đây mới là anime được nhắc đến nhiều nhất trên Twitter năm 2023',
                'slug_tieu_de' => 'khong-phai-kny-hay-one-piece-day-moi-la-anime-duoc-nhac-den-nhieu-nhat-tren-twitter-nam-2023',
                'hinh_anh' => 'https://gamek.mediacdn.vn/thumb_w/640/133514250583805952/2023/12/28/82a3403c073edde0703175ad8c0f9bee-1703740179635-17037401797391237485002.jpg',
                'mo_ta' => 'Mobile Suit GTA: The Witch From Mercury là anime được nhắc đến nhiều nhất trên Twitter năm 2023.',
                'mo_ta_chi_tiet' => 'Vừa qua, tài khoản @TrendAward đã tỉ mỉ xem xét siêu dữ liệu của các bài đăng bằng tiếng Nhật. Giống như một thám tử kỹ thuật số" với kính lúp trong tay, tài khoản này xác định rằng Frieren: Beyond Journey s End, Mobile Suit GTA: The Witch From Mercury và các tác phẩm đáng ngạc nhiên khác đã chinh phục bục thảo luận của cư dân mạng trên Twitter. Quá trình truy xét của @TrendAward đã biến những dòng tweet đơn giản thành dữ liệu có giá trị',
                'id_chuyen_muc' => 1, 'tinh_trang' => 1
            ],
            [
                'tieu_de' => 'Top 10 anime isekai hay nhất năm 2023',
                'slug_tieu_de' => 'top-10-anime-isekai-hay-nhat-nam-2023',
                'hinh_anh' => 'https://gamek.mediacdn.vn/133514250583805952/2023/12/26/base64-1703499929978265862019-1703562956622-17035629567681010844098-1703567561585-1703567562290966755689.png',
                'mo_ta' => 'Sức hấp dẫn của Isekai nằm ở khả năng đưa các nhân vật thực tế vào thế giới giả tưởng, cung cấp nền tảng để khám phá những câu chuyện sáng tạo và độc đáo.',
                'mo_ta_chi_tiet' => 'Trong những năm gần đây, thể loại Isekai đã định nghĩa lại thế giới anime, thu hút người hâm mộ cũng như những người sáng tạo bằng tiền đề đổi mới và sự tự do sáng tạo. Xu hướng này vẫn tiếp tục mạnh mẽ vào năm 2023, với một số tựa phim isekai nổi bật trên thị trường.',
                'id_chuyen_muc' => 1, 'tinh_trang' => 1
            ],
        ]);
    }
}
