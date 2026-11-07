<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KiemTraNhaCungCap
{
    public function handle(Request $yeucau, Closure $tiep): Response
    {
        $nguoidung = $yeucau->user();

        // laNhaCungCap() nằm trong model NguoiDung: có đơn đăng ký đã được duyệt
        if (! $nguoidung || ! $nguoidung->laNhaCungCap()) {
            abort(403, 'Chỉ người bán đã được duyệt mới vào được trang này.');
        }

        return $tiep($yeucau);
    }
}