<div class="hopxacthuc">
    <button type="button" class="nutdong" onclick="dongCuaSo()">&times;</button>

    <div class="bieutuongxacthuc"><i class="fas fa-user-plus"></i></div>
    <h2>Đăng ký tài khoản</h2>

    <form method="POST" action="{{ route('dangky') }}">
        @csrf
        <div class="onhap">
            <input type="text" name="taikhoan" placeholder="Email hoặc số điện thoại"
                   value="{{ old('taikhoan') }}" required>
        </div>
        <div class="onhap matkhauhop">
            <input type="password" name="matkhau" placeholder="Mật khẩu" id="matkhau-dangky" required>
            <i class="fas fa-eye" onclick="hienAnMatKhau('matkhau-dangky', this)"></i>
        </div>
        <div class="onhap matkhauhop">
            <input type="password" name="nhaplaimatkhau" placeholder="Xác nhận mật khẩu" id="matkhau-xacnhan" required>
            <i class="fas fa-eye" onclick="hienAnMatKhau('matkhau-xacnhan', this)"></i>
        </div>
        <p class="ghichuxacthuc">Mật khẩu từ 6 ký tự, có ít nhất 1 chữ hoa và 1 ký tự đặc biệt.</p>

        <button type="submit" class="nutchinh">Đăng ký</button>
    </form>

    <div class="chuyenhuong">
        Đã có tài khoản?
        <a href="#" onclick="moCuaSo('dangnhap'); return false;">Đăng nhập ngay</a>
    </div>
</div>