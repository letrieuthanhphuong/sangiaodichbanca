<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DangKyNhaCungCapController extends Controller
{
    // Gõ thẳng /dang-ky-nguoi-ban thì về trang chủ và mở cửa sổ đăng ký bán hàng
    public function mo()
    {
        return redirect('/')->with('mocuaso', 'nhacungcap');
    }

    // Xử lý gửi đơn đăng ký
    public function guiDon(Request $yeucau)
    {
        $nguoidung = Auth::user();

        // Chỉ khách hàng mới được đăng ký (quản trị viên thì không)
        if ($nguoidung->vaitro !== 'khachhang') {
            return redirect('/')->with('thongbao', [
                'loai' => 'canhbao',
                'tieude' => 'Không thể đăng ký',
                'noidung' => 'Tài khoản quản trị không thể đăng ký bán hàng.',
            ]);
        }

        $hoso = NhaCungCap::where('id_nguoidung', $nguoidung->id_nguoidung)->first();

        // Đang chờ duyệt hoặc đã được duyệt thì không gửi thêm
        if ($hoso && $hoso->trangthaiduyet === 'choduyet') {
            return redirect('/')->with('thongbao', [
                'loai' => 'thongtin',
                'tieude' => 'Đơn đang chờ duyệt',
                'noidung' => 'Bạn đã gửi đơn trước đó, vui lòng chờ quản trị viên duyệt.',
            ]);
        }

        if ($hoso && $hoso->trangthaiduyet === 'daduyet') {
            return redirect('/')->with('thongbao', [
                'loai' => 'thongtin',
                'tieude' => 'Đã là người bán',
                'noidung' => 'Cửa hàng của bạn đã được duyệt rồi.',
            ]);
        }

        // Kiểm tra dữ liệu nhập vào (độ dài khớp với cột trong bảng)
        $kiemtra = Validator::make($yeucau->all(), [
            'tencuahang' => 'required|string|max:150',
            'diachi' => 'required|string|max:255',
            'sodienthoai' => ['required', 'regex:/^0[0-9]{9,10}$/'],
            'masothue' => 'nullable|string|max:20',
            'mota' => 'nullable|string|max:2000',
        ], [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute không hợp lệ.',
            'max' => ':attribute quá dài (tối đa :max ký tự).',
            'regex' => 'Số điện thoại cửa hàng không hợp lệ (bắt đầu bằng 0, gồm 10 hoặc 11 số).',
        ], [
            'tencuahang' => 'Tên cửa hàng',
            'diachi' => 'Địa chỉ',
            'sodienthoai' => 'Số điện thoại',
            'masothue' => 'Mã số thuế',
            'mota' => 'Mô tả',
        ]);

        if ($kiemtra->fails()) {
            return back()
                ->withInput()
                ->with('thongbao', [
                    'loai' => 'loi',
                    'tieude' => 'Thông tin chưa hợp lệ',
                    'noidung' => $kiemtra->errors()->first(),
                ])
                ->with('mocuaso', 'nhacungcap');
        }

        // Chưa có hồ sơ thì tạo mới, hồ sơ bị từ chối thì cập nhật và gửi lại
        NhaCungCap::updateOrCreate(
            ['id_nguoidung' => $nguoidung->id_nguoidung],
            $kiemtra->validated() + [
                'trangthaiduyet' => 'choduyet',
                'lydotuchoi' => null,
                'id_quantri' => null,
                'ngayduyet' => null,
            ]
        );

        return back()->with('thongbao', [
            'loai' => 'thanhcong',
            'tieude' => 'Đã gửi đơn đăng ký!',
            'noidung' => 'Vui lòng chờ quản trị viên duyệt.',
        ]);
    }
}