<?php
/**
 * @var string $title
 * @var array $diaDiemList
 * @var array $danhMucList
 * @var array $filters
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
require_once dirname(__DIR__, 2) . '/layouts/navbar.php';
?>

<!-- ========================================================
     1. SUB-HEADER & QUICK CHIPS / VIEW MODE SWITCHER
     ======================================================== -->
<section class="locations-explorer-header">
    <div class="container-fluid px-lg-4 px-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb small mb-0">
                        <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none">Trang chủ</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">Bản đồ & Địa điểm</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold text-dark mb-0">Bản Đồ Ẩm Thực & Du Lịch Nha Trang</h1>
                <p class="text-muted small mb-0">Khám phá hơn 30 tọa độ đặc sản biển, danh thắng vịnh đảo và đặt vé E-Voucher tiện lợi</p>
            </div>

            <!-- Điều khiển Chế độ Xem (Split / Grid / Map) -->
            <div class="d-flex align-items-center gap-2">
                <div class="btn-group btn-group-sm view-mode-btn-group shadow-sm rounded-pill overflow-hidden bg-white p-1 border" role="group">
                    <button type="button" class="btn btn-primary rounded-pill active" id="btn-view-split" title="Xem danh sách & bản đồ song song">
                        <i class="bi bi-layout-split me-1"></i>Song song
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill border-0" id="btn-view-grid" title="Xem danh sách toàn màn hình">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Chỉ danh sách
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill border-0" id="btn-view-map" title="Xem bản đồ toàn màn hình">
                        <i class="bi bi-map me-1"></i>Chỉ bản đồ
                    </button>
                </div>

                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilterCard" aria-expanded="false">
                    <i class="bi bi-sliders me-1"></i>Bộ lọc nâng cao
                </button>
            </div>
        </div>

        <!-- Thanh Quick Filter Chips 1 Chạm -->
        <div class="quick-chips-wrapper">
            <button type="button" class="chip-btn <?= empty($filters['loai']) && empty($filters['co_ban_ve']) ? 'active' : '' ?>" data-filter-chip="all">
                <i class="bi bi-grid me-1"></i>Tất cả (30)
            </button>
            <button type="button" class="chip-btn <?= ($filters['loai'] ?? '') === 'du_lich' ? 'active' : '' ?>" data-filter-chip="du_lich">
                <i class="bi bi-compass me-1 text-info"></i>Du lịch & Danh thắng
            </button>
            <button type="button" class="chip-btn <?= ($filters['loai'] ?? '') === 'am_thuc' ? 'active' : '' ?>" data-filter-chip="am_thuc">
                <i class="bi bi-egg-fried me-1 text-danger"></i>Ẩm thực đặc sản
            </button>
            <button type="button" class="chip-btn <?= !empty($filters['co_ban_ve']) ? 'active' : '' ?>" data-filter-chip="co_ban_ve">
                <i class="bi bi-ticket-perforated me-1 text-success"></i>Có vé online E-Voucher
            </button>
            <button type="button" class="chip-btn <?= !empty($filters['dang_mo_cua']) ? 'active' : '' ?>" data-filter-chip="dang_mo_cua">
                <i class="bi bi-clock-fill me-1 text-warning"></i>Đang mở cửa
            </button>
            <button type="button" class="chip-btn" id="btn-locate-me">
                <i class="bi bi-crosshair me-1 text-primary"></i>Gần vị trí của tôi
            </button>
        </div>

        <!-- Thanh Chọn Bán Kính Định Vị GPS Quanh Người Dùng (Radius Filter) -->
        <div class="radius-filter-wrapper d-none align-items-center gap-2 mt-2 pt-2 border-top flex-wrap bg-light p-2 rounded-3" id="radiusFilterBar">
            <span class="small fw-bold text-primary me-1"><i class="bi bi-broadcast-pin me-1"></i>Bán kính quanh bạn:</span>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0.5 small radius-chip" data-radius="1000">1 km</button>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 small radius-chip active" data-radius="3000">3 km</button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0.5 small radius-chip" data-radius="5000">5 km</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-0.5 small radius-chip" data-radius="all">Tất cả</button>
            <span class="small text-muted ms-2" id="radiusResultText" style="font-size: 11.5px;"></span>
        </div>

        <!-- Khung Bộ Lọc Nâng Cao (Collapse) -->
        <div class="collapse <?= (!empty($filters['tu_khoa']) || !empty($filters['danh_muc_id']) || !empty($filters['muc_gia'])) ? 'show' : '' ?> mt-3" id="advancedFilterCard">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <form id="live-filter-form" action="<?= url('locations') ?>" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Từ khóa tìm kiếm</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="tu_khoa" class="form-control border-start-0" placeholder="Nem nướng, I-Resort, Tháp Bà..." value="<?= htmlspecialchars($filters['tu_khoa'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Phân loại</label>
                            <select name="loai" class="form-select form-select-sm">
                                <option value="">Tất cả loại hình</option>
                                <option value="am_thuc" <?= ($filters['loai'] ?? '') === 'am_thuc' ? 'selected' : '' ?>>Ẩm thực & Quán ăn</option>
                                <option value="du_lich" <?= ($filters['loai'] ?? '') === 'du_lich' ? 'selected' : '' ?>>Du lịch & Danh thắng</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Danh mục trải nghiệm</label>
                            <select name="danh_muc_id" class="form-select form-select-sm">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($danhMucList as $dm): ?>
                                    <option value="<?= $dm['id'] ?>" data-loai="<?= htmlspecialchars($dm['loai_danh_muc']) ?>" <?= ($filters['danh_muc_id'] ?? '') == $dm['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Khoảng giá</label>
                            <select name="muc_gia" class="form-select form-select-sm">
                                <option value="">Tất cả mức giá</option>
                                <option value="duoi_50k" <?= ($filters['muc_gia'] ?? '') === 'duoi_50k' ? 'selected' : '' ?>>Dưới 50.000 đ</option>
                                <option value="50k_200k" <?= ($filters['muc_gia'] ?? '') === '50k_200k' ? 'selected' : '' ?>>50.000 đ - 200.000 đ</option>
                                <option value="tren_200k" <?= ($filters['muc_gia'] ?? '') === 'tren_200k' ? 'selected' : '' ?>>Trên 200.000 đ</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="co_ban_ve" value="1" id="checkCoBanVe" <?= (!empty($filters['co_ban_ve'])) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold text-dark" for="checkCoBanVe">
                                    Có vé online
                                </label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="dang_mo_cua" value="1" id="checkDangMoCua" <?= (!empty($filters['dang_mo_cua'])) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold text-success" for="checkDangMoCua">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Đang mở cửa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-2 mt-3 pt-2 border-top">
                        <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                        </button>
                        <button type="submit" id="btn-submit-filter" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc kết quả
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     2. BỐ CỤC CHIA ĐÔI HIỆN ĐẠI (SPLIT VIEW)
     ======================================================== -->
<main class="container-fluid px-lg-4 px-3 my-3 flex-grow-1">
    <div class="row g-4" id="main-explorer-row">
        <!-- Cột Danh Sách Địa Điểm (Bên Trái) -->
        <div class="col-lg-7 col-xl-7" id="col-locations-list">
            <!-- Header tóm tắt danh sách -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold fs-6" id="locations-count">
                        <?= count($diaDiemList) ?>
                    </span>
                    <span class="fw-bold text-dark">địa điểm phù hợp</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small d-none d-sm-inline">Sắp xếp:</span>
                    <select id="sort-selector" class="form-select form-select-sm rounded-pill" style="width: auto;">
                        <option value="popular" <?= ($filters['sap_xep'] ?? '') === 'popular' ? 'selected' : '' ?>>Lượt xem & Phổ biến</option>
                        <option value="rating_desc" <?= in_array($filters['sap_xep'] ?? '', ['rating_desc', 'rating']) ? 'selected' : '' ?>>Đánh giá: Cao đến thấp</option>
                        <option value="rating_asc" <?= ($filters['sap_xep'] ?? '') === 'rating_asc' ? 'selected' : '' ?>>Đánh giá: Thấp đến cao</option>
                        <option value="price_asc" <?= ($filters['sap_xep'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến cao</option>
                        <option value="price_desc" <?= ($filters['sap_xep'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến thấp</option>
                    </select>
                </div>
            </div>

            <!-- Lưới Card Danh Sách Địa Điểm -->
            <div class="row g-3" id="locations-container">
                <?php foreach ($diaDiemList as $dd): ?>
                    <?php 
                        $isFood = $dd['loai_danh_muc'] === 'am_thuc';
                        $loaiLabel = $isFood ? 'Ẩm thực' : 'Du lịch';
                        $catBadgeClass = $isFood ? 'badge-tag-food text-coral' : 'badge-tag-travel text-teal';
                    ?>
                    <div class="col-md-6 col-sm-12" id="card-location-<?= $dd['id'] ?>">
                        <div class="location-card-modern" onclick="focusOnMap(<?= $dd['id'] ?>, <?= $dd['vi_do'] ?>, <?= $dd['kinh_do'] ?>)" style="cursor: pointer;">
                            <div class="location-card-img-wrap">
                                <img src="<?= htmlspecialchars(hinh_anh($dd['anh_dai_dien'])) ?>" class="location-card-img" alt="<?= htmlspecialchars($dd['ten_dia_diem']) ?>" loading="lazy">
                                <span class="card-category-glass <?= $catBadgeClass ?>">
                                    <?= $loaiLabel ?> &bull; <?= htmlspecialchars($dd['ten_danh_muc']) ?>
                                </span>
                                <?php if ($dd['co_ban_ve']): ?>
                                    <span class="card-ticket-glass">
                                        <i class="bi bi-ticket-perforated me-1"></i>Đặt vé E-Voucher
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="location-card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="small">
                                        <?= \App\Helpers\FormatHelper::saoHtml((float)$dd['diem_danh_gia']) ?>
                                        <span class="text-muted ms-1">(<?= (int)$dd['tong_danh_gia'] ?>)</span>
                                    </div>
                                    <span class="text-muted small"><i class="bi bi-eye me-1"></i><?= (int)$dd['luot_xem'] ?></span>
                                </div>

                                <h3 class="location-card-title"><?= htmlspecialchars($dd['ten_dia_diem']) ?></h3>

                                <div class="location-card-address">
                                    <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($dd['dia_chi']) ?>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                    <span class="location-card-price">
                                        <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_cao_nhat']) ?>
                                    </span>
                                    <div class="d-flex gap-1" onclick="event.stopPropagation();">
                                        <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-2 py-1 btn-trip-action-<?= $dd['id'] ?>" onclick="window.toggleTripLocation(<?= $dd['id'] ?>, '<?= addslashes(htmlspecialchars($dd['ten_dia_diem'])) ?>', <?= (float)$dd['vi_do'] ?>, <?= (float)$dd['kinh_do'] ?>, <?= (float)$dd['gia_thap_nhat'] ?>)" title="Thêm vào lộ trình du lịch đa điểm">
                                            <i class="bi bi-pin-map"></i> Lộ trình
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" onclick="focusOnMap(<?= $dd['id'] ?>, <?= $dd['vi_do'] ?>, <?= $dd['kinh_do'] ?>)" title="Xem vị trí trên bản đồ">
                                            <i class="bi bi-geo-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" onclick="window.startMapRouting(<?= $dd['vi_do'] ?>, <?= $dd['kinh_do'] ?>, '<?= addslashes(htmlspecialchars($dd['ten_dia_diem'])) ?>')" title="Chỉ đường OSRM nội bộ">
                                            <i class="bi bi-sign-turn-right-fill"></i>
                                        </button>
                                        <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold">
                                            Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cột Bản Đồ Cố Định (Bên Phải - Sticky Map) -->
        <div class="col-lg-5 col-xl-5" id="col-locations-map">
            <div class="sticky-map-wrapper">
                <!-- Thanh công cụ nổi trên bản đồ -->
                <div class="map-floating-bar d-flex align-items-center gap-2">
                    <span class="map-pill-badge" id="map-counter-badge">
                        <i class="bi bi-geo-fill text-warning me-1"></i><span id="map-pin-count"><?= count($diaDiemList) ?></span> ghim tọa độ
                    </span>
                    <button type="button" class="btn btn-light btn-sm rounded-pill shadow-sm fw-semibold" id="btn-fit-bounds" title="Thu phóng thấy tất cả điểm">
                        <i class="bi bi-arrows-fullscreen me-1"></i>Toàn cảnh
                    </button>
                </div>

                <!-- Canvas Bản đồ Leaflet -->
                <div id="map"></div>
            </div>
        </div>
    </div>
</main>

<!-- ========================================================
     THANH CÔNG CỤ NỔI LỘ TRÌNH ĐA ĐIỂM (FLOATING TRIP BAR)
     ======================================================== -->
<div id="floating-trip-bar" class="d-none" style="position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); z-index: 1050; max-width: 94%; width: 560px;">
    <div class="card border-0 shadow-lg rounded-pill p-2 px-3 text-white d-flex flex-row align-items-center justify-content-between gap-2" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(255,255,255,0.2) !important; backdrop-filter: blur(10px);">
        <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1.5 fw-bold" id="trip-count-badge">
                <i class="bi bi-compass-fill me-1"></i><span id="trip-selected-count">0</span> điểm
            </span>
            <div class="small text-truncate text-white-50" id="trip-selected-names" style="font-size: 13px;">
                Chưa chọn địa điểm nào
            </div>
        </div>
        <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
            <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1" onclick="window.openTripModal()">
                <i class="bi bi-sign-turn-right-fill"></i><span>Xem Lộ Trình</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-light rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" onclick="window.clearAllTripLocations()" title="Xóa hết lộ trình">
                <i class="bi bi-x-lg" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL LỘ TRÌNH ĐA ĐIỂM & TỐI ƯU CHI PHÍ
     ======================================================== -->
<div class="modal fade" id="tripPlannerModal" tabindex="-1" aria-labelledby="tripPlannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.25rem;">
                        <i class="bi bi-signpost-2-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="tripPlannerModalLabel">Kế Hoạch Lộ Trình Đa Điểm</h5>
                        <small class="text-muted">Tự động tối ưu tuyến đường di chuyển & dự toán chi phí du lịch</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <!-- Thanh công cụ tối ưu & điều khiển -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2.5 bg-light rounded-3 mb-3 border">
                    <div class="small fw-semibold text-secondary d-flex align-items-center gap-1.5" id="trip-start-point-box">
                        <i class="bi bi-geo-alt-fill text-danger fs-6"></i>
                        <span id="trip-start-point-label">Đang lấy tọa độ GPS chính xác của bạn...</span>
                        <button type="button" class="btn btn-sm btn-link p-0 text-primary small text-decoration-none ms-1" onclick="window.refreshGPSAndTrip(this)" title="Cập nhật lại GPS tức thì">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold d-flex align-items-center gap-1 shadow-2xs" onclick="window.optimizeTripOrder()">
                            <i class="bi bi-lightning-charge-fill text-warning"></i>
                            <span>Tối ưu thứ tự gần nhất (TSP)</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" onclick="window.clearAllTripLocations()">
                            <i class="bi bi-trash3 me-1"></i>Xóa hết
                        </button>
                    </div>
                </div>

                <!-- Danh sách các chặng dừng -->
                <div class="mb-3">
                    <h6 class="fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px;">Các chặng ghé thăm (<span id="trip-modal-stop-count">0</span> địa điểm):</h6>
                    <div class="d-flex flex-column gap-2" id="trip-stops-list">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <!-- Bảng Dự Toán Quãng Đường & Chi Phí Thông Minh ($0 API) -->
                <div class="card border-0 rounded-3 p-3 mb-2 trip-budget-card">
                    <h6 class="fw-bold text-dark small text-uppercase mb-2 d-flex align-items-center gap-1.5" style="letter-spacing: 0.5px;">
                        <i class="bi bi-calculator text-primary"></i>Dự Toán Hành Trình & Chi Phí Di Chuyển
                    </h6>
                    <div class="row g-2 mb-2 text-center">
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-white rounded-2 border">
                                <small class="text-muted d-block" style="font-size: 11px;">Tổng quãng đường</small>
                                <strong class="fs-6 text-primary" id="trip-total-distance">-- km</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-white rounded-2 border">
                                <small class="text-muted d-block" style="font-size: 11px;">Thời gian lái xe</small>
                                <strong class="fs-6 text-dark" id="trip-total-time">-- phút</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-white rounded-2 border">
                                <small class="text-muted d-block" style="font-size: 11px;">Xăng xe máy (~)</small>
                                <strong class="fs-6 text-success" id="trip-cost-bike">-- đ</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-white rounded-2 border">
                                <small class="text-muted d-block" style="font-size: 11px;">GrabBike ước tính</small>
                                <strong class="fs-6 text-info" id="trip-cost-grab">-- đ</strong>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-2 border small">
                        <span class="text-secondary">
                            <i class="bi bi-ticket-perforated text-warning me-1"></i>Tổng ngân sách vé tham quan dự kiến:
                        </span>
                        <strong class="text-danger fw-bold fs-6" id="trip-total-tickets">0 đ</strong>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm d-flex align-items-center gap-1.5" onclick="window.drawMultiRouteOnMap()">
                    <i class="bi bi-map-fill"></i>
                    <span>Vẽ Lộ Trình Lên Bản Đồ</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Nút Nổi Xem Bản Đồ Trên Màn Hình Di Động (Floating Action Button) -->
<button type="button" class="btn btn-primary rounded-pill px-4 py-2 mobile-map-toggle-fab" id="btn-mobile-map-toggle">
    <i class="bi bi-map-fill me-1"></i>Xem bản đồ
</button>

<script>
    window.APP_URL = "<?= rtrim(url(''), '/') ?>";
    window.INITIAL_LOCATIONS = <?= json_encode($diaDiemList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>
