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

        // Cá mới đăng: đã duyệt, đang bán, mới nhất trước, có phân trang
        $danhsachca = SanPham::hienThi()
            ->with(['hinhanh', 'nhacungcap'])
            ->orderByDesc('ngaytao')
            ->orderByDesc('id_sanpham')
            ->paginate(6)
            ->fragment('canhmoi')   // chuyển trang xong vẫn dừng ở mục cá mới đăng
            ->withQueryString();

        return view('trangchu', compact('danhmuc', 'cuahang', 'danhsachca'));
    }
}