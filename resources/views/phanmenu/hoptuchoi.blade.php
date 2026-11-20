{{--
    Hộp nhập lý do từ chối, dùng chung cho trang duyệt sản phẩm và duyệt nhà cung cấp.
    Dùng: @include('phanmenu.hoptuchoi', ['lydomau' => ['Lý do gợi ý 1', 'Lý do gợi ý 2']])
    Nút "Từ chối" của từng dòng cần có: type="button" data-url="địa chỉ từ chối" data-ten="tên hiển thị" onclick="moTuChoi(this)"
--}}
<div class="nenhop" id="hoptuchoi" hidden>
    <form method="POST" id="formtuchoi" class="hophoi hophoirong" role="dialog" aria-modal="true" aria-labelledby="tuchoitieude">
        @csrf
        <h3 id="tuchoitieude">Từ chối</h3>
        <p id="tuchoiten" class="tenmuctuchoi"></p>

        <label for="lydotuchoi">Lý do từ chối (người đăng sẽ thấy lý do này)</label>
        <textarea name="lydotuchoi" id="lydotuchoi" rows="3" maxlength="500" required class="otruonglydo"></textarea>

        @if (! empty($lydomau))
            <div class="lydomau">
                @foreach ($lydomau as $lydo)
                    <button type="button" class="chiplydo" data-lydo="{{ $lydo }}">{{ $lydo }}</button>
                @endforeach
            </div>
        @endif

        <div class="nuthop">
            <button type="button" class="nutvien" id="tuchoihuy">Hủy</button>
            <button type="submit" class="nutnguyhiem">Từ chối</button>
        </div>
    </form>
</div>

<script>
    (function () {
        var nen = document.getElementById('hoptuchoi');
        var form = document.getElementById('formtuchoi');
        var o = document.getElementById('lydotuchoi');
        var phimtruoc = null;

        // Gọi từ nút "Từ chối" của từng dòng: gán địa chỉ gửi, hiện tên rồi mở hộp
        window.moTuChoi = function (nut) {
            phimtruoc = nut;
            form.action = nut.dataset.url;
            document.getElementById('tuchoiten').textContent = nut.dataset.ten || '';
            o.value = '';
            nen.hidden = false;
            o.focus();
        };

        function dong() {
            nen.hidden = true;
            if (phimtruoc) { phimtruoc.focus(); }
        }

        document.getElementById('tuchoihuy').addEventListener('click', dong);
        nen.addEventListener('click', function (e) { if (e.target === nen) { dong(); } });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !nen.hidden) { dong(); } });

        // Bấm lý do gợi ý thì điền sẵn vào ô, vẫn sửa được
        document.querySelectorAll('.chiplydo').forEach(function (chip) {
            chip.addEventListener('click', function () {
                o.value = chip.dataset.lydo;
                o.focus();
            });
        });
    })();
</script>