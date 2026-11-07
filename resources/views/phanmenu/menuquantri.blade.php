@php
    $sodoncuahang = \App\Models\NhaCungCap::where('trangthaiduyet', 'choduyet')->count();
    $sosanphamcho = \App\Models\SanPham::where('trangthaiduyet', 'choduyet')->count();
@endphp

<nav class="menuben">
    <a href="{{ route('quantri.tongquan') }}"
       class="{{ request()->routeIs('quantri.tongquan') ? 'dangchon' : '' }}">Tổng quan</a>

    <a href="{{ route('quantri.nhacungcap') }}"
       class="{{ request()->routeIs('quantri.nhacungcap') ? 'dangchon' : '' }}">
        Duyệt nhà cung cấp
        @if ($sodoncuahang > 0)
            <span class="sodem">{{ $sodoncuahang }}</span>
        @endif
    </a>

    <a href="{{ route('quantri.sanpham') }}"
       class="{{ request()->routeIs('quantri.sanpham') ? 'dangchon' : '' }}">
        Kiểm duyệt sản phẩm
        @if ($sosanphamcho > 0)
            <span class="sodem">{{ $sosanphamcho }}</span>
        @endif
    </a>

    <span class="menusapco">Quản lý người dùng</span>
    <span class="menusapco">Đơn hàng</span>
    <span class="menusapco">Khiếu nại cá chết</span>
</nav>