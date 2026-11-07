@extends('bocuc.nhacungcap')

@section('tieude', 'Sản phẩm của tôi')
@section('tieudetrang', 'Sản phẩm của tôi')

@section('noidung')
@php
    $nhanban = ['dangban' => 'Đang bán', 'hethang' => 'Hết hàng', 'an' => 'Đã ẩn'];
    $nhanduyet = ['choduyet' => 'Chờ duyệt', 'daduyet' => 'Đã duyệt', 'tuchoi' => 'Bị từ chối'];
@endphp

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div class="thanhloc m-0">
        <a href="{{ route('nhacungcap.sanpham') }}" class="{{ ! $loc ? 'dangchon' : '' }}">Tất cả</a>
        <a href="{{ route('nhacungcap.sanpham', ['trangthaiduyet' => 'choduyet']) }}"
           class="{{ $loc === 'choduyet' ? 'dangchon' : '' }}">Chờ duyệt</a>
        <a href="{{ route('nhacungcap.sanpham', ['trangthaiduyet' => 'daduyet']) }}"
           class="{{ $loc === 'daduyet' ? 'dangchon' : '' }}">Đã duyệt</a>
        <a href="{{ route('nhacungcap.sanpham', ['trangthaiduyet' => 'tuchoi']) }}"
           class="{{ $loc === 'tuchoi' ? 'dangchon' : '' }}">Bị từ chối</a>
    </div>
    <a href="{{ route('nhacungcap.sanpham.them') }}" class="nutcam nutnho">+ Thêm sản phẩm</a>
</div>

<div class="table-responsive">
    <table class="bang">
        <thead>
            <tr>
                <th style="width: 90px">Ảnh</th>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Bán</th>
                <th>Kiểm duyệt</th>
                <th style="min-width: 150px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($danhsach as $sp)
                @php
                    $duongdan = $sp->hinhanh->first()?->duongdan;
                    $coanh = $duongdan && file_exists(public_path($duongdan));
                @endphp
                <tr>
                    <td>
                        @if ($coanh)
                            <img src="{{ asset($duongdan) }}" class="anhnho" alt="">
                        @else
                            <div class="anhnho"></div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $sp->tensanpham }}</strong><br>
                        <small class="text-secondary">{{ $sp->danhmuc?->tendanhmuc }}</small>
                        @if ($sp->trangthaiduyet === 'tuchoi')
                            <br><small class="chuloi">Lý do từ chối: {{ $sp->lydotuchoi }}</small>
                        @endif
                    </td>
                    <td>{{ number_format($sp->gia, 0, ',', '.') }}đ</td>
                    <td>{{ $sp->soluongton }}</td>
                    <td>{{ $nhanban[$sp->trangthai] }}</td>
                    <td><span class="nhantrangthai {{ $sp->trangthaiduyet }}">{{ $nhanduyet[$sp->trangthaiduyet] }}</span></td>
                    <td>
                        <a href="{{ route('nhacungcap.sanpham.sua', $sp->id_sanpham) }}" class="nutvien nutnho">Sửa</a>

                        <form method="POST" action="{{ route('nhacungcap.sanpham.xoa', $sp->id_sanpham) }}"
                              class="d-inline"
                              onsubmit="return confirm('Xóa sản phẩm này? Không thể hoàn tác.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="nutxoa">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary">Chưa có sản phẩm nào.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection