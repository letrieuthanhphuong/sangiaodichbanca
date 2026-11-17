{{-- Thanh danh mục dưới đầu trang, hiện ở mọi trang của khách --}}
@php
    $dsdanhmuc = \App\Models\DanhMuc::where('trangthai', 1)->orderBy('id_danhmuc')->get();
    $trangsanpham = request()->routeIs('sanpham.danhsach');
    $dangxem = (int) request('danhmuc');   // danh mục đang xem (0 nếu không chọn)
@endphp

<nav class="thanhdanhmuc" aria-label="Danh mục sản phẩm">
    <div class="container">
        <a href="{{ route('sanpham.danhsach') }}"
           class="{{ $trangsanpham && ! $dangxem ? 'dangchon' : '' }}">Tất cả</a>

        @foreach ($dsdanhmuc as $dm)
            <a href="{{ route('sanpham.danhsach', ['danhmuc' => $dm->id_danhmuc]) }}"
               class="{{ $trangsanpham && $dangxem === (int) $dm->id_danhmuc ? 'dangchon' : '' }}">{{ $dm->tendanhmuc }}</a>
        @endforeach
    </div>
</nav>