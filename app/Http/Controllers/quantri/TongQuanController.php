<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\NhaCungCap;

class TongQuanController extends Controller
{
    public function tongQuan()
    {
        $thongke = [
            'nguoidung' => NguoiDung::where('vaitro', 'khachhang')->count(),
            'cuahang' => NhaCungCap::where('trangthaiduyet', 'daduyet')->count(),
            'choduyet' => NhaCungCap::where('trangthaiduyet', 'choduyet')->count(),
            'tuchoi' => NhaCungCap::where('trangthaiduyet', 'tuchoi')->count(),
        ];

        $donmoi = NhaCungCap::with('nguoidung')
            ->where('trangthaiduyet', 'choduyet')
            ->latest('ngaytao')
            ->take(5)
            ->get();

        return view('quantri.tongquan', compact('thongke', 'donmoi'));
    }
}