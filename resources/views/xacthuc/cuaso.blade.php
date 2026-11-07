{{-- Hai cửa sổ nổi: đăng nhập và đăng ký. Bấm ra ngoài vùng hộp để đóng --}}
<div class="nencuaso" id="cuaso-dangnhap" onclick="if (event.target === this) dongCuaSo()">
    @include('xacthuc.dangnhap')
</div>

<div class="nencuaso" id="cuaso-dangky" onclick="if (event.target === this) dongCuaSo()">
    @include('xacthuc.dangky')
</div>

