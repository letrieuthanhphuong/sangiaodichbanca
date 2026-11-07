<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\Controller;
use App\Models\NhaCungCap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DuyetNhaCungCapController extends Controller
{
    public function danhSach(Request $yeucau)
    {
        $trangthai = $yeucau->query('trangthai', 'choduyet');

        if (! in_array($trangthai, ['choduyet', 'daduyet', 'tuchoi'])) {
            $trangthai = 'choduyet';
        }

        $danhsach = NhaCungCap::with('nguoidung')
            ->where('trangthaiduyet', $trangthai)
            ->orderByDesc('ngaytao')
            ->get();

        return view('quantri.nhacungcap', compact('danhsach', 'trangthai'));
    }

    public function duyet(Request $yeucau, NhaCungCap $nhacungcap)
    {
        if ($nhacungcap->trangthaiduyet !== 'choduyet') {
            return back()->with('thongbao', [
                'loai' => 'canhbao',
                'tieude' => 'Đã xử lý',
                'noidung' => 'Đơn này đã được xử lý rồi.',
            ]);
        }

        // Người bán được xác định bằng trạng thái "daduyet" trong bảng nhacungcap,
        // vaitro của tài khoản vẫn giữ là khachhang nên người bán vẫn mua hàng được
        $nhacungcap->update([
            'trangthaiduyet' => 'daduyet',
            'lydotuchoi' => null,
            'id_quantri' => $yeucau->user()->id_nguoidung,
            'ngayduyet' => now(),
        ]);

        return back()->with('thongbao', [
            'loai' => 'thanhcong',
            'tieude' => 'Đã duyệt',
            'noidung' => 'Cửa hàng "' . $nhacungcap->tencuahang . '" đã trở thành người bán.',
        ]);
    }

    public function tuChoi(Request $yeucau, NhaCungCap $nhacungcap)
    {
        if ($nhacungcap->trangthaiduyet !== 'choduyet') {
            return back()->with('thongbao', [
                'loai' => 'canhbao',
                'tieude' => 'Đã xử lý',
                'noidung' => 'Đơn này đã được xử lý rồi.',
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

        $nhacungcap->update([
            'trangthaiduyet' => 'tuchoi',
            'lydotuchoi' => $kiemtra->validated()['lydotuchoi'],
            'id_quantri' => $yeucau->user()->id_nguoidung,
            'ngayduyet' => now(),
        ]);

        return back()->with('thongbao', [
            'loai' => 'thongtin',
            'tieude' => 'Đã từ chối',
            'noidung' => 'Đã từ chối cửa hàng "' . $nhacungcap->tencuahang . '".',
        ]);
    }
}