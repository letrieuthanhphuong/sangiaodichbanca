@extends('bocuc.quantri')

@section('tieude', 'Kiểm duyệt sản phẩm - Quản trị')
@section('tieudetrang', 'Kiểm duyệt sản phẩm')

@section('noidung')
<div class="thanhloc">
    <a href="{{ route('quantri.sanpham', ['trangthai' => 'choduyet']) }}"
       class="{{ $trangthai === 'choduyet' ? 'dangchon' : '' }}">Chờ duyệt</a>
    <a href="{{ route('quantri.sanpham', ['trangthai' => 'daduyet']) }}"
       class="{{ $trangthai === 'daduyet' ? 'dangchon' : '' }}">Đã duyệt</a>
    <a href="{{ route('quantri.sanpham', ['trangthai' => 'tuchoi']) }}"
       class="{{ $trangthai === 'tuchoi' ? 'dangchon' : '' }}">Đã từ chối</a>
</div>

<div class="table-responsive">
    <table class="bang">
        <thead>
            <tr>
                <th style="width: 90px">Ảnh</th>
                <th>Sản phẩm</th>
                <th>Nhà cung cấp</th>
                <th>Giá / Tồn kho</th>
                <th>Cập nhật</th>
                <th style="min-width: 260px">Xử lý</th>
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
                        <small class="text-secondary">
                            {{ $sp->danhmuc?->tendanhmuc }}@if ($sp->loaica) · {{ $sp->loaica->tenloaica }}@endif
                        </small>
                        @if ($sp->kichthuoc || $sp->xuatxu)
                            <br><small>
                                @if ($sp->kichthuoc) {{ rtrim(rtrim(number_format($sp->kichthuoc, 1), '0'), '.') }} cm @endif
                                @if ($sp->kichthuoc && $sp->xuatxu) · @endif
                                {{ $sp->xuatxu }}
                            </small>
                        @endif
                        @if ($sp->mota)
                            <br><small class="text-secondary">{{ Str::limit($sp->mota, 120) }}</small>
                        @endif
                    </td>
                    <td>{{ $sp->nhacungcap->tencuahang }}</td>
                    <td>
                        {{ number_format($sp->gia, 0, ',', '.') }}đ<br>
                        <small class="text-secondary">Tồn: {{ $sp->soluongton }}</small>
                    </td>
                    <td>{{ $sp->ngaycapnhat?->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($sp->trangthaiduyet === 'choduyet')
                            <form method="POST" action="{{ route('quantri.sanpham.duyet', $sp->id_sanpham) }}"
                                  onsubmit="return confirm('Duyệt sản phẩm này?')">
                                @csrf
                                <button type="submit" class="nutcam nutnho">Duyệt</button>
                            </form>

                            <form method="POST" action="{{ route('quantri.sanpham.tuchoi', $sp->id_sanpham) }}"
                                  class="d-flex gap-2 mt-2">
                                @csrf
                                <input type="text" name="lydotuchoi" class="otruong" placeholder="Lý do từ chối" required>
                                <button type="submit" class="nutvien nutnho">Từ chối</button>
                            </form>
                        @elseif ($sp->trangthaiduyet === 'daduyet')
                            <span class="nhantrangthai daduyet">Đã duyệt</span>
                        @else
                            <span class="nhantrangthai tuchoi">Từ chối</span><br>
                            <small class="chuloi">{{ $sp->lydotuchoi }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary">Không có sản phẩm nào.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection