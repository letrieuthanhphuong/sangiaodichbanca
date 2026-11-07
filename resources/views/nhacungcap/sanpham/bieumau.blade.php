@extends('bocuc.nhacungcap')

@section('tieude', $sanpham ? 'Sửa sản phẩm' : 'Thêm sản phẩm')
@section('tieudetrang', $sanpham ? 'Sửa sản phẩm' : 'Thêm sản phẩm')

@section('noidung')
@if ($errors->any())
    <div class="canhbao">
        @foreach ($errors->all() as $loi)
            <div>{{ $loi }}</div>
        @endforeach
    </div>
@endif

@if ($sanpham && $sanpham->trangthaiduyet === 'tuchoi')
    <div class="canhbao">
        <strong>Sản phẩm này bị từ chối.</strong> Lý do: {{ $sanpham->lydotuchoi }}<br>
        Sửa xong và lưu lại, sản phẩm sẽ được gửi duyệt lại.
    </div>
@endif

<form method="POST" enctype="multipart/form-data" class="khungbieumau"
      action="{{ $sanpham ? route('nhacungcap.sanpham.capnhat', $sanpham->id_sanpham) : route('nhacungcap.sanpham.luu') }}">
    @csrf
    @if ($sanpham)
        @method('PUT')
    @endif

    <div class="nhomtruong">
        <label>Tên sản phẩm</label>
        <input type="text" name="tensanpham" class="otruong"
               value="{{ old('tensanpham', $sanpham->tensanpham ?? '') }}" required>
    </div>

    <div class="row">
        <div class="col-md-6 nhomtruong">
            <label>Danh mục</label>
            <select name="id_danhmuc" class="otruong" required>
                @foreach ($danhmuc as $dm)
                    <option value="{{ $dm->id_danhmuc }}"
                        @selected(old('id_danhmuc', $sanpham->id_danhmuc ?? '') == $dm->id_danhmuc)>
                        {{ $dm->tendanhmuc }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 nhomtruong">
            <label>Loại cá (để trống nếu là phụ kiện)</label>
            <select name="id_loaica" class="otruong">
                <option value="">— Không chọn —</option>
                @foreach ($loaica as $lc)
                    <option value="{{ $lc->id_loaica }}"
                        @selected(old('id_loaica', $sanpham->id_loaica ?? '') == $lc->id_loaica)>
                        {{ $lc->tenloaica }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 nhomtruong">
            <label>Giá (đồng)</label>
            <input type="number" name="gia" class="otruong" min="1000" step="1000"
                   value="{{ old('gia', $sanpham ? (int) $sanpham->gia : '') }}" required>
        </div>
        <div class="col-md-4 nhomtruong">
            <label>Số lượng tồn</label>
            <input type="number" name="soluongton" class="otruong" min="0"
                   value="{{ old('soluongton', $sanpham->soluongton ?? 1) }}" required>
        </div>
        <div class="col-md-4 nhomtruong">
            <label>Trạng thái bán</label>
            <select name="trangthai" class="otruong">
                @foreach (['dangban' => 'Đang bán', 'hethang' => 'Hết hàng', 'an' => 'Ẩn'] as $giatri => $nhan)
                    <option value="{{ $giatri }}"
                        @selected(old('trangthai', $sanpham->trangthai ?? 'dangban') === $giatri)>{{ $nhan }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 nhomtruong">
            <label>Kích thước (cm)</label>
            <input type="number" name="kichthuoc" class="otruong" min="0" step="0.1"
                   value="{{ old('kichthuoc', $sanpham->kichthuoc ?? '') }}">
        </div>
        <div class="col-md-6 nhomtruong">
            <label>Xuất xứ</label>
            <input type="text" name="xuatxu" class="otruong"
                   value="{{ old('xuatxu', $sanpham->xuatxu ?? '') }}">
        </div>
    </div>

    <div class="nhomtruong">
        <label>Mô tả</label>
        <textarea name="mota" rows="4" class="otruong">{{ old('mota', $sanpham->mota ?? '') }}</textarea>
    </div>

    <div class="nhomtruong">
        <label>Hình ảnh (tối đa 5 ảnh, mỗi ảnh dưới 2 MB){{ $sanpham ? '' : ' *' }}</label>
        <input type="file" name="hinhanh[]" class="otruong" multiple
               accept="image/jpeg,image/png,image/webp">

        @if ($sanpham && $sanpham->hinhanh->count())
            <div class="luoianh">
                @foreach ($sanpham->hinhanh as $anh)
                    <label class="oanh">
                        @if (file_exists(public_path($anh->duongdan)))
                            <img src="{{ asset($anh->duongdan) }}" alt="">
                        @else
                            <div class="khongco">Chưa có tệp ảnh</div>
                        @endif
                        <input type="checkbox" name="xoaanh[]" value="{{ $anh->id_hinhanhsanpham }}"> Xóa ảnh này
                    </label>
                @endforeach
            </div>
        @endif
    </div>

    @unless ($sanpham)
        <p class="ghichuduyet">Sản phẩm mới sẽ ở trạng thái "Chờ duyệt" cho tới khi quản trị viên duyệt.</p>
    @endunless

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="nutcam">{{ $sanpham ? 'Lưu thay đổi' : 'Thêm sản phẩm' }}</button>
        <a href="{{ route('nhacungcap.sanpham') }}" class="nutvien">Hủy</a>
    </div>
</form>
@endsection