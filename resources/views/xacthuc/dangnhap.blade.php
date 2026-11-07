<div class="hopxacthuc">
    <button type="button" class="nutdong" onclick="dongCuaSo()">&times;</button>

    <div class="bieutuongxacthuc"><i class="fas fa-user-circle"></i></div>
    <h2>Đăng nhập</h2>

    <form method="POST" action="{{ route('dangnhap') }}">
        @csrf
        <div class="onhap">
            <input type="text" name="taikhoan" placeholder="Email hoặc số điện thoại"
                   value="{{ old('taikhoan') }}" required>
        </div>
        <div class="onhap matkhauhop">
            <input type="password" name="matkhau" placeholder="Mật khẩu" id="matkhau-dangnhap" required>
            <i class="fas fa-eye" onclick="hienAnMatKhau('matkhau-dangnhap', this)"></i>
        </div>

        <button type="submit" class="nutchinh">Đăng nhập</button>
    </form>

    <div class="chuyenhuong">
        Chưa có tài khoản?
        <a href="#" onclick="moCuaSo('dangky'); return false;">Đăng ký ngay</a>
    </div>
</div>