@extends('bocuc.khung')

@section('tieude', 'Giỏ hàng - AquaMarket')

@section('noidung')
<div class="dausanpham">
    <h1>Giỏ hàng</h1>
</div>

@forelse ($theocuahang as $dongcuahang)
    <div class="cuahanggio">
        <div class="tencuahanggio">
            <i class="fas fa-store"></i> {{ $dongcuahang->first()->sanpham->nhacungcap->tencuahang }}
        </div>

        @foreach ($dongcuahang as $d)
            @php
                $sp = $d->sanpham;
                $duongdan = $sp->hinhanh->first()?->duongdan;
                $coanh = $duongdan && file_exists(public_path($duongdan));
            @endphp
            <div class="donggio" data-ten="{{ $sp->tensanpham }}" data-xoa="{{ route('giohang.xoa', $d->id_giohang) }}">
                <div class="anhgio">
                    @if ($coanh)
                        <img src="{{ asset($duongdan) }}" alt="{{ $sp->tensanpham }}">
                    @endif
                </div>

                <div class="thongtingio">
                    <div class="tengio">{{ $sp->tensanpham }}</div>
                    <div class="giagio">{{ number_format($sp->gia, 0, ',', '.') }}đ</div>
                </div>

                {{-- Số lượng: bấm − hoặc + là cập nhật luôn; giảm xuống 0 thì hỏi xóa --}}
                <form action="{{ route('giohang.capnhat', $d->id_giohang) }}" method="POST" class="soluonggio">
                    @csrf
                    @method('PATCH')
                    <button type="button" class="nutsl" onclick="doiSoLuong(this, -1)" aria-label="Giảm">−</button>
                    <input type="number" name="soluong" value="{{ $d->soluong }}" data-cu="{{ $d->soluong }}"
                           min="1" max="99" onchange="guiSoLuong(this)">
                    <button type="button" class="nutsl" onclick="doiSoLuong(this, 1)" aria-label="Tăng">+</button>
                </form>

                <div class="thanhtiengio">{{ number_format($d->soluong * $sp->gia, 0, ',', '.') }}đ</div>

                <button type="button" class="nutxoagio" title="Xóa khỏi giỏ"
                        onclick="hoiXoa(this.closest('.donggio'))">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endforeach
    </div>
@empty
    <div class="khongketqua">
        <i class="fas fa-cart-shopping"></i>
        <p>Giỏ hàng đang trống.</p>
        <a href="{{ route('sanpham.danhsach') }}" class="nutvien">Tiếp tục mua hàng</a>
    </div>
@endforelse

@if ($theocuahang->count())
    <div class="tonggio">
        <a href="{{ route('sanpham.danhsach') }}" class="nutvien me-auto">
            <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
        </a>
        <div>Tổng cộng: <strong>{{ number_format($tong, 0, ',', '.') }}đ</strong></div>
        <a href="#" class="nutcam">Đặt hàng</a>
    </div>


{{-- Hộp hỏi xóa: dùng chung cho thùng rác và khi giảm số lượng về 0 --}}
<div class="nenhoixoa" id="hoixoa">
    <div class="hopxoa">
        <h3>Xóa khỏi giỏ hàng?</h3>
        <p>Bạn muốn xóa <strong id="tenxoagio"></strong> khỏi giỏ hàng không?</p>
        <div class="nuthopxoa">
            <button type="button" class="nutvien" onclick="dongHoiXoa()">Không</button>
            <form id="formxoagio" method="POST" action="#">
                @csrf
                @method('DELETE')
                <button type="submit" class="nutcam">Xóa</button>
            </form>
        </div>
    </div>
</div>

<script>
    var hopXoa = document.getElementById('hoixoa');
    var formXoa = document.getElementById('formxoagio');
    var tenXoa = document.getElementById('tenxoagio');
    var osoDangSua = null;   // ô số lượng đang bị giảm về 0 (để trả lại số cũ nếu bấm "Không")

    // Mở hộp hỏi xóa cho một dòng trong giỏ
    function hoiXoa(dong, oso) {
        formXoa.action = dong.dataset.xoa;
        tenXoa.textContent = dong.dataset.ten;
        osoDangSua = oso || null;
        hopXoa.classList.add('mo');
    }

    // Bấm "Không": đóng hộp, trả số lượng về như cũ
    function dongHoiXoa() {
        hopXoa.classList.remove('mo');
        if (osoDangSua) {
            osoDangSua.value = osoDangSua.dataset.cu;
            osoDangSua = null;
        }
    }

    // Gửi số lượng mới; nhỏ hơn 1 thì hỏi xóa thay vì gửi
    function guiSoLuong(oso) {
        var v = parseInt(oso.value, 10);

        if (isNaN(v) || v < 1) {
            hoiXoa(oso.closest('.donggio'), oso);
            return;
        }

        if (v > 99) v = 99;
        oso.value = v;

        if (String(v) !== oso.dataset.cu) {
            oso.form.submit();
        }
    }

    // Bấm − hoặc +
    function doiSoLuong(nut, buoc) {
        var oso = nut.parentElement.querySelector('input');
        oso.value = (parseInt(oso.value, 10) || 0) + buoc;
        guiSoLuong(oso);
    }

    // Bấm ra ngoài hộp hoặc nhấn Esc cũng là "Không"
    hopXoa.addEventListener('click', function (e) {
        if (e.target === hopXoa) dongHoiXoa();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && hopXoa.classList.contains('mo')) dongHoiXoa();
    });
</script>
@endif
@endsection