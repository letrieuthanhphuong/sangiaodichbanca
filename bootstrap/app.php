<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'vaitro' => \App\Http\Middleware\KiemTraVaiTro::class,
        ]);

        // Chưa đăng nhập thì về trang chủ và tự mở cửa sổ đăng nhập
        $middleware->redirectGuestsTo('/dangnhap');

        // Đã đăng nhập thì không vào lại trang đăng nhập, đăng ký
        $middleware->redirectUsersTo('/');

        $middleware->alias([
            'vaitro' => \App\Http\Middleware\KiemTraVaiTro::class,
            'nhacungcap' => \App\Http\Middleware\KiemTraNhaCungCap::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();