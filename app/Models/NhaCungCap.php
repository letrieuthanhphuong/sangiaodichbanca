<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    protected $table = 'nhacungcap';
    protected $primaryKey = 'id_nhacungcap';
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = null;// bảng nhacungcap không có cột ngày cập nhật
    protected $fillable = [
        'id_nguoidung', 'tencuahang', 'mota', 'diachi', 'sodienthoai', 'masothue',
        'trangthaiduyet', 'lydotuchoi', 'id_quantri', 'ngayduyet',
    ];
    protected function casts(): array
    {
        return ['ngayduyet' => 'datetime'];
    }
    public function nguoidung()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoidung', 'id_nguoidung');
    }
        public function sanpham()
    {
        return $this->hasMany(SanPham::class, 'id_nhacungcap', 'id_nhacungcap');
    }
}