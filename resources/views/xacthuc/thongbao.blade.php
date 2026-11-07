@php
    $thongbao = session('thongbao');

    // Nhận cả chuỗi đơn giản (các chức năng đã làm trước) lẫn mảng đầy đủ
    if (is_string($thongbao)) {
        $thongbao = ['loai' => 'thanhcong', 'tieude' => 'Thông báo', 'noidung' => $thongbao];
    }
@endphp

@if ($thongbao)
    <div class="thongbaonoi {{ $thongbao['loai'] }}" id="thongbaonoi">
        <strong>{{ $thongbao['tieude'] }}</strong>
        <span>{{ $thongbao['noidung'] }}</span>
    </div>
@endif