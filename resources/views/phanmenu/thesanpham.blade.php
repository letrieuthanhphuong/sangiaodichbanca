
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
        <a href="#" class="nutcam">Thêm vào giỏ</a>
    </div>
</div>