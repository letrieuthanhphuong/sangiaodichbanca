<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class XacThucController extends Controller
{
    // Quay lại trang trước, hiện thông báo và mở lại đúng cửa sổ
    private function baoLoi(Request $yeucau, string $cuaso, string $loai, string $tieude, string $noidung)
    {
        return back()
            ->withInput($yeucau->only('taikhoan'))
            ->with('thongbao', ['loai' => $loai, 'tieude' => $tieude, 'noidung' => $noidung])
            ->with('mocuaso', $cuaso);
    }
    // ================= ĐĂNG KÝ =================
    public function dangKy(Request $yeucau)
    {
        $taikhoan = trim($yeucau->input('taikhoan', ''));
        $matkhau = $yeucau->input('matkhau', '');
        $nhaplai = $yeucau->input('nhaplaimatkhau', '');
        $email = null;
        $sdt = null;
        // 1. Tài khoản trống
        if ($taikhoan === '') {
            return $this->baoLoi($yeucau, 'dangky', 'loi', 'Lỗi', 'Vui lòng nhập email hoặc số điện thoại!');
        }
        // 2. Phân loại email hay số điện thoại
        if (filter_var($taikhoan, FILTER_VALIDATE_EMAIL)) {
            $email = $taikhoan;
        } elseif (preg_match('/^0[0-9]{9,10}$/', $taikhoan)) {
            $sdt = $taikhoan;
        } else {
            return $this->baoLoi($yeucau, 'dangky', 'loi', 'Lỗi', 'Vui lòng nhập email hoặc số điện thoại hợp lệ.');
        }
        // 3. Mật khẩu trống
        if ($matkhau === '') {
            return $this->baoLoi($yeucau, 'dangky', 'loi', 'Lỗi', 'Vui lòng nhập mật khẩu!');
        }
        // 4. Độ mạnh: từ 6 ký tự, có 1 chữ hoa, có 1 ký tự đặc biệt
        if (! preg_match('/^(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{6,}$/', $matkhau)) {
            return $this->baoLoi($yeucau, 'dangky', 'loi', 'Mật khẩu yếu!',
                'Mật khẩu phải từ 6 ký tự, gồm ít nhất 1 chữ hoa và 1 ký tự đặc biệt!');
        }
        // 5. Nhập lại mật khẩu
        if ($matkhau !== $nhaplai) {
            return $this->baoLoi($yeucau, 'dangky', 'loi', 'Lỗi', 'Mật khẩu nhập lại không khớp!');
        }
        // 6. Trùng trong database
        $datontai = $email
            ? NguoiDung::where('email', $email)->exists()
            : NguoiDung::where('sodienthoai', $sdt)->exists();

        if ($datontai) {
            return $this->baoLoi($yeucau, 'dangky', 'loi', 'Đăng ký thất bại!', 'Email hoặc số điện thoại đã được đăng ký.');
        }
        NguoiDung::create([
            'hoten' => $email ? explode('@', $email)[0] : $sdt,   // tạm lấy phần trước @, sau này cho sửa trong hồ sơ
            'email' => $email,
            'sodienthoai' => $sdt,
            'matkhau' => Hash::make($matkhau),
            'vaitro' => 'khachhang',
            'trangthai' => 'hoatdong',
        ]);
        // Đăng ký xong thì mở cửa sổ đăng nhập, điền sẵn tài khoản vừa tạo
        return redirect('/')
            ->withInput(['taikhoan' => $taikhoan])
            ->with('thongbao', ['loai' => 'thanhcong', 'tieude' => 'Đăng ký thành công!', 'noidung' => 'Bạn có thể đăng nhập ngay.'])
            ->with('mocuaso', 'dangnhap');
    }
    // ================= ĐĂNG NHẬP =================
    public function dangNhap(Request $yeucau)
    {
        $taikhoan = trim($yeucau->input('taikhoan', ''));
        $matkhau = $yeucau->input('matkhau', '');

        if ($taikhoan === '' || $matkhau === '') {
            return $this->baoLoi($yeucau, 'dangnhap', 'canhbao', 'Thông tin trống!',
                'Vui lòng nhập đầy đủ email hoặc số điện thoại và mật khẩu.');
        }
        $nguoidung = NguoiDung::where('email', $taikhoan)->orWhere('sodienthoai', $taikhoan)->first();
        // Sai tài khoản hay sai mật khẩu đều báo chung một câu, để người lạ không dò được tài khoản nào tồn tại
        if (! $nguoidung || ! Hash::check($matkhau, $nguoidung->matkhau)) {
            return $this->baoLoi($yeucau, 'dangnhap', 'loi', 'Đăng nhập thất bại!', 'Tên đăng nhập hoặc mật khẩu không đúng.');
        }
        // Kiểm tra khóa sau khi mật khẩu đúng, nên chỉ chủ tài khoản mới biết tài khoản bị khóa
        if ($nguoidung->trangthai === 'khoa') {
            return $this->baoLoi($yeucau, 'dangnhap', 'canhbao', 'Tài khoản bị khóa!',
                'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
        }
        Auth::login($nguoidung);
        $yeucau->session()->regenerate();
        // Quản trị viên
        if ($nguoidung->vaitro === 'quantri') {
            return redirect('/quantri/nhacungcap')->with('thongbao', [
                'loai' => 'thanhcong',
                'tieude' => 'Đăng nhập thành công!',
                'noidung' => 'Chào mừng quản trị viên ' . $nguoidung->hoten,
            ]);
        }
        // Khách hàng (kể cả người bán)
        return redirect()->intended('/')->with('thongbao', [
            'loai' => 'thanhcong',
            'tieude' => 'Đăng nhập thành công!',
            'noidung' => 'Chào mừng bạn quay lại AquaMarket!',
        ]);
    }

    // ================= ĐĂNG XUẤT =================
    public function dangXuat(Request $yeucau)
    {
        Auth::logout();
        $yeucau->session()->invalidate();
        $yeucau->session()->regenerateToken();
        return redirect('/')->with('thongbao', [
            'loai' => 'thongtin',
            'tieude' => 'Đã đăng xuất',
            'noidung' => 'Hẹn gặp lại bạn!',
        ]);
    }
}