<?php

namespace App\Http\Controllers;

use App\Models\SanPham;

class ChiTietSanPhamController extends Controller
{
    public function hienChiTiet($id)
    {
        // Chỉ xem được sản phẩm đã duyệt và chưa bị ẩn (hết hàng vẫn xem được).
        // Sản phẩm chờ duyệt, bị từ chối hoặc bị ẩn, hoặc id không có thật, thì trả về 404.
        $sanpham = SanPham::with(['hinhanh', 'nhacungcap', 'danhmuc', 'loaica'])
            ->where('trangthaiduyet', 'daduyet')
            ->whereIn('trangthai', ['dangban', 'hethang'])
            ->findOrFail($id);

        // Sản phẩm cùng danh mục đang bán, trừ chính nó, lấy ngẫu nhiên 3 cái
        $lienquan = SanPham::hienThi()
            ->with(['hinhanh', 'nhacungcap'])
            ->where('id_danhmuc', $sanpham->id_danhmuc)
            ->where('id_sanpham', '!=', $sanpham->id_sanpham)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('sanpham.chitiet', compact('sanpham', 'lienquan'));
    }
}