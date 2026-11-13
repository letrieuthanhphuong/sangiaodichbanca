<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\XacThucController;
use App\Http\Controllers\DangKyNhaCungCapController;
use App\Http\Controllers\QuanTri\TongQuanController;
use App\Http\Controllers\QuanTri\DuyetNhaCungCapController;
use App\Http\Controllers\nhacungcap\BangDieuKhienController;
use App\Http\Controllers\NhaCungCap\SanPhamController;
use App\Http\Controllers\QuanTri\DuyetSanPhamController;
use App\Http\Controllers\TrangChuController;
use App\Http\Controllers\TimKiemController;

Route::get('/', function () {
    return view('trangchu');
});

Route::get('/', [TrangChuController::class, 'hienTrangChu']);

Route::middleware('guest')->group(function () {
    Route::post('/dangky', [XacThucController::class, 'dangKy'])->name('dangky');
    Route::post('/dangnhap', [XacThucController::class, 'dangNhap'])->name('dangnhap');

    Route::get('/dangnhap', fn () => redirect('/')->with('mocuaso', 'dangnhap'));
    Route::get('/dangky', fn () => redirect('/')->with('mocuaso', 'dangky'));
});

Route::post('/dangxuat', [XacThucController::class, 'dangXuat'])
    ->middleware('auth')->name('dangxuat');

Route::middleware('auth')->group(function () {
    Route::get('/dangkynguoiban', [DangKyNhaCungCapController::class, 'mo'])->name('nhacungcap.mo');
    Route::post('/dangkynguoiban', [DangKyNhaCungCapController::class, 'guiDon'])->name('nhacungcap.guidon');
});

Route::get('/timkiem', [TimKiemController::class, 'hienKetQua'])->name('timkiem');

// Khu vực quản trị viên
Route::middleware(['auth', 'vaitro:quantri'])->prefix('quantri')->group(function () {
    Route::get('/', [TongQuanController::class, 'tongQuan'])->name('quantri.tongquan');
    Route::get('/nhacungcap', [DuyetNhaCungCapController::class, 'danhSach'])->name('quantri.nhacungcap');
    Route::post('/nhacungcap/{nhacungcap}/duyet', [DuyetNhaCungCapController::class, 'duyet'])->name('quantri.nhacungcap.duyet');
    Route::post('/nhacungcap/{nhacungcap}/tuchoi', [DuyetNhaCungCapController::class, 'tuChoi'])->name('quantri.nhacungcap.tuchoi');
    Route::get('/sanpham', [DuyetSanPhamController::class, 'danhSach'])->name('quantri.sanpham');
    Route::post('/sanpham/{sanpham}/duyet', [DuyetSanPhamController::class, 'duyet'])->name('quantri.sanpham.duyet');
    Route::post('/sanpham/{sanpham}/tuchoi', [DuyetSanPhamController::class, 'tuChoi'])->name('quantri.sanpham.tuchoi');
});

// Khu vực người bán (chỉ cửa hàng đã được duyệt)
Route::middleware(['auth', 'nhacungcap'])->prefix('nhacungcap')->group(function () {
    Route::get('/', [BangDieuKhienController::class, 'tongQuan'])->name('nhacungcap.tongquan');
});

Route::middleware(['auth', 'nhacungcap'])->prefix('nhacungcap')->group(function () {
    Route::get('/', [BangDieuKhienController::class, 'tongQuan'])->name('nhacungcap.tongquan');

    Route::get('/sanpham', [SanPhamController::class, 'danhSach'])->name('nhacungcap.sanpham');
    Route::get('/sanpham/them', [SanPhamController::class, 'them'])->name('nhacungcap.sanpham.them');
    Route::post('/sanpham', [SanPhamController::class, 'luu'])->name('nhacungcap.sanpham.luu');
    Route::get('/sanpham/{id}/sua', [SanPhamController::class, 'sua'])->name('nhacungcap.sanpham.sua');
    Route::put('/sanpham/{id}', [SanPhamController::class, 'capNhat'])->name('nhacungcap.sanpham.capnhat');
    Route::delete('/sanpham/{id}', [SanPhamController::class, 'xoa'])->name('nhacungcap.sanpham.xoa');
});

