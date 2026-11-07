<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KiemTraVaiTro
{
    public function handle(Request $yeucau, Closure $tiep, string ...$cacvaitro): Response
    {
        $nguoidung = $yeucau->user();

        if (! $nguoidung || ! in_array($nguoidung->vaitro, $cacvaitro)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $tiep($yeucau);
    }
}