<?php
/**
 * @var string $title
 * @var array $goiVe
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Stepper Tiến Trình Đặt Vé (Bước 1/3) -->
            <?php 
            $currentStep = 1;
            include __DIR__ . '/_stepper.php'; 
            ?>

            <div class="card border-0 shadow-sm p-4 rounded-4">
                <div class="border-bottom pb-3 mb-4">
                    <span class="badge bg-success-subtle text-success fw-bold text-uppercase px-3 py-1 rounded-pill">Đặt vé trực tuyến</span>
                    <h2 class="fw-bold mt-2 mb-1"><?= htmlspecialchars($goiVe['ten_goi_ve']) ?></h2>
                    <p class="text-muted small mb-0">Địa điểm: <strong><?= htmlspecialchars($goiVe['ten_dia_diem']) ?></strong></p>
                </div>

                <form action="<?= url('booking/submit') ?>" method="POST" id="bookingForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="goi_ve_id" value="<?= (int)$goiVe['id'] ?>">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Ngày tham quan <span class="text-danger">*</span></label>
                            <input type="date" name="ngay_tham_quan" class="form-control" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                            <div class="form-text small">Vui lòng chọn ngày bạn dự định sử dụng dịch vụ.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <select name="phuong_thuc_thanh_toan" class="form-select" required>
                                <option value="chuyen_khoan">Chuyển khoản Ngân hàng (QR Code)</option>
                                <option value="vnpay">Cổng thanh toán VNPAY Sandbox</option>
                                <option value="momo">Ví điện tử MoMo</option>
                                <option value="tai_quay">Thanh toán tại quầy khi nhận vé</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bảng chọn số lượng vé -->
                    <div class="card bg-light border-0 p-3 mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-people me-2 text-primary"></i>Chọn Số Lượng Vé</h6>
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-6">
                                <strong>Vé Người Lớn</strong>
                                <div class="text-primary small fw-semibold">
                                    <?= \App\Helpers\FormatHelper::tienTe((float)$goiVe['gia_nguoi_lon']) ?> / vé
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustQty('sl_nl', -1)">-</button>
                                    <input type="number" id="sl_nl" name="so_luong_nguoi_lon" class="form-control form-control-sm text-center" value="1" min="0" max="50">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustQty('sl_nl', 1)">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-6">
                                <strong>Vé Trẻ Em</strong>
                                <div class="text-secondary small fw-semibold">
                                    <?= \App\Helpers\FormatHelper::tienTe((float)$goiVe['gia_tre_em']) ?> / vé
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustQty('sl_te', -1)">-</button>
                                    <input type="number" id="sl_te" name="so_luong_tre_em" class="form-control form-control-sm text-center" value="0" min="0" max="50">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustQty('sl_te', 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin liên hệ nhận E-Voucher -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-envelope-paper me-2 text-primary"></i>Thông Tin Nhận Phiếu E-Voucher</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Họ và tên người nhận</label>
                                <input type="text" name="ten_khach_hang" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Email nhận vé</label>
                                <input type="email" name="email_khach_hang" class="form-control" value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                                <input type="tel" 
                                       id="sdt_khach_hang"
                                       name="sdt_khach_hang" 
                                       class="form-control" 
                                       placeholder="0912345678" 
                                       inputmode="numeric"
                                       maxlength="11"
                                       pattern="^(0|\+84)[0-9]{9,10}$"
                                       title="Vui lòng nhập số điện thoại hợp lệ gồm 10-11 chữ số (ví dụ: 0912345678)"
                                       oninput="this.value = this.value.replace(/[^0-9+]/g, ''); validatePhoneInput(this);"
                                       required>
                                <div class="invalid-feedback small" id="sdtErrorFeedback">
                                    Số điện thoại phải gồm 10-11 chữ số và bắt đầu bằng 0 hoặc +84.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tổng tiền ước tính -->
                    <div class="d-flex justify-content-between align-items-center p-3 bg-primary-subtle rounded-3 mb-4">
                        <span class="fw-bold text-dark fs-5">Tạm tính tổng tiền:</span>
                        <span class="fw-bold text-primary fs-4" id="tamTinhEl">0 đ</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill fs-5">
                        <i class="bi bi-shield-check me-2"></i>Xác Nhận Đặt Vé & Nhận E-Voucher
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    const giaNguoiLon = <?= (float)$goiVe['gia_nguoi_lon'] ?>;
    const giaTreEm = <?= (float)$goiVe['gia_tre_em'] ?>;

    function adjustQty(id, delta) {
        const input = document.getElementById(id);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        input.value = val;
        capNhatTongTien();
    }

    function capNhatTongTien() {
        const slNl = parseInt(document.getElementById('sl_nl').value) || 0;
        const slTe = parseInt(document.getElementById('sl_te').value) || 0;
        const tong = (slNl * giaNguoiLon) + (slTe * giaTreEm);
        document.getElementById('tamTinhEl').innerText = tong.toLocaleString('vi-VN') + ' đ';
    }

    document.getElementById('sl_nl').addEventListener('input', capNhatTongTien);
    document.getElementById('sl_te').addEventListener('input', capNhatTongTien);
    capNhatTongTien();

    function validatePhoneInput(input) {
        const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
        const val = input.value.trim();
        if (val.length > 0 && !phoneRegex.test(val)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        } else if (phoneRegex.test(val)) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        }
        input.classList.remove('is-invalid', 'is-valid');
        return false;
    }

    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        const phoneInput = document.getElementById('sdt_khach_hang');
        const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
        if (!phoneRegex.test(phoneInput.value.trim())) {
            e.preventDefault();
            phoneInput.classList.add('is-invalid');
            phoneInput.focus();
        }
    });
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

