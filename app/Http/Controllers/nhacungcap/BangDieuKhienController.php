<?php

namespace App\Http\Controllers\NhaCungCap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BangDieuKhienController extends Controller
{
       public function tongQuan()
    {
        $cuahang = Auth::user()->nhacungcap;

        $thongke = [
            'dangban' => $cuahang->sanpham()
                ->where('trangthaiduyet', 'daduyet')
                ->where('trangthai', 'dangban')
                ->count(),
        ];

        return view('nhacungcap.tongquan', compact('cuahang', 'thongke'));
    }
}