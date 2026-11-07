@php $don = Auth::user()->nhacungcap; @endphp

<div class="nencuaso" id="cuaso-nhacungcap" onclick="if (event.target === this) dongCuaSo()">
    <div class="hopxacthuc hopxacthucrong">
        <button type="button" class="nutdong" onclick="dongCuaSo()">&times;</button>

        @if ($don && $don->trangthaiduyet === 'choduyet')

            {{-- Đơn đang chờ: chỉ hiện thông tin, không cho gửi thêm --}}
            <div class="bieutuongxacthuc"><i class="fas fa-hourglass-half"></i></div>
            <h2>Đơn đang chờ duyệt</h2>
            <p class="ghichuxacthuc" style="text-align: center;">
                Quản trị viên sẽ xem xét đơn của bạn. Khi được duyệt, mục "Kênh người bán" sẽ xuất hiện ở thanh trên cùng.
            </p>

            <table class="bangthongtin">
                <tr><th>Tên cửa hàng</th><td>{{ $don->tencuahang }}</td></tr>
                <tr><th>Địa chỉ</th><td>{{ $don->diachi }}</td></tr>
                <tr><th>Số điện thoại</th><td>{{ $don->sodienthoai }}</td></tr>
                <tr><th>Mã số thuế</th><td>{{ $don->masothue ?: 'Chưa cung cấp' }}</td></tr>
                <tr><th>Ngày gửi</th><td>{{ $don->ngaytao?->format('d/m/Y H:i') }}</td></tr>
            </table>

        @else

            <div class="bieutuongxacthuc"><i class="fas fa-store"></i></div>
            <h2>Đăng ký bán hàng</h2>

            @if ($don && $don->trangthaiduyet === 'tuchoi')
                <div class="canhbao" style="text-align: left;">
                    <strong>Đơn trước của bạn bị từ chối.</strong><br>
                    Lý do: {{ $don->lydotuchoi }}<br>
                    Bạn có thể chỉnh sửa rồi gửi lại.
                </div>
            @endif

            <form method="POST" action="{{ route('nhacungcap.guidon') }}">
                @csrf
                <div class="onhap">
                    <input type="text" name="tencuahang" placeholder="Tên cửa hàng"
                           value="{{ old('tencuahang', $don->tencuahang ?? '') }}" required>
                </div>
                <div class="onhap">
                    <input type="text" name="diachi" placeholder="Địa chỉ cửa hàng"
                           value="{{ old('diachi', $don->diachi ?? '') }}" required>
                </div>
                <div class="onhap">
                    <input type="text" name="sodienthoai" placeholder="Số điện thoại cửa hàng"
                           value="{{ old('sodienthoai', $don->sodienthoai ?? '') }}" required>
                </div>
                <div class="onhap">
                    <input type="text" name="masothue" placeholder="Mã số thuế / mã đăng ký kinh doanh (nếu có)"
                           value="{{ old('masothue', $don->masothue ?? '') }}">
                </div>
                <div class="onhap">
                    <textarea name="mota" rows="3" placeholder="Giới thiệu cửa hàng">{{ old('mota', $don->mota ?? '') }}</textarea>
                </div>

                <button type="submit" class="nutchinh">Gửi đơn đăng ký</button>
            </form>

        @endif
    </div>
</div>