<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'sanpham';
    protected $primaryKey = 'id_sanpham';
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = 'ngaycapnhat';
    protected $fillable = [
        'id_nhacungcap', 'id_danhmuc', 'id_loaica', 'tensanpham', 'mota', 'gia',
        'soluongton', 'kichthuoc', 'xuatxu', 'trangthai', 'trangthaiduyet', 'lydotuchoi',
    ];
    // Sản phẩm được phép hiện cho khách: đã duyệt và đang bán
    public function scopeHienThi($truyvan)
    {
        return $truyvan->where('trangthaiduyet', 'daduyet')->where('trangthai', 'dangban');
    }
    public function nhacungcap()
    {
        return $this->belongsTo(NhaCungCap::class, 'id_nhacungcap', 'id_nhacungcap');
    }
    public function danhmuc()
    {
        return $this->belongsTo(DanhMuc::class, 'id_danhmuc', 'id_danhmuc');
    }
    public function loaica()
    {
        return $this->belongsTo(LoaiCa::class, 'id_loaica', 'id_loaica');
    }
    public function hinhanh()
    {
        return $this->hasMany(HinhAnhSanPham::class, 'id_sanpham', 'id_sanpham')->orderBy('thutu');
    }
    public function sanpham()
    {
        return $this->hasMany(SanPham::class, 'id_nhacungcap', 'id_nhacungcap');
    }

    
}