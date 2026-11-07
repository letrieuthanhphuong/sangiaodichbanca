<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiCa extends Model
{
    protected $table = 'loaica';
    protected $primaryKey = 'id_loaica';
    public $timestamps = false;

    protected $fillable = [
        'id_danhmuc', 'tenloaica', 'tenkhoahoc', 'kichthuoctruongthanh', 'nhietdo',
        'ph', 'dokhonuoi', 'tinhcach', 'moitruongsong', 'huongdansoc',
    ];
}