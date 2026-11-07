@extends('bocuc.quantri')

@section('tieude', 'Duyệt người bán - Quản trị')
@section('tieudetrang', 'Duyệt nhà cung cấp')

@section('noidung')
<div class="thanhloc">
    <a href="{{ route('quantri.nhacungcap', ['trangthai' => 'choduyet']) }}"
       class="{{ $trangthai === 'choduyet' ? 'dangchon' : '' }}">Chờ duyệt</a>
    <a href="{{ route('quantri.nhacungcap', ['trangthai' => 'daduyet']) }}"
       class="{{ $trangthai === 'daduyet' ? 'dangchon' : '' }}">Đã duyệt</a>
    <a href="{{ route('quantri.nhacungcap', ['trangthai' => 'tuchoi']) }}"
       class="{{ $trangthai === 'tuchoi' ? 'dangchon' : '' }}">Đã từ chối</a>
</div>

<div class="table-responsive">
    <table class="bang">
        <thead>
            <tr>
                <th>Cửa hàng</th>
                <th>Người đăng ký</th>
                <th>Liên hệ</th>
                <th>Mã số thuế</th>
                <th>Ngày gửi</th>
                <th style="min-width: 260px">Xử lý</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($danhsach as $don)
                <tr>
                    <td>
                        <strong>{{ $don->tencuahang }}</strong><br>
                        <small class="text-secondary">{{ $don->diachi }}</small>
                        @if ($don->mota)
                            <br><small class="text-secondary">{{ $don->mota }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $don->nguoidung->hoten }}<br>
                        <small>{{ $don->nguoidung->email ?? $don->nguoidung->sodienthoai }}</small>
                    </td>
                    <td>{{ $don->sodienthoai }}</td>
                    <td>{{ $don->masothue ?: '—' }}</td>
                    <td>{{ $don->ngaytao?->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($don->trangthaiduyet === 'choduyet')
                            <form method="POST" action="{{ route('quantri.nhacungcap.duyet', $don->id_nhacungcap) }}"
                                  onsubmit="return confirm('Duyệt cửa hàng này?')">
                                @csrf
                                <button type="submit" class="nutcam nutnho">Duyệt</button>
                            </form>

                            <form method="POST" action="{{ route('quantri.nhacungcap.tuchoi', $don->id_nhacungcap) }}"
                                  class="d-flex gap-2 mt-2">
                                @csrf
                                <input type="text" name="lydotuchoi" class="otruong" placeholder="Lý do từ chối" required>
                                <button type="submit" class="nutvien nutnho">Từ chối</button>
                            </form>
                        @elseif ($don->trangthaiduyet === 'daduyet')
                            Đã duyệt lúc {{ $don->ngayduyet?->format('d/m/Y H:i') }}
                        @else
                            Từ chối: {{ $don->lydotuchoi }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary">Không có đơn nào.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection