<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\NhaCungCap;
use App\Models\SanPham;

class TrangChuController extends Controller
{
    public function hienTrangChu()
    {
        // Danh mục kèm số sản phẩm đang hiển thị
        $danhmuc = DanhMuc::where('trangthai', 1)
            ->withCount(['sanpham as soluong' => function ($truyvan) {
                $truyvan->hienThi();
            }])
            ->get();

        // Cửa hàng đã được duyệt, kèm số sản phẩm đang bán (cửa hàng nhiều hàng đứng trước)
        $cuahang = NhaCungCap::where('trangthaiduyet', 'daduyet')
            ->withCount(['sanpham as soluong' => function ($truyvan) {
                $truyvan->hienThi();
            }])
            ->orderByDesc('soluong')
            ->orderByDesc('ngayduyet')
            ->take(8)
            ->get();

        // Cá mới đăng: đã duyệt, đang bán, mới nhất trước
        $danhsachca = SanPham::hienThi()
            ->with(['hinhanh', 'nhacungcap'])
            ->orderByDesc('ngaytao')
            ->orderByDesc('id_sanpham')
            ->take(30)
            ->get()
            ->groupBy('id_nhacungcap')
            ->flatMap(fn ($nhom) => $nhom->take(2))
            ->sortByDesc('id_sanpham')
            ->take(6)
            ->values();

        $camket = [
            ['tieude' => 'Cá sống 100%', 'mota' => 'Đền bù nếu cá chết khi giao'],
            ['tieude' => 'Đóng gói chuyên dụng', 'mota' => 'Túi oxy, thùng xốp giữ nhiệt'],
            ['tieude' => 'Nhà cung cấp uy tín', 'mota' => 'Cửa hàng được quản trị viên xét duyệt'],
            ['tieude' => 'Thanh toán an toàn', 'mota' => 'Giữ tiền đến khi bạn nhận cá'],
        ];

        return view('trangchu', compact('danhmuc', 'cuahang', 'danhsachca', 'camket'));
    }
}