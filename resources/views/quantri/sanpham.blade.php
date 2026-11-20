@extends('bocuc.quantri')

@section('tieude', 'Kiểm duyệt sản phẩm - Quản trị')
@section('tieudetrang', 'Kiểm duyệt sản phẩm')

@section('noidung')
@php
    $tab = ['choduyet' => 'Chờ duyệt', 'daduyet' => 'Đã duyệt', 'tuchoi' => 'Đã từ chối'];
@endphp

<div class="thanhtab">
    @foreach ($tab as $ma => $nhan)
        <a href="{{ route('quantri.sanpham', ['trangthai' => $ma]) }}" class="{{ $trangthai === $ma ? 'dangchon' : '' }}">
            {{ $nhan }} <span class="sotab">{{ $soluong[$ma] ?? 0 }}</span>
        </a>
    @endforeach
</div>

@if ($danhsach->isEmpty())
    <div class="trongduyet">
        <i class="fas fa-circle-check"></i>
        <p>{{ $trangthai === 'choduyet' ? 'Không có sản phẩm nào đang chờ duyệt.' : 'Chưa có sản phẩm nào ở mục này.' }}</p>
    </div>
@else
    <div class="danhsachduyet">
        @foreach ($danhsach as $sp)
            @php
                $duongdan = $sp->hinhanh->first()?->duongdan;
                $coanh = $duongdan && file_exists(public_path($duongdan));
            @endphp
            <article class="theduyet">
                <div class="anhduyet">
                    @if ($coanh)
                        <img src="{{ asset($duongdan) }}" alt="">
                    @else
                        <i class="fas fa-fish"></i>
                    @endif
                    @if ($sp->hinhanh->count() > 1)
                        <span class="soanh">{{ $sp->hinhanh->count() }} ảnh</span>
                    @endif
                </div>

                <div class="thongtinduyet">
                    <div class="dongphu">{{ $sp->nhacungcap->tencuahang }} · cập nhật {{ $sp->ngaycapnhat?->format('d/m/Y H:i') }}</div>
                    <h3>{{ $sp->tensanpham }}</h3>
                    <div class="giaduyet">{{ number_format($sp->gia, 0, ',', '.') }}đ <small>· tồn {{ $sp->soluongton }}</small></div>

                    <div class="nhomnhan">
                        @if ($sp->danhmuc)
                            <span class="nhanphu">{{ $sp->danhmuc->tendanhmuc }}</span>
                        @endif
                        @if ($sp->loaica)
                            <span class="nhanphu">{{ $sp->loaica->tenloaica }}</span>
                        @endif
                        @if ($sp->kichthuoc)
                            <span class="nhanphu">{{ rtrim(rtrim(number_format($sp->kichthuoc, 1), '0'), '.') }} cm</span>
                        @endif
                        @if ($sp->xuatxu)
                            <span class="nhanphu">{{ $sp->xuatxu }}</span>
                        @endif
                    </div>

                    @if ($sp->mota)
                        <p class="motaduyet">{{ $sp->mota }}</p>
                    @endif

                    @if ($sp->trangthaiduyet === 'tuchoi')
                        <p class="lydoduyet"><i class="fas fa-circle-xmark"></i> {{ $sp->lydotuchoi }}</p>
                    @endif
                </div>

                <div class="hanhdongduyet">
                    @if ($sp->trangthaiduyet === 'choduyet')
                        <form method="POST" action="{{ route('quantri.sanpham.duyet', $sp->id_sanpham) }}"
                              data-xacnhan-tieude="Duyệt sản phẩm này?"
                              data-xacnhan-noidung="“{{ $sp->tensanpham }}” sẽ hiện trên trang chủ nếu đang bán."
                              data-xacnhan-nut="Duyệt">
                            @csrf
                            <button type="submit" class="nutduyet"><i class="fas fa-check"></i> Duyệt</button>
                        </form>

                        <button type="button" class="nuttuchoi"
                                data-url="{{ route('quantri.sanpham.tuchoi', $sp->id_sanpham) }}"
                                data-ten="{{ $sp->tensanpham }}"
                                onclick="moTuChoi(this)"><i class="fas fa-xmark"></i> Từ chối</button>
                    @elseif ($sp->trangthaiduyet === 'daduyet')
                        <span class="nhantrangthai daduyet">Đã duyệt</span>
                    @else
                        <span class="nhantrangthai tuchoi">Từ chối</span>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif

@include('phanmenu.hoptuchoi', ['lydomau' => [
    'Ảnh sản phẩm chưa rõ',
    'Thông tin chưa đầy đủ',
    'Giá chưa hợp lý',
    'Sản phẩm không phù hợp với sàn',
]])
@endsection