<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\NhaCungCap;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DanhSachSanPhamController extends Controller
{
    // Số sản phẩm trên mỗi trang
    private const MOITRANG = 12;

    // Mã độ khó nuôi và chữ hiển thị
    private const DOKHO = [
        'de' => 'Dễ nuôi',
        'trungbinh' => 'Trung bình',
        'kho' => 'Khó nuôi',
    ];

    public function hienDanhSach(Request $yeucau)
    {
        //Dữ liệu cho cột lọc
        $dsdanhmuc = DanhMuc::where('trangthai', 1)
            ->withCount(['sanpham as soluong' => function ($truyvan) {
                $truyvan->hienThi();
            }])
            ->orderBy('id_danhmuc')
            ->get();

        $dscuahang = NhaCungCap::where('trangthaiduyet', 'daduyet')
            ->withCount(['sanpham as soluong' => function ($truyvan) {
                $truyvan->hienThi();
            }])
            ->orderBy('tencuahang')
            ->get()
            ->filter(fn ($ch) => $ch->soluong > 0)
            ->values();

        //Đọc điều kiện lọc từ địa chỉ 
        $tho = $yeucau->input('tukhoa', '');
        $tukhoa = is_string($tho) ? trim(mb_substr($tho, 0, 100)) : '';

        $danhmucchon = $this->laSo($yeucau->input('danhmuc'));
        if (! $dsdanhmuc->contains(fn ($dm) => (int) $dm->id_danhmuc === $danhmucchon)) {
            $danhmucchon = null;   // mã danh mục không có thật thì coi như không chọn
        }

        $idcuahang = $dscuahang->pluck('id_nhacungcap')->map(fn ($id) => (int) $id)->all();
        $cuahangchon = array_values(array_intersect($this->danhSachSo($yeucau->input('cuahang', [])), $idcuahang));

        $dokhochon = array_values(array_intersect(
            array_keys(self::DOKHO),
            array_filter((array) $yeucau->input('dokho', []), 'is_string')
        ));

        $giatu = $this->laSo($yeucau->input('giatu'));
        $giaden = $this->laSo($yeucau->input('giaden'));
        if ($giatu !== null && $giaden !== null && $giatu > $giaden) {
            [$giatu, $giaden] = [$giaden, $giatu];   // gõ ngược thì đổi chỗ
        }

        $sapxep = $yeucau->input('sapxep');
        if (! in_array($sapxep, ['moinhat', 'giatang', 'giagiam'], true)) {
            $sapxep = 'moinhat';
        }

        //Truy vấn sản phẩm 
        $truyvan = SanPham::hienThi()->with(['hinhanh', 'nhacungcap', 'danhmuc', 'loaica']);

        if ($danhmucchon) {
            $truyvan->where('id_danhmuc', $danhmucchon);
        }
        $danhmucchon = $this->laSo($yeucau->input('danhmuc'));
        if (! $dsdanhmuc->contains(fn ($dm) => (int) $dm->id_danhmuc === $danhmucchon)) {
            $danhmucchon = null;
        }
        // Tìm kiếm và danh mục là hai chế độ riêng: có từ khóa thì bỏ danh mục
        if ($tukhoa !== '') {
            $danhmucchon = null;
        }
        if ($cuahangchon) {
            $truyvan->whereIn('id_nhacungcap', $cuahangchon);
        }
        if ($giatu !== null) {
            $truyvan->where('gia', '>=', $giatu);
        }
        if ($giaden !== null) {
            $truyvan->where('gia', '<=', $giaden);
        }
        if ($dokhochon) {
            $truyvan->whereHas('loaica', function ($q) use ($dokhochon) {
                $q->whereIn('dokhonuoi', $dokhochon);
            });
        }

        if ($sapxep === 'giatang') {
            $truyvan->orderBy('gia')->orderByDesc('id_sanpham');
        } elseif ($sapxep === 'giagiam') {
            $truyvan->orderByDesc('gia')->orderByDesc('id_sanpham');
        } else {
            $truyvan->orderByDesc('id_sanpham');
        }

        $ketqua = $truyvan->get();

        //Tìm theo từ khóa (có hoặc không dấu)
        if ($tukhoa !== '') {
            $ketqua = $this->locTheoTuKhoa($ketqua, $tukhoa);
        }

        //Phân trang
        $tong = $ketqua->count();
        $trangcuoi = max(1, (int) ceil($tong / self::MOITRANG));
        $trang = min(max(1, LengthAwarePaginator::resolveCurrentPage()), $trangcuoi);

        $phantrang = new LengthAwarePaginator(
            $ketqua->forPage($trang, self::MOITRANG)->values(),
            $tong,
            self::MOITRANG,
            $trang,
            ['path' => $yeucau->url(), 'query' => $yeucau->query()]
        );

        //Các điều kiện đang lọc
        $bo = fn (array $thaydoi) => $yeucau->fullUrlWithQuery($thaydoi + ['page' => null]);
        $tien = fn ($so) => number_format($so, 0, ',', '.') . 'đ';

        $dangloc = [];

        if ($tukhoa !== '') {
            $dangloc[] = ['nhan' => 'Từ khóa: ' . $tukhoa, 'url' => $bo(['tukhoa' => null])];
        }

        $tendanhmuc = null;
        if ($danhmucchon) {
            $tendanhmuc = $dsdanhmuc->first(fn ($dm) => (int) $dm->id_danhmuc === $danhmucchon)->tendanhmuc;
            $dangloc[] = ['nhan' => 'Danh mục: ' . $tendanhmuc, 'url' => $bo(['danhmuc' => null])];
        }

        if ($giatu !== null || $giaden !== null) {
            if ($giatu !== null && $giaden !== null) {
                $nhan = 'Giá: ' . $tien($giatu) . ' - ' . $tien($giaden);
            } elseif ($giatu !== null) {
                $nhan = 'Giá: từ ' . $tien($giatu);
            } else {
                $nhan = 'Giá: đến ' . $tien($giaden);
            }
            $dangloc[] = ['nhan' => $nhan, 'url' => $bo(['giatu' => null, 'giaden' => null])];
        }

        foreach ($cuahangchon as $id) {
            $ten = $dscuahang->first(fn ($ch) => (int) $ch->id_nhacungcap === $id)->tencuahang;
            $dangloc[] = [
                'nhan' => 'Cửa hàng: ' . $ten,
                'url' => $bo(['cuahang' => array_values(array_diff($cuahangchon, [$id]))]),
            ];
        }

        foreach ($dokhochon as $ma) {
            $dangloc[] = [
                'nhan' => 'Độ khó: ' . self::DOKHO[$ma],
                'url' => $bo(['dokho' => array_values(array_diff($dokhochon, [$ma]))]),
            ];
        }

        // Địa chỉ "xóa bộ lọc": bỏ hết điều kiện nhưng giữ từ khóa đang tìm
        $giulai = [];
        if ($tukhoa !== '') {
            $giulai['tukhoa'] = $tukhoa;
        } elseif ($danhmucchon) {
            $giulai['danhmuc'] = $danhmucchon;
        }
        $urlxoaloc = route('sanpham.danhsach', $giulai);

        // Tiêu đề trang
        if ($tendanhmuc) {
            $tieude = $tendanhmuc;
        } elseif ($tukhoa !== '') {
            $tieude = 'Kết quả cho “' . $tukhoa . '”';
        } else {
            $tieude = 'Tất cả sản phẩm';
        }

        $dsdokho = self::DOKHO;

        return view('sanpham.danhsach', compact(
            'tieude', 'tong', 'phantrang', 'dangloc', 'urlxoaloc', 'tukhoa', 'sapxep',
            'dsdanhmuc', 'danhmucchon', 'giatu', 'giaden',
            'dscuahang', 'cuahangchon', 'dsdokho', 'dokhochon'
        ));
    }

    // Lọc danh sách theo từ khóa: khớp giá tiền, hoặc khớp chữ trong tên, cửa hàng, danh mục, loại cá
    private function locTheoTuKhoa($danhsach, string $tukhoa)
    {
        // Giá có thể gõ kiểu 12.500.000 hoặc 12,500,000
        $lagia = ctype_digit(str_replace(['.', ',', ' '], '', $tukhoa));
        $sogia = $lagia ? (float) str_replace(['.', ',', ' '], '', $tukhoa) : 0;

        $tukhoathuong = mb_strtolower($tukhoa, 'UTF-8');

        // Gõ không dấu (ví dụ "dia") thì tìm được cả chữ có dấu (Dĩa); gõ có dấu thì chỉ khớp đúng dấu
        $timkhongdau = ($tukhoathuong === $this->boDau($tukhoathuong));

        return $danhsach->filter(function ($sp) use ($lagia, $sogia, $tukhoathuong, $timkhongdau) {
            if ($lagia && (float) $sp->gia == $sogia) {
                return true;
            }

            $noidung = mb_strtolower(implode(' ', [
                $sp->tensanpham,
                $sp->nhacungcap?->tencuahang,
                $sp->danhmuc?->tendanhmuc,
                $sp->loaica?->tenloaica,
            ]), 'UTF-8');

            return str_contains($noidung, $tukhoathuong)
                || ($timkhongdau && str_contains($this->boDau($noidung), $tukhoathuong));
        })->values();
    }

    // Đọc một số nguyên dương từ giá trị gửi lên, không hợp lệ thì trả về null
    private function laSo($giatri): ?int
    {
        if (! is_scalar($giatri)) {
            return null;
        }

        $so = str_replace(['.', ',', ' '], '', (string) $giatri);

        if ($so === '' || strlen($so) > 12 || ! ctype_digit($so)) {
            return null;
        }

        return (int) $so;
    }

    // Đọc danh sách số (ví dụ cuahang[]=1&cuahang[]=2), bỏ giá trị không hợp lệ
    private function danhSachSo($giatri): array
    {
        $danhsach = [];

        foreach ((array) $giatri as $phantu) {
            $so = $this->laSo($phantu);

            if ($so !== null) {
                $danhsach[] = $so;
            }
        }

        return array_values(array_unique($danhsach));
    }

    // Đổi chữ có dấu thành không dấu để tìm "dia" ra "Dĩa", "rong" ra "Rồng"
    private function boDau($chuoi)
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        return strtr($chuoi, [
            'á'=>'a','à'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'đ'=>'d',
            'é'=>'e','è'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'í'=>'i','ì'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'ó'=>'o','ò'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'ú'=>'u','ù'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'ý'=>'y','ỳ'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        ]);
    }
}