<nav class="menuben">
    <a href="{{ route('nhacungcap.tongquan') }}"class="{{ request()->routeIs('nhacungcap.tongquan') ? 'dangchon' : '' }}">Tổng quan</a>
    <a href="{{ route('nhacungcap.sanpham') }}"class="{{ request()->routeIs('nhacungcap.sanpham*') ? 'dangchon' : '' }}">Sản phẩm của tôi</a>
    <span class="menusapco">Đơn hàng</span>
    <span class="menusapco">Khuyến mãi</span>
    <span class="menusapco">Khiếu nại cá chết</span>
</nav>