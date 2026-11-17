
@php
    $duongdananh = $ca->hinhanh->first()?->duongdan;
    $coanh = $duongdananh && file_exists(public_path($duongdananh));
@endphp

<div class="theca">
    <div class="anhca">
        @if ($coanh)
            <img src="{{ asset($duongdananh) }}" alt="{{ $ca->tensanpham }}">
        @endif
    </div>
    <div class="noidungthe">
        {{-- Trang chi tiết sản phẩm chưa làm nên tạm để # --}}
        <div class="tenca"><a href="#">{{ $ca->tensanpham }}</a></div>
        <div class="tencuahang">{{ $ca->nhacungcap->tencuahang }}</div>
        <div class="giaca">{{ number_format($ca->gia, 0, ',', '.') }}đ</div>
        @auth
            <form action="{{ route('giohang.them', $ca->id_sanpham) }}" method="POST">
                @csrf
                <button type="submit" class="nutcam w-100">Thêm vào giỏ</button>
            </form>
        @else
            <a href="#" class="nutcam" onclick="moCuaSo('dangnhap'); return false;">Thêm vào giỏ</a>
        @endauth
    </div>
</div>