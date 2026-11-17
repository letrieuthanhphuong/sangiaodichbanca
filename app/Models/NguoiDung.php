<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as NguoiDungXacThuc;

class NguoiDung extends NguoiDungXacThuc
{
    protected $table = 'nguoidung';
    protected $primaryKey = 'id_nguoidung';
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = 'ngaycapnhat';
    protected $fillable = ['hoten', 'email', 'matkhau', 'sodienthoai', 'vaitro', 'trangthai'];
    protected $hidden = ['matkhau'];
    protected $rememberTokenName = '';
    public function getAuthPassword()
    {
        return $this->matkhau;
    }
    public function getAuthPasswordName()
    {
        return 'matkhau';
    }
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