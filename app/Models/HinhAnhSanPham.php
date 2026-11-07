<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HinhAnhSanPham extends Model
{
    protected $table = 'hinhanhsanpham';
    protected $primaryKey = 'id_hinhanhsanpham';
    public $timestamps = false;   // bảng hinhanhsanpham không có cột thời gian

    protected $fillable = ['id_sanpham', 'duongdan', 'thutu'];
}