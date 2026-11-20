@extends('bocuc.quantri')

@section('tieude', 'Duyệt nhà cung cấp - Quản trị')
@section('tieudetrang', 'Duyệt nhà cung cấp')

@section('noidung')
@php
    $tab = ['choduyet' => 'Chờ duyệt', 'daduyet' => 'Đã duyệt', 'tuchoi' => 'Đã từ chối'];
@endphp

<div class="thanhtab">
    @foreach ($tab as $ma => $nhan)
        <a href="{{ route('quantri.nhacungcap', ['trangthai' => $ma]) }}" class="{{ $trangthai === $ma ? 'dangchon' : '' }}">
            {{ $nhan }} <span class="sotab">{{ $soluong[$ma] ?? 0 }}</span>
        </a>
    @endforeach
</div>

@if ($danhsach->isEmpty())
    <div class="trongduyet">
        <i class="fas fa-circle-check"></i>
        <p>{{ $trangthai === 'choduyet' ? 'Không có đơn nào đang chờ duyệt.' : 'Chưa có đơn nào ở mục này.' }}</p>
    </div>
@else
    <div class="danhsachduyet">
        @foreach ($danhsach as $don)
            <article class="theduyet theduyetcuahang">
                <div class="chucaiduyet">{{ mb_strtoupper(mb_substr($don->tencuahang, 0, 1)) }}</div>

                <div class="thongtinduyet">
                    <div class="dongphu">Gửi lúc {{ $don->ngaytao?->format('d/m/Y H:i') }}</div>
                    <h3>{{ $don->tencuahang }}</h3>

                    <div class="nhomnhan">
                        <span class="nhanphu"><i class="fas fa-user"></i> {{ $don->nguoidung->hoten }}</span>
                        <span class="nhanphu"><i class="fas fa-phone"></i> {{ $don->sodienthoai }}</span>
                        @if ($don->nguoidung->email)
                            <span class="nhanphu"><i class="fas fa-envelope"></i> {{ $don->nguoidung->email }}</span>
                        @endif
                        <span class="nhanphu {{ $don->masothue ? '' : 'canhbaonhan' }}">
                            <i class="fas fa-file-invoice"></i> {{ $don->masothue ?: 'Chưa có mã số thuế' }}
                        </span>
                    </div>

                    <p class="dongphu mt-2 mb-0"><i class="fas fa-location-dot"></i> {{ $don->diachi }}</p>

                    @if ($don->mota)
                        <p class="motaduyet">{{ $don->mota }}</p>
                    @endif

                    @if ($don->trangthaiduyet === 'daduyet')
                        <p class="dongphu mt-2 mb-0">Đã duyệt lúc {{ $don->ngayduyet?->format('d/m/Y H:i') }}</p>
                    @elseif ($don->trangthaiduyet === 'tuchoi')
                        <p class="lydoduyet"><i class="fas fa-circle-xmark"></i> {{ $don->lydotuchoi }}</p>
                    @endif
                </div>

                <div class="hanhdongduyet">
                    @if ($don->trangthaiduyet === 'choduyet')
                        <form method="POST" action="{{ route('quantri.nhacungcap.duyet', $don->id_nhacungcap) }}"
                              data-xacnhan-tieude="Duyệt nhà cung cấp này?"
                              data-xacnhan-noidung="“{{ $don->tencuahang }}” sẽ được đăng bán sản phẩm trên sàn."
                              data-xacnhan-nut="Duyệt">
                            @csrf
                            <button type="submit" class="nutduyet"><i class="fas fa-check"></i> Duyệt</button>
                        </form>

                        <button type="button" class="nuttuchoi"
                                data-url="{{ route('quantri.nhacungcap.tuchoi', $don->id_nhacungcap) }}"
                                data-ten="{{ $don->tencuahang }}"
                                onclick="moTuChoi(this)"><i class="fas fa-xmark"></i> Từ chối</button>
                    @elseif ($don->trangthaiduyet === 'daduyet')
                        <span class="nhantrangthai daduyet">Đã duyệt</span>
                    @else
                        <span class="nhantrangthai tuchoi">Từ chối</span>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif

@include('phanmenu.hoptuchoi', ['lydomau' => [
    'Chưa cung cấp mã số thuế hoặc giấy đăng ký kinh doanh',
    'Thông tin cửa hàng chưa rõ ràng',
    'Số điện thoại hoặc địa chỉ không hợp lệ',
]])
@endsection