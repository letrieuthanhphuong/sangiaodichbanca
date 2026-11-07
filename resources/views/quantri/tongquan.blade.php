@extends('bocuc.quantri')

@section('tieude', 'Tổng quan - Quản trị')
@section('tieudetrang', 'Tổng quan hệ thống')

@section('noidung')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="thethongke">
            <small>Người dùng</small>
            <strong>{{ $thongke['nguoidung'] }}</strong>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="thethongke">
            <small>Cửa hàng đã duyệt</small>
            <strong>{{ $thongke['cuahang'] }}</strong>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="thethongke noibat">
            <small>Đơn chờ duyệt</small>
            <strong>{{ $thongke['choduyet'] }}</strong>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="thethongke">
            <small>Đơn bị từ chối</small>
            <strong>{{ $thongke['tuchoi'] }}</strong>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="tieudemuc m-0">Đơn xin bán hàng mới</h2>
    <a href="{{ route('quantri.nhacungcap') }}" class="nutvien nutnho">Xem tất cả</a>
</div>

<div class="table-responsive">
    <table class="bang">
        <thead>
            <tr>
                <th>Cửa hàng</th>
                <th>Người đăng ký</th>
                <th>Ngày gửi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($donmoi as $don)
                <tr>
                    <td>{{ $don->tencuahang }}</td>
                    <td>{{ $don->nguoidung->hoten }}<br><small>{{ $don->nguoidung->email ?? $don->nguoidung->sodienthoai }}</small></td>
                    <td>{{ $don->ngaytao?->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-secondary">Chưa có đơn nào chờ duyệt.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection