@extends('bocuc.nhacungcap')

@section('tieude', 'Tổng quan - Kênh người bán')
@section('tieudetrang', 'Tổng quan cửa hàng')

@section('noidung')
{{-- Các số này sẽ nối dữ liệu thật khi có bảng sanpham, donhang --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="thethongke">
            <small>Sản phẩm đang bán</small>
            <strong>—</strong>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="thethongke noibat">
            <small>Đơn hàng mới</small>
            <strong>—</strong>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="thethongke">
            <small>Doanh thu tháng này</small>
            <strong>—</strong>
        </div>
    </div>
</div>

<h2 class="tieudemuc">Thông tin cửa hàng</h2>

<div class="table-responsive">
    <table class="bang">
        <tr><th style="width: 200px">Tên cửa hàng</th><td>{{ $cuahang->tencuahang }}</td></tr>
        <tr><th>Địa chỉ</th><td>{{ $cuahang->diachi }}</td></tr>
        <tr><th>Số điện thoại</th><td>{{ $cuahang->sodienthoai }}</td></tr>
        <tr><th>Mã số thuế</th><td>{{ $cuahang->masothue ?: 'Chưa cung cấp' }}</td></tr>
        <tr><th>Giới thiệu</th><td>{{ $cuahang->mota ?: '—' }}</td></tr>
        <tr><th>Được duyệt ngày</th><td>{{ $cuahang->ngayduyet?->format('d/m/Y') }}</td></tr>
    </table>
</div>
@endsection