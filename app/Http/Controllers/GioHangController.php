<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GioHangController extends Controller
{
    private const TOIDA = 99;   // tối đa mỗi sản phẩm trong giỏ

    // Xem giỏ hàng, nhóm theo cửa hàng
    public function xem()
    {
        $dong = GioHang::with(['sanpham.hinhanh', 'sanpham.nhacungcap'])
            ->where('id_nguoidung', Auth::id())
            ->whereHas('sanpham', fn ($q) => $q->hienThi())   // bỏ sản phẩm đã ẩn hoặc không còn bán
            ->orderByDesc('ngaythem')                          
            ->get();

        // Đồng bộ với tồn kho: hết hàng thì xóa khỏi giỏ, vượt tồn thì hạ về số còn lại
        $canhbao = [];
        foreach ($dong as $d) {
            $ton = (int) $d->sanpham->soluongton;

            if ($ton <= 0) {
                $canhbao[] = $d->sanpham->tensanpham . ' đã hết hàng, đã xóa khỏi giỏ.';
                $d->delete();
            } elseif ($d->soluong > $ton) {
                $canhbao[] = $d->sanpham->tensanpham . ' chỉ còn ' . $ton . ' con, đã điều chỉnh số lượng.';
                $d->update(['soluong' => $ton]);
            }
        }

        $dong = $dong->filter(fn ($d) => $d->exists);

        if ($canhbao) {
            session()->now('thongbao', [
                'loai' => 'canhbao',
                'tieude' => 'Giỏ hàng đã thay đổi',
                'noidung' => implode(' ', $canhbao),
            ]);
        }

        $theocuahang = $dong->groupBy(fn ($d) => $d->sanpham->id_nhacungcap);
        $tong = $dong->sum(fn ($d) => $d->soluong * $d->sanpham->gia);

        return view('giohang.xem', compact('theocuahang', 'tong'));
    }

    // Thêm vào giỏ 
    public function them(Request $yeucau, int $id)
    {
        $soluong = max(1, min(self::TOIDA, (int) $yeucau->input('soluong', 1)));
        $sanpham = SanPham::hienThi()->findOrFail($id);

        // Không tự mua hàng của cửa hàng mình
        if ((int) $sanpham->nhacungcap?->id_nguoidung === (int) Auth::id()) {
            return back()->with('thongbao', [
                'loai' => 'loi',
                'tieude' => 'Không thể thêm',
                'noidung' => 'Đây là sản phẩm của cửa hàng bạn.',
            ]);
        }

        $ton = (int) $sanpham->soluongton;

        // Hết hàng
        if ($ton <= 0) {
            return back()->with('thongbao', [
                'loai' => 'loi',
                'tieude' => 'Hết hàng',
                'noidung' => 'Sản phẩm hiện đã hết hàng.',
            ]);
        }

        $dong = GioHang::firstOrNew([
            'id_nguoidung' => Auth::id(),
            'id_sanpham' => $sanpham->id_sanpham,
        ]);

        $moi = (int) $dong->soluong + $soluong;

        // Vượt tồn kho
        if ($moi > min($ton, self::TOIDA)) {
            return back()->with('thongbao', [
                'loai' => 'loi',
                'tieude' => 'Không đủ hàng',
                'noidung' => 'Sản phẩm này chỉ còn ' . $ton . ' con.',
            ]);
        }

        $dong->soluong = $moi;
        $dong->save();

        return back()->with('thongbao', [
            'loai' => 'thanhcong',
            'tieude' => 'Đã thêm vào giỏ',
            'noidung' => $sanpham->tensanpham,
        ]);
    }

    // Đổi số lượng một dòng trong giỏ
    public function capNhat(Request $yeucau, int $id)
    {
        $dong = GioHang::with('sanpham')
            ->where('id_nguoidung', Auth::id())
            ->findOrFail($id);

        $soluong = max(1, min(self::TOIDA, (int) $yeucau->input('soluong', 1)));
        $ton = (int) $dong->sanpham->soluongton;

        if ($soluong > $ton) {
            return back()->with('thongbao', [
                'loai' => 'loi',
                'tieude' => 'Không đủ hàng',
                'noidung' => 'Sản phẩm này chỉ còn ' . $ton . ' con.',
            ]);
        }

        $dong->update(['soluong' => $soluong]);

        return back();
    }

    // Xóa một dòng khỏi giỏ
    public function xoa(int $id)
    {
        GioHang::where('id_nguoidung', Auth::id())->where('id_giohang', $id)->delete();

        return back();
    }
}