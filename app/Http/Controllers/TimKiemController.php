<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;

class TimKiemController extends Controller
{
    public function hienKetQua(Request $yeucau)
    {
        // Lấy từ khóa, cắt khoảng trắng hai đầu và giới hạn 100 ký tự (gõ ?tukhoa[]=... thì coi như rỗng)
        $tho = $yeucau->input('tukhoa', '');
        $tukhoa = is_string($tho) ? trim(mb_substr($tho, 0, 100)) : '';
        $ketqua = collect();

        if ($tukhoa !== '') {
            // Chỉ tìm trong sản phẩm được phép hiện cho khách (đã duyệt và đang bán)
            $danhsach = SanPham::hienThi()
                ->with(['hinhanh', 'nhacungcap', 'danhmuc', 'loaica'])
                ->orderByDesc('id_sanpham')
                ->get();

            // Giá có thể gõ kiểu 12.500.000 hoặc 12,500,000 nên bỏ dấu chấm, phẩy, khoảng trắng rồi mới kiểm tra
            $sogia = str_replace(['.', ',', ' '], '', $tukhoa);
            $lagia = ctype_digit($sogia);

            $tukhoathuong = mb_strtolower($tukhoa, 'UTF-8');
            $tukhoakhongdau = $this->boDau($tukhoathuong);

            // Gõ không dấu (ví dụ "dia") thì tìm được cả chữ có dấu (Dĩa); gõ có dấu thì chỉ khớp đúng dấu
            $timkhongdau = ($tukhoathuong === $tukhoakhongdau);

            $ketqua = $danhsach->filter(function ($sp) use ($lagia, $sogia, $tukhoathuong, $timkhongdau) {
                // Khớp đúng giá tiền
                if ($lagia && (float) $sp->gia == (float) $sogia) {
                    return true;
                }

                // Gộp các cột cần tìm: tên sản phẩm, cửa hàng, danh mục, loại cá
                $noidung = mb_strtolower(implode(' ', [
                    $sp->tensanpham,
                    $sp->nhacungcap?->tencuahang,
                    $sp->danhmuc?->tendanhmuc,
                    $sp->loaica?->tenloaica,
                ]), 'UTF-8');

                return str_contains($noidung, $tukhoathuong)
                    || ($timkhongdau && str_contains($this->boDau($noidung), $tukhoathuong));
            })->values();
        }

        return view('timkiem', compact('ketqua', 'tukhoa'));
    }

    // Đổi chữ có dấu thành không dấu để tìm "dia" ra "Dĩa", "rong" ra "Rồng"
    private function boDau($chuoi)
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        return strtr($chuoi, [
            'á'=>'a','à'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'đ'=>'d',
            'é'=>'e','è'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'í'=>'i','ì'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'ó'=>'o','ò'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'ú'=>'u','ù'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'ý'=>'y','ỳ'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        ]);
    }
}