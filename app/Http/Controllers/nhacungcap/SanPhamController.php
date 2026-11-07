<?php

namespace App\Http\Controllers\NhaCungCap;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\HinhAnhSanPham;
use App\Models\LoaiCa;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SanPhamController extends Controller
{
    private function cuaHang()
    {
        return Auth::user()->nhacungcap;
    }

    // Chỉ tìm trong sản phẩm của chính cửa hàng này, sản phẩm của người khác sẽ báo 404
    private function timSanPham($id)
    {
        return $this->cuaHang()->sanpham()->with('hinhanh')->findOrFail($id);
    }

    private function duLieuBieuMau($sanpham = null): array
    {
        return [
            'sanpham' => $sanpham,
            'danhmuc' => DanhMuc::where('trangthai', 1)->orderBy('tendanhmuc')->get(),
            'loaica' => LoaiCa::orderBy('tenloaica')->get(),
        ];
    }

    private function luat(): array
    {
        return [
            'tensanpham' => 'required|string|max:180',
            'id_danhmuc' => 'required|integer|exists:danhmuc,id_danhmuc',
            'id_loaica' => 'nullable|integer|exists:loaica,id_loaica',
            'mota' => 'nullable|string|max:3000',
            'gia' => 'required|numeric|min:1000|max:999999999',
            'soluongton' => 'required|integer|min:0|max:100000',
            'kichthuoc' => 'nullable|numeric|min:0|max:999',
            'xuatxu' => 'nullable|string|max:100',
            'trangthai' => 'required|in:dangban,hethang,an',
            'hinhanh' => 'nullable|array|max:5',
            'hinhanh.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    private function thongBao(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute không hợp lệ.',
            'integer' => ':attribute phải là số nguyên.',
            'numeric' => ':attribute phải là số.',
            'exists' => ':attribute không hợp lệ.',
            'in' => ':attribute không hợp lệ.',
            'array' => 'Danh sách ảnh không hợp lệ.',
            'image' => 'Tệp tải lên phải là hình ảnh.',
            'mimes' => 'Ảnh chỉ nhận định dạng jpg, png hoặc webp.',
            'uploaded' => 'Tải ảnh lên thất bại (có thể ảnh quá nặng).',
            'min' => ':attribute quá nhỏ (tối thiểu :min).',
            'max' => ':attribute quá lớn hoặc quá dài (tối đa :max).',
            'gia.min' => 'Giá tối thiểu là 1.000đ.',
            'hinhanh.min' => 'Cần ít nhất 1 ảnh.',
            'hinhanh.max' => 'Mỗi sản phẩm tối đa 5 ảnh.',
            'hinhanh.*.max' => 'Mỗi ảnh tối đa 2 MB.',
        ];
    }

    private function tenTruong(): array
    {
        return [
            'tensanpham' => 'Tên sản phẩm',
            'id_danhmuc' => 'Danh mục',
            'id_loaica' => 'Loại cá',
            'mota' => 'Mô tả',
            'gia' => 'Giá',
            'soluongton' => 'Số lượng tồn',
            'kichthuoc' => 'Kích thước',
            'xuatxu' => 'Xuất xứ',
            'trangthai' => 'Trạng thái bán',
            'hinhanh' => 'Hình ảnh',
        ];
    }

    // Lưu các ảnh tải lên vào public/images/ca và ghi đường dẫn vào CSDL
    private function luuAnh(Request $yeucau, $sanpham): void
    {
        if (! $yeucau->hasFile('hinhanh')) {
            return;
        }

        $thutu = (int) HinhAnhSanPham::where('id_sanpham', $sanpham->id_sanpham)->max('thutu');

        foreach ($yeucau->file('hinhanh') as $tep) {
            $ten = $sanpham->id_sanpham . '_' . uniqid() . '.' . $tep->extension();
            $tep->move(public_path('images/ca'), $ten);

            HinhAnhSanPham::create([
                'id_sanpham' => $sanpham->id_sanpham,
                'duongdan' => 'images/ca/' . $ten,
                'thutu' => ++$thutu,
            ]);
        }
    }

    // Chỉ xóa tệp nằm trong images/ca để tránh xóa nhầm file khác
    private function xoaTepAnh(string $duongdan): void
    {
        if (str_starts_with($duongdan, 'images/ca/')) {
            $tep = public_path($duongdan);

            if (is_file($tep)) {
                @unlink($tep);
            }
        }
    }

    // ================= DANH SÁCH =================
    public function danhSach(Request $yeucau)
    {
        $loc = $yeucau->query('trangthaiduyet');

        $truyvan = $this->cuaHang()->sanpham()->with(['hinhanh', 'danhmuc']);

        if (in_array($loc, ['choduyet', 'daduyet', 'tuchoi'])) {
            $truyvan->where('trangthaiduyet', $loc);
        } else {
            $loc = null;
        }

        $danhsach = $truyvan->orderByDesc('id_sanpham')->get();

        return view('nhacungcap.sanpham.danhsach', compact('danhsach', 'loc'));
    }

    // ================= THÊM =================
    public function them()
    {
        return view('nhacungcap.sanpham.bieumau', $this->duLieuBieuMau());
    }

    public function luu(Request $yeucau)
    {
        $luat = $this->luat();
        $luat['hinhanh'] = 'required|array|min:1|max:5';   // thêm mới thì bắt buộc có ảnh

        $dulieu = $yeucau->validate($luat, $this->thongBao(), $this->tenTruong());

        DB::transaction(function () use ($yeucau, $dulieu) {
            $sanpham = $this->cuaHang()->sanpham()->create(
                Arr::except($dulieu, ['hinhanh']) + ['trangthaiduyet' => 'choduyet']
            );

            $this->luuAnh($yeucau, $sanpham);
        });

        return redirect()->route('nhacungcap.sanpham')->with('thongbao', [
            'loai' => 'thanhcong',
            'tieude' => 'Đã thêm sản phẩm',
            'noidung' => 'Sản phẩm đang chờ quản trị viên duyệt trước khi hiện trên trang chủ.',
        ]);
    }

    // ================= SỬA =================
    public function sua($id)
    {
        return view('nhacungcap.sanpham.bieumau', $this->duLieuBieuMau($this->timSanPham($id)));
    }

    public function capNhat(Request $yeucau, $id)
    {
        $sanpham = $this->timSanPham($id);

        $dulieu = $yeucau->validate($this->luat(), $this->thongBao(), $this->tenTruong());

        // Số ảnh sau khi sửa phải từ 1 đến 5
        $idanhxoa = array_filter((array) $yeucau->input('xoaanh', []), 'is_numeric');
        $soanhxoa = HinhAnhSanPham::where('id_sanpham', $sanpham->id_sanpham)
            ->whereIn('id_hinhanhsanpham', $idanhxoa)->count();
        $soanhmoi = count($yeucau->file('hinhanh', []));
        $tongsoanh = $sanpham->hinhanh->count() - $soanhxoa + $soanhmoi;

        if ($tongsoanh < 1 || $tongsoanh > 5) {
            return back()->withInput()->withErrors([
                'hinhanh' => $tongsoanh < 1 ? 'Sản phẩm cần có ít nhất 1 ảnh.' : 'Mỗi sản phẩm tối đa 5 ảnh.',
            ]);
        }

        $tepcanxoa = [];

        DB::transaction(function () use ($yeucau, $dulieu, $sanpham, $idanhxoa, $soanhxoa, $soanhmoi, &$tepcanxoa) {
            $sanpham->fill(Arr::except($dulieu, ['hinhanh']));

            // Sửa nội dung thì phải duyệt lại; chỉ đổi giá, tồn kho, trạng thái bán thì không
            $doinoidung = $sanpham->isDirty(['tensanpham', 'mota', 'id_danhmuc', 'id_loaica', 'kichthuoc', 'xuatxu'])
                || $soanhxoa > 0
                || $soanhmoi > 0;

            if ($sanpham->trangthaiduyet === 'tuchoi'
                || ($sanpham->trangthaiduyet === 'daduyet' && $doinoidung)) {
                $sanpham->trangthaiduyet = 'choduyet';
                $sanpham->lydotuchoi = null;
            }

            $sanpham->save();

            $anhcanxoa = HinhAnhSanPham::where('id_sanpham', $sanpham->id_sanpham)
                ->whereIn('id_hinhanhsanpham', $idanhxoa)->get();

            foreach ($anhcanxoa as $anh) {
                $tepcanxoa[] = $anh->duongdan;
                $anh->delete();
            }

            $this->luuAnh($yeucau, $sanpham);
        });

        // Xóa tệp ảnh sau khi lưu CSDL thành công
        foreach ($tepcanxoa as $duongdan) {
            $this->xoaTepAnh($duongdan);
        }

        $guiduyetlai = $sanpham->trangthaiduyet === 'choduyet';

        return redirect()->route('nhacungcap.sanpham')->with('thongbao', [
            'loai' => 'thanhcong',
            'tieude' => 'Đã lưu sản phẩm',
            'noidung' => $guiduyetlai
                ? 'Sản phẩm đã được gửi duyệt lại.'
                : 'Đã cập nhật thông tin sản phẩm.',
        ]);
    }

    // ================= XÓA =================
    public function xoa($id)
    {
        $sanpham = $this->timSanPham($id);
        $duongdananh = $sanpham->hinhanh->pluck('duongdan');

        try {
            $sanpham->delete();   // ảnh trong CSDL tự xóa theo (ON DELETE CASCADE)
        } catch (QueryException $loi) {
            // Mã 23000: vướng khóa ngoại, tức là sản phẩm đã có trong đơn hàng
            if ((string) $loi->getCode() !== '23000') {
                throw $loi;
            }

            return back()->with('thongbao', [
                'loai' => 'canhbao',
                'tieude' => 'Không thể xóa',
                'noidung' => 'Sản phẩm này đã có trong đơn hàng. Hãy chuyển trạng thái bán sang "Ẩn" thay vì xóa.',
            ]);
        }

        foreach ($duongdananh as $duongdan) {
            $this->xoaTepAnh($duongdan);
        }

        return redirect()->route('nhacungcap.sanpham')->with('thongbao', [
            'loai' => 'thongtin',
            'tieude' => 'Đã xóa sản phẩm',
            'noidung' => 'Sản phẩm và các ảnh của nó đã được xóa.',
        ]);
    }
}