@extends('bocuc.khung')

@section('tieude', $sanpham->tensanpham . ' - AquaMarket')

@section('noidung')
@php
    // Chỉ lấy những ảnh có tệp thật trong public
    $anh = $sanpham->hinhanh->filter(fn ($h) => file_exists(public_path($h->duongdan)))->values();
    $loaica = $sanpham->loaica;
    $conhang = $sanpham->trangthai === 'dangban' && $sanpham->soluongton > 0;
    $nhandokho = ['de' => 'Dễ nuôi', 'trungbinh' => 'Trung bình', 'kho' => 'Khó nuôi'];

    // Người xem là chủ cửa hàng bán sản phẩm này thì không cho mua
    $sanphamcuaban = Auth::check()
        && (int) Auth::user()->nhacungcap?->id_nhacungcap === (int) $sanpham->id_nhacungcap;
@endphp

{{-- Đường dẫn: Trang chủ > Danh mục > Sản phẩm --}}
<nav class="duongdan" aria-label="Đường dẫn">
    <a href="/">Trang chủ</a>
    <i class="fas fa-angle-right"></i>
    <a href="{{ route('sanpham.danhsach', ['danhmuc' => $sanpham->id_danhmuc]) }}">{{ $sanpham->danhmuc?->tendanhmuc }}</a>
    <i class="fas fa-angle-right"></i>
    <span>{{ $sanpham->tensanpham }}</span>
</nav>

<div class="row g-4">
    {{-- ===== Ảnh sản phẩm ===== --}}
    <div class="col-lg-6">
        <div class="khunganhchinh">
            @if ($anh->count())
                <img id="anhchinh" src="{{ asset($anh->first()->duongdan) }}" alt="{{ $sanpham->tensanpham }}">
            @else
                <div class="chuacoanh">
                    <i class="fas fa-fish"></i>
                    <span>Chưa có ảnh</span>
                </div>
            @endif
        </div>

        {{-- Có từ 2 ảnh trở lên thì hiện dãy ảnh nhỏ, bấm để đổi ảnh lớn --}}
        @if ($anh->count() > 1)
            <div class="dayanhnho">
                @foreach ($anh as $i => $h)
                    <button type="button" class="anhnhochon {{ $i === 0 ? 'dangchon' : '' }}"
                            data-anh="{{ asset($h->duongdan) }}" onclick="chonAnh(this)"
                            aria-label="Xem ảnh {{ $i + 1 }}">
                        <img src="{{ asset($h->duongdan) }}" alt="">
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===== Thông tin và nút mua ===== --}}
    <div class="col-lg-6">
        <div class="thongtinsp">
            <a href="{{ route('sanpham.danhsach', ['cuahang' => [$sanpham->id_nhacungcap]]) }}" class="tencuahanglink">
                <i class="fas fa-store"></i> {{ $sanpham->nhacungcap->tencuahang }}
            </a>

            <h1>{{ $sanpham->tensanpham }}</h1>
            <div class="giachitiet">{{ number_format($sanpham->gia, 0, ',', '.') }}đ</div>

            <ul class="thongsonhanh">
                @if ($loaica)
                    <li><span>Loại cá</span><strong>{{ $loaica->tenloaica }}</strong></li>
                @endif
                @if ($sanpham->kichthuoc)
                    <li><span>Kích thước</span><strong>{{ rtrim(rtrim(number_format($sanpham->kichthuoc, 1), '0'), '.') }} cm</strong></li>
                @endif
                @if ($sanpham->xuatxu)
                    <li><span>Xuất xứ</span><strong>{{ $sanpham->xuatxu }}</strong></li>
                @endif
                <li>
                    <span>Tình trạng</span>
                    <strong class="{{ $conhang ? 'conhang' : 'hethang' }}">
                        {{ $conhang ? 'Còn ' . $sanpham->soluongton : 'Hết hàng' }}
                    </strong>
                </li>
            </ul>

            {{-- Nút mua đổi theo từng trường hợp --}}
            @if ($sanphamcuaban)
                <div class="ghichumua">Đây là sản phẩm của cửa hàng bạn nên không thể mua.</div>
            @elseif (! $conhang)
                <button type="button" class="nutcam nutmua" disabled>Hết hàng</button>
            @elseif (! Auth::check())
                <button type="button" class="nutcam nutmua" onclick="moCuaSo('dangnhap')">Đăng nhập để mua</button>
            @elseif (Route::has('giohang.them'))
                <form method="POST"
                    action="{{ route('giohang.them', ['id' => $sanpham->id_sanpham]) }}"
                    class="formmua">
                    @csrf

                    <input type="hidden" name="id_sanpham" value="{{ $sanpham->id_sanpham }}">

                    <label class="chonsoluong">
                        Số lượng
                        <input type="number" name="soluong"
                            value="1" min="1"
                            max="{{ $sanpham->soluongton }}">
                    </label>

                    <button type="submit" class="nutcam nutmua">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </form>
            @else
                <button type="button" class="nutcam nutmua" disabled>Giỏ hàng chưa sẵn sàng</button>
            @endif

            @if ($sanpham->mota)
                <div class="mota">
                    <h2>Mô tả</h2>
                    <p>{!! nl2br(e($sanpham->mota)) !!}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== Thông tin chăm sóc (chỉ có với cá, phụ kiện không có loại cá) ===== --}}
@if ($loaica)
    <h2 class="tieudemuc">Thông tin chăm sóc</h2>
    <div class="khungchamsoc">
        <table class="bangchamsoc">
            @if ($loaica->tenkhoahoc)
                <tr><th>Tên khoa học</th><td><em>{{ $loaica->tenkhoahoc }}</em></td></tr>
            @endif
            @if ($loaica->kichthuoctruongthanh)
                <tr><th>Kích thước trưởng thành</th><td>{{ $loaica->kichthuoctruongthanh }}</td></tr>
            @endif
            @if ($loaica->nhietdo)
                <tr><th>Nhiệt độ nước</th><td>{{ $loaica->nhietdo }}</td></tr>
            @endif
            @if ($loaica->ph)
                <tr><th>Độ pH</th><td>{{ $loaica->ph }}</td></tr>
            @endif
            <tr>
                <th>Độ khó nuôi</th>
                <td><span class="nhandokho {{ $loaica->dokhonuoi }}">{{ $nhandokho[$loaica->dokhonuoi] ?? $loaica->dokhonuoi }}</span></td>
            </tr>
            @if ($loaica->tinhcach)
                <tr><th>Tính cách</th><td>{{ $loaica->tinhcach }}</td></tr>
            @endif
            @if ($loaica->moitruongsong)
                <tr><th>Môi trường sống</th><td>{{ $loaica->moitruongsong }}</td></tr>
            @endif
            @if ($loaica->huongdansoc)
                <tr><th>Hướng dẫn chăm sóc</th><td>{{ $loaica->huongdansoc }}</td></tr>
            @endif
        </table>
    </div>
@endif

{{-- ===== Sản phẩm cùng danh mục ===== --}}
@if ($lienquan->count())
    <h2 class="tieudemuc">Sản phẩm cùng danh mục</h2>
    <div class="row g-4">
        @foreach ($lienquan as $ca)
            <div class="col-lg-4 col-md-6">
                @include('phanmenu.thesanpham', ['ca' => $ca])
            </div>
        @endforeach
    </div>
@endif

<script>
    // Bấm ảnh nhỏ thì đổi ảnh lớn và đánh dấu ảnh đang xem
    function chonAnh(nut) {
        document.getElementById('anhchinh').src = nut.dataset.anh;
        document.querySelectorAll('.anhnhochon').forEach(function (o) { o.classList.remove('dangchon'); });
        nut.classList.add('dangchon');
    }
</script>
@endsection