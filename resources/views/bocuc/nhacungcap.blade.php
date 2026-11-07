<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('tieude', 'Kênh người bán - AquaMarket')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/giaodien.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bangdieukhien.css') }}">
</head>
<body>
<div class="khungbangdieukhien">

    <aside class="thanhben">
        <a href="/" class="thuonghieu d-block mb-4">Aqua<span>Market</span></a>

        @include('phanmenu.menunhacungcap')

        <div class="chancuathanhben">
            <a href="/">← Về trang mua hàng</a>
            <form method="POST" action="{{ route('dangxuat') }}" class="m-0">
                @csrf
                <button type="submit" class="nutchu">Đăng xuất</button>
            </form>
        </div>
    </aside>

    <section class="noidungchinh">
        <div class="dautrangbang">
            <h1>@yield('tieudetrang')</h1>
            <span>{{ Auth::user()->nhacungcap->tencuahang }}</span>
        </div>

        @yield('noidung')
    </section>

</div>

@include('xacthuc.thongbao')

<script>
    // Thông báo tự mờ dần rồi biến mất
    document.querySelectorAll('.thongbaonoi').forEach(function (o) {
        setTimeout(function () {
            o.style.opacity = '0';
            setTimeout(function () { o.remove(); }, 400);
        }, 3000);
    });
</script>
</body>
</html>