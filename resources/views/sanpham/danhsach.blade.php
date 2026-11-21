@extends('bocuc.khung')

@section('tieude', $tieude . ' - AquaMarket')

@section('noidung')
<div class="dausanpham">
    <div>
        <h1>{{ $tieude }}</h1>
        <div class="soketqua">{{ $tong }} sản phẩm</div>
    </div>

    <div class="d-flex align-items-center gap-3">
        {{-- Chỉ hiện ở màn hình nhỏ: bấm để mở hoặc đóng cột lọc --}}
        <button type="button" class="nutvien nutmoloc"
                onclick="document.getElementById('formloc').classList.toggle('mo')">
            <i class="fas fa-sliders"></i> Bộ lọc
        </button>

        {{-- Ô chọn nằm ngoài form nhưng thuộc form qua thuộc tính form="formloc", nên đổi là gửi cùng mọi bộ lọc --}}
        <label class="chonsapxep">
            Sắp xếp
            <select name="sapxep" form="formloc" onchange="this.form.submit()">
                <option value="moinhat" @selected($sapxep === 'moinhat')>Mới nhất</option>
                <option value="giatang" @selected($sapxep === 'giatang')>Giá tăng dần</option>
                <option value="giagiam" @selected($sapxep === 'giagiam')>Giá giảm dần</option>
            </select>
        </label>
    </div>
</div>

{{-- Các điều kiện đang lọc, bấm vào từng cái để bỏ --}}
@if ($dangloc)
    <div class="dangloc">
        @foreach ($dangloc as $chip)
            <a href="{{ $chip['url'] }}" class="chiploc">{{ $chip['nhan'] }} <i class="fas fa-xmark"></i></a>
        @endforeach
        <a href="{{ $urlxoaloc }}" class="xoaloc">Xóa bộ lọc</a>
    </div>
@endif

<div class="row g-4">
    {{-- Cột lọc --}}
    <aside class="col-lg-3">
        <form method="GET" action="{{ route('sanpham.danhsach') }}" id="formloc" class="cotloc">
            @if ($tukhoa !== '')
                <input type="hidden" name="tukhoa" value="{{ $tukhoa }}">
            @elseif ($danhmucchon)
                <input type="hidden" name="danhmuc" value="{{ $danhmucchon }}">
            @endif

            <div class="nhomloc">
                <h3>Danh mục</h3>

                <a href="{{ route('sanpham.danhsach') }}"
                class="dongloc dongdanhmuc {{ ! $danhmucchon && $tukhoa === '' ? 'dangchon' : '' }}">
                    <span><i class="chamchon"></i> Tất cả</span>
                </a>

                @foreach ($dsdanhmuc as $dm)
                    <a href="{{ route('sanpham.danhsach', ['danhmuc' => $dm->id_danhmuc]) }}"
                    class="dongloc dongdanhmuc {{ $danhmucchon === (int) $dm->id_danhmuc ? 'dangchon' : '' }}">
                        <span><i class="chamchon"></i> {{ $dm->tendanhmuc }}</span>
                        <small>{{ $dm->soluong }}</small>
                    </a>
                @endforeach
            </div>

            <div class="nhomloc">
                <h3>Khoảng giá (đồng)</h3>
                <div class="khoanggia">
                    <input type="number" name="giatu" class="otruongloc" min="0" step="1000"
                           placeholder="Từ" value="{{ $giatu }}">
                    <span>-</span>
                    <input type="number" name="giaden" class="otruongloc" min="0" step="1000"
                           placeholder="Đến" value="{{ $giaden }}">
                </div>
            </div>

            @if ($dscuahang->count())
                <div class="nhomloc">
                    <h3>Cửa hàng</h3>
                    @foreach ($dscuahang as $ch)
                        <label class="dongloc">
                            <span>
                                <input type="checkbox" name="cuahang[]" value="{{ $ch->id_nhacungcap }}"
                                       @checked(in_array((int) $ch->id_nhacungcap, $cuahangchon, true))> {{ $ch->tencuahang }}
                            </span>
                            <small>{{ $ch->soluong }}</small>
                        </label>
                    @endforeach
                </div>
            @endif

            <div class="nhomloc">
                <h3>Độ khó nuôi</h3>
                @foreach ($dsdokho as $ma => $nhan)
                    <label class="dongloc">
                        <span>
                            <input type="checkbox" name="dokho[]" value="{{ $ma }}"
                                   @checked(in_array($ma, $dokhochon, true))> {{ $nhan }}
                        </span>
                    </label>
                @endforeach
                <small class="ghichuloc">Chỉ tính cá. Phụ kiện không hiện khi lọc theo độ khó nuôi.</small>
            </div>

            <div class="nutloc">
                <button type="submit" class="nutcam">Áp dụng</button>
                <a href="{{ $urlxoaloc }}" class="nutvien">Xóa</a>
            </div>
        </form>
    </aside>

    {{-- Danh sách sản phẩm --}}
    <div class="col-lg-9">
        <div class="row g-4">
            @forelse ($phantrang as $ca)
                <div class="col-xl-4 col-md-6">
                    @include('phanmenu.thesanpham', ['ca' => $ca])
                </div>
            @empty
                <div class="col-12">
                    <div class="khongketqua">
                        <i class="fas fa-fish"></i>
                        <p>Không có sản phẩm nào khớp.</p>
                        <small>Bỏ bớt bộ lọc hoặc thử từ khóa ngắn hơn như “koi”, “betta”.</small>
                        <a href="{{ $urlxoaloc }}" class="nutvien">Xóa bộ lọc</a>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($phantrang->lastPage() > 1)
            <nav class="phantrang" aria-label="Phân trang">
                @for ($i = 1; $i <= $phantrang->lastPage(); $i++)
                    @if ($i === $phantrang->currentPage())
                        <span class="dangchon" aria-current="page">{{ $i }}</span>
                    @else
                        <a href="{{ $phantrang->url($i) }}">{{ $i }}</a>
                    @endif
                @endfor
            </nav>
        @endif
    </div>
</div>
@endsection