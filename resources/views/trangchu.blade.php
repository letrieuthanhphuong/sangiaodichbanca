@extends('bocuc.khung')

@section('noidung')
    {{-- Banner trang chủ --}}
    <section class="bannertrangchu">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="nhan">Sàn giao dịch cá cảnh số 1</span>
                <h1>Sàn mua bán <em>cá cảnh</em> &amp; sinh vật thủy sinh</h1>
                <p>Kết nối người nuôi và trại cá uy tín trên toàn quốc, giao hàng an toàn.</p>

                <a href="#canhmoi" class="nutcam">Mua ngay</a>

                {{-- Nút thứ hai thay đổi theo loại tài khoản --}}
                @guest
                    <a href="#" class="nutvien ms-2" onclick="moCuaSo('dangnhap'); return false;">Đăng ký nhà cung cấp</a>
                @else
                    @if (Auth::user()->laNhaCungCap())
                        <a href="{{ route('nhacungcap.tongquan') }}" class="nutvien ms-2">Kênh nhà cung cấp</a>
                    @elseif (! Auth::user()->laQuanTri())
                        <a href="#" class="nutvien ms-2" onclick="moCuaSo('nhacungcap'); return false;">Đăng ký nhà cung cấp</a>
                    @endif
                @endguest
            </div>
            <div class="col-md-6">
                @if (file_exists(public_path('images/ca/7.jpg')))
                    <img src="{{ asset('images/ca/7.jpg') }}" alt="Cá cảnh">
                @endif
            </div>
        </div>
    </section>

    {{-- Cửa hàng uy tín: chỉ hiện nhà cung cấp đã được duyệt --}}
    <h2 class="tieudemuc">Cửa hàng uy tín</h2>
    <div class="row g-3">
        @forelse ($cuahang as $ch)
            <div class="col-lg-3 col-md-4 col-6">
                <div class="thecuahang">
                    <div class="chucaidau">{{ mb_strtoupper(mb_substr($ch->tencuahang, 0, 1)) }}</div>
                    <div class="tencuahangthe">{{ $ch->tencuahang }}</div>
                    <div class="diachicuahang"><i class="fas fa-location-dot"></i> {{ $ch->diachi }}</div>
                    @if ($ch->mota)
                        <p class="motacuahang">{{ Str::limit($ch->mota, 70) }}</p>
                    @endif
                    <div class="soluongcuahang">{{ $ch->soluong }} sản phẩm</div>
                </div>
            </div>
        @empty
            <p class="text-secondary">Chưa có cửa hàng nào.</p>
        @endforelse
    </div>

    {{-- Cá mới đăng bán: chỉ hiện sản phẩm đã duyệt và đang bán --}}
    <h2 class="tieudemuc" id="canhmoi">Cá mới đăng bán</h2>
    <div class="row g-4">
        @forelse ($danhsachca as $ca)
            @php
                $duongdananh = $ca->hinhanh->first()?->duongdan;
                $coanh = $duongdananh && file_exists(public_path($duongdananh));
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="theca">
                    <div class="anhca">
                        @if ($coanh)
                            <img src="{{ asset($duongdananh) }}" alt="{{ $ca->tensanpham }}">
                        @endif
                    </div>
                    <div class="noidungthe">
                        <div class="tenca">{{ $ca->tensanpham }}</div>
                        <div class="tencuahang">{{ $ca->nhacungcap->tencuahang }}</div>
                        <div class="giaca">{{ number_format($ca->gia, 0, ',', '.') }}đ</div>
                        <a href="#" class="nutcam">Thêm vào giỏ</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-secondary">Chưa có sản phẩm nào.</p>
        @endforelse
    </div>
@endsection