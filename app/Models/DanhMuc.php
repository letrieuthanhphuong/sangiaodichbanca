<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'danhmuc';
    protected $primaryKey = 'id_danhmuc';

    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = null;   // bảng danhmuc không có cột ngày cập nhật

    protected $fillable = ['tendanhmuc', 'mota', 'trangthai'];

    public function sanpham()
    {
        return $this->hasMany(SanPham::class, 'id_danhmuc', 'id_danhmuc');
    }
}