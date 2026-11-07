<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DuyetSanPhamController extends Controller
{
    public function danhSach(Request $yeucau)
    {
        $trangthai = $yeucau->query('trangthai', 'choduyet');

        if (! in_array($trangthai, ['choduyet', 'daduyet', 'tuchoi'])) {
            $trangthai = 'choduyet';
        }

        $danhsach = SanPham::with(['nhacungcap', 'danhmuc', 'loaica', 'hinhanh'])
            ->where('trangthaiduyet', $trangthai)
            ->orderByDesc('ngaycapnhat')
            ->orderByDesc('id_sanpham')
            ->get();

        return view('quantri.sanpham', compact('danhsach', 'trangthai'));
    }

    public function duyet(SanPham $sanpham)
    {
        if ($sanpham->trangthaiduyet !== 'choduyet') {
            return back()->with('thongbao', [
                'loai' => 'canhbao',
                'tieude' => 'Đã xử lý',
                'noidung' => 'Sản phẩm này đã được xử lý rồi.',
            ]);
        }

        $sanpham->update([
            'trangthaiduyet' => 'daduyet',
            'lydotuchoi' => null,
        ]);

        return back()->with('thongbao', [
            'loai' => 'thanhcong',
            'tieude' => 'Đã duyệt',
            'noidung' => 'Sản phẩm "' . $sanpham->tensanpham . '" đã được duyệt và hiện trên trang chủ nếu đang bán.',
        ]);
    }

    public function tuChoi(Request $yeucau, SanPham $sanpham)
    {
        if ($sanpham->trangthaiduyet !== 'choduyet') {
            return back()->with('thongbao', [
                'loai' => 'canhbao',
                'tieude' => 'Đã xử lý',
                'noidung' => 'Sản phẩm này đã được xử lý rồi.',
            ]);
        }

        $kiemtra = Validator::make($yeucau->all(), [
            'lydotuchoi' => 'required|string|max:500',
        ], [
            'required' => 'Vui lòng nhập lý do từ chối.',
            'max' => 'Lý do quá dài (tối đa 500 ký tự).',
        ]);

        if ($kiemtra->fails()) {
            return back()->with('thongbao', [
                'loai' => 'loi',
                'tieude' => 'Chưa từ chối được',
                'noidung' => $kiemtra->errors()->first(),
            ]);
        }

        $sanpham->update([
            'trangthaiduyet' => 'tuchoi',
            'lydotuchoi' => $kiemtra->validated()['lydotuchoi'],
        ]);

        return back()->with('thongbao', [
            'loai' => 'thongtin',
            'tieude' => 'Đã từ chối',
            'noidung' => 'Đã từ chối sản phẩm "' . $sanpham->tensanpham . '". Nhà cung cấp sẽ thấy lý do và sửa lại.',
        ]);
    }
}