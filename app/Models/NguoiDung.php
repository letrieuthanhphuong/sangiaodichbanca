<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as NguoiDungXacThuc;

class NguoiDung extends NguoiDungXacThuc
{
    protected $table = 'nguoidung';
    protected $primaryKey = 'id_nguoidung';
    // Tên cột thời gian trong bảng của bạn
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = 'ngaycapnhat';
    protected $fillable = ['hoten', 'email', 'matkhau', 'sodienthoai', 'vaitro', 'trangthai'];
    protected $hidden = ['matkhau'];
    // Không dùng chức năng "ghi nhớ đăng nhập" nên không cần cột remember_token
    protected $rememberTokenName = '';
    // Cột mật khẩu tên là "matkhau" chứ không phải "password"
    public function getAuthPassword()
    {
        return $this->matkhau;
    }
    public function getAuthPasswordName()
    {
        return 'matkhau';
    }
    // Gán mật khẩu thường vào thì Laravel tự băm (nếu đã băm rồi thì giữ nguyên)
    protected function casts(): array
    {
        return ['matkhau' => 'hashed'];
    }
    public function nhacungcap()
    {
        return $this->hasOne(NhaCungCap::class, 'id_nguoidung', 'id_nguoidung');
    }
    public function laQuanTri(): bool
    {
        return $this->vaitro === 'quantri';
    }
    // Người bán = có đơn đăng ký đã được duyệt
    public function laNhaCungCap(): bool
    {
        return $this->nhacungcap?->trangthaiduyet === 'daduyet';
    }
}