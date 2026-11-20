{{--
    Hộp hỏi xác nhận dùng chung, thay cho hộp confirm() mặc định của trình duyệt.
    Cách dùng: thêm các thuộc tính này vào thẻ <form>, bấm gửi form thì hộp tự hiện ra:
        data-xacnhan-tieude="Duyệt sản phẩm này?"
        data-xacnhan-noidung="Nội dung giải thích"        (không bắt buộc)
        data-xacnhan-nut="Duyệt"                          (chữ trên nút đồng ý, không bắt buộc)
        data-xacnhan-kieu="nguyhiem"                      (nút đỏ, dùng cho thao tác xóa, không bắt buộc)
--}}
<div class="nenhop" id="hopxacnhan" hidden>
    <div class="hophoi" role="dialog" aria-modal="true" aria-labelledby="xacnhantieude" aria-describedby="xacnhannoidung">
        <h3 id="xacnhantieude"></h3>
        <p id="xacnhannoidung"></p>
        <div class="nuthop">
            <button type="button" class="nutvien" id="xacnhanhuy">Hủy</button>
            <button type="button" class="nutcam" id="xacnhandongy">Đồng ý</button>
        </div>
    </div>
</div>

<script>
    (function () {
        var nen = document.getElementById('hopxacnhan');
        var nutdongy = document.getElementById('xacnhandongy');
        var formcho = null;     // form đang chờ người dùng xác nhận
        var phimtruoc = null;   // phần tử đang được chọn trước khi mở hộp, để trả lại khi đóng

        function dong() {
            nen.hidden = true;
            formcho = null;
            if (phimtruoc) { phimtruoc.focus(); }
        }

        // Bắt mọi form có thuộc tính data-xacnhan-tieude, chặn gửi và hỏi trước
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.dataset || !form.dataset.xacnhanTieude) { return; }

            e.preventDefault();
            formcho = form;
            phimtruoc = document.activeElement;

            document.getElementById('xacnhantieude').textContent = form.dataset.xacnhanTieude;
            document.getElementById('xacnhannoidung').textContent = form.dataset.xacnhanNoidung || '';
            nutdongy.textContent = form.dataset.xacnhanNut || 'Đồng ý';
            nutdongy.className = form.dataset.xacnhanKieu === 'nguyhiem' ? 'nutnguyhiem' : 'nutcam';

            nen.hidden = false;
            nutdongy.focus();
        });

        nutdongy.addEventListener('click', function () {
            var form = formcho;
            nen.hidden = true;
            formcho = null;
            if (form) { form.submit(); }   // submit() trực tiếp thì không bị chặn lại lần nữa
        });

        document.getElementById('xacnhanhuy').addEventListener('click', dong);
        nen.addEventListener('click', function (e) { if (e.target === nen) { dong(); } });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !nen.hidden) { dong(); } });
    })();
</script>