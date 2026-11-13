<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('tieude', 'AquaMarket - Sàn giao dịch cá cảnh')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/giaodien.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chung.css') }}">
    <link rel="stylesheet" href="{{ asset('css/khungkhachhang.css') }}">
    <link rel="stylesheet" href="{{ asset('css/trangchu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theca.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cuaso.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timkiem.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    {{-- Thanh trên cùng --}}
    <div class="thanhtren">
        <div class="container d-flex justify-content-between align-items-center">
            <span>Hotline: 1900 xxxx</span>

            <div class="d-flex align-items-center" style="gap: 8px;">
                @auth
                    @if (Auth::user()->laQuanTri())
                        <a href="{{ route('quantri.tongquan') }}">Trang quản trị</a>
                    @elseif (Auth::user()->laNhaCungCap())
                        <a href="{{ route('nhacungcap.tongquan') }}">Kênh nhà cung cấp</a>
                    @elseif (Auth::user()->nhacungcap?->trangthaiduyet === 'choduyet')
                        <a href="#" onclick="moCuaSo('nhacungcap'); return false;">Đơn đăng ký đang chờ duyệt</a>
                    @else
                        <a href="#" onclick="moCuaSo('nhacungcap'); return false;">Đăng ký nhà cung cấp</a>
                    @endif

                    <span>|</span>
                    <span>Hỗ trợ</span>
                    <span>|</span>
                    <span>Chào, {{ Auth::user()->hoten }}</span>

                    <form action="{{ route('dangxuat') }}" method="POST" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="nutchu">Đăng xuất</button>
                    </form>
                @else
                    <span>Hỗ trợ</span>
                    <span>|</span>
                    <a href="#" onclick="moCuaSo('dangnhap'); return false;">Đăng nhập</a>
                    <span>|</span>
                    <a href="#" onclick="moCuaSo('dangky'); return false;">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Đầu trang --}}
    <header class="dautrang">
        <div class="container d-flex align-items-center gap-4">
            <a href="/" class="thuonghieu">Aqua<span>Market</span></a>
            <form action="{{ route('timkiem') }}" method="GET" class="formtimkiem">
                <input type="text" name="tukhoa" class="otimkiem" value="{{ request('tukhoa') }}"
                    placeholder="Tìm cá Koi, Rồng, Betta..." autocomplete="off">
                <button type="submit" class="nutcam">Tìm</button>
            </form>
            <a href="#" class="nutcam text-nowrap">Giỏ hàng (0)</a>
        </div>
    </header>

    {{-- Nội dung riêng của từng trang --}}
    <main class="container pb-5">
        @yield('noidung')
    </main>

    @php
        $thongbao = session('thongbao');
    @endphp

    @if ($thongbao)
        {{-- Kiểm tra nếu $thongbao là mảng đúng chuẩn từ controller gửi sang --}}
        @if (is_array($thongbao))
            <div class="thongbaonoi {{ $thongbao['loai'] ?? 'thanhcong' }}" id="thongbaonoi">
                <strong>{{ $thongbao['tieude'] ?? 'Thông báo' }}</strong>
                <span>{{ $thongbao['noidung'] ?? '' }}</span>
            </div>
        @else
            {{-- Phòng hờ trường hợp có chỗ nào đó lỡ truyền chuỗi string đơn giản --}}
            <div class="thongbaonoi thanhcong" id="thongbaonoi">
                <strong>Thông báo</strong>
                <span>{{ $thongbao }}</span>
            </div>
        @endif
    @endif

    {{-- Chân trang --}}
    <footer class="chantrang">
        © 2026 AquaMarket - Sàn giao dịch cá cảnh trực tuyến
    </footer>

@guest
    @include('xacthuc.cuaso')
@endguest

@auth
    @if (! Auth::user()->laQuanTri() && ! Auth::user()->laNhaCungCap())
        @include('nhacungcap.cuasonhacungcap')
    @endif
@endauth

@include('xacthuc.thongbao')

{{-- Đưa dữ liệu session từ Laravel ra các thuộc tính data-* để JavaScript đọc an toàn --}}
<div id="laravel-data" 
     data-mocuaso="{{ session('mocuaso') }}" 
     style="display: none;">
</div>

<script>
    // Mở một cửa sổ: 'dangnhap' hoặc 'dangky'
    function moCuaSo(ten) {
        var o = document.getElementById('cuaso-' + ten);
        if (!o) return;
        dongCuaSo();
        o.classList.add('mo');
    }

    function dongCuaSo() {
        document.querySelectorAll('.nencuaso').forEach(function (o) {
            o.classList.remove('mo');
        });
    }

    // Bấm con mắt để hiện hoặc ẩn mật khẩu
    function hienAnMatKhau(idtruong, bieutuong) {
        var truong = document.getElementById(idtruong);
        var dangAn = truong.type === 'password';
        truong.type = dangAn ? 'text' : 'password';
        bieutuong.classList.toggle('fa-eye', !dangAn);
        bieutuong.classList.toggle('fa-eye-slash', dangAn);
    }

    document.querySelectorAll('.thongbaonoi').forEach(function (thongbao) {
        setTimeout(function () {
            thongbao.style.opacity = '0';

            setTimeout(function () {
                thongbao.remove();
            }, 400);
        }, 2000);
    });
</script>

</body>
</html>