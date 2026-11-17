<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'giohang';
    protected $primaryKey = 'id_giohang';
    public $timestamps = false; 
    protected $fillable = ['id_nguoidung', 'id_sanpham', 'soluong'];

    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'id_sanpham', 'id_sanpham');
    }
}