@extends('bocuc.khung')

@section('tieude', ($tukhoa !== '' ? 'Tìm kiếm: ' . $tukhoa : 'Tìm kiếm') . ' - AquaMarket')

@section('noidung')
<h2 class="tieudemuc">Kết quả tìm kiếm</h2>

@if ($tukhoa === '')
    {{-- Chưa nhập gì --}}
    <div class="khongketqua">
        <i class="fas fa-magnifying-glass"></i>
        <p>Bạn muốn tìm gì?</p>
        <small>Nhập tên cá, danh mục, cửa hàng hoặc giá tiền vào ô tìm kiếm ở đầu trang.</small>
    </div>
@else
    <p class="ghichutimkiem">
        Có <strong>{{ $ketqua->count() }}</strong> sản phẩm cho “{{ $tukhoa }}”
    </p>

    <div class="row g-4">
        @forelse ($ketqua as $ca)
            <div class="col-lg-4 col-md-6">
                @include('phanmenu.thesanpham', ['ca' => $ca])
            </div>
        @empty
            <div class="col-12">
                <div class="khongketqua">
                    <i class="fas fa-fish"></i>
                    <p>Không có sản phẩm nào khớp với “{{ $tukhoa }}”.</p>
                    <small>Thử từ khóa ngắn hơn như “koi” hoặc “betta”, hoặc gõ giá tiền như 350000.</small>
                    <a href="/" class="nutvien">Về trang chủ</a>
                </div>
            </div>
        @endforelse
    </div>
@endif
@endsection