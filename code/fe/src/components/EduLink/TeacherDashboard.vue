<template>
  <div class="teacher-dashboard-page">
    <div class="profile-container">
      <!-- 1. BREADCRUMB & ROLE SWITCHER -->
      <div class="breadcrumb-and-switcher">
        <nav class="breadcrumb-nav">
          <i class="fa-solid fa-house me-1"></i>
          <router-link to="/my-schedule" class="crumb-link">Trang chủ</router-link>
          <span class="crumb-sep">›</span>
          <span class="crumb-link">Quản lý giảng viên</span>
          <span class="crumb-sep">›</span>
          <span class="crumb-active">Không gian giảng viên: TS. Nguyễn Minh Triết (Khoa Toán - Tin học)</span>
        </nav>

        <div class="role-switcher-box">
          <span class="role-switcher-label"><i class="fa-solid fa-eye me-1"></i> Chế độ xem:</span>
          <div class="role-switcher-pills">
            <router-link to="/hoc-vien" class="role-pill">
              <i class="fa-solid fa-user-graduate me-1"></i> Học viên
            </router-link>
            <router-link to="/ho-so-giang-vien" class="role-pill active">
              <i class="fa-solid fa-chalkboard-user me-1"></i> Giáo viên
            </router-link>
          </div>
        </div>
      </div>

      <!-- 2. TOP PROFILE BANNER (THẺ GIẢNG VIÊN) -->
      <div class="profile-header-card">
        <div class="profile-top-row">
          <!-- Left: Avatar & Tên, Đơn vị công tác -->
          <div class="profile-info-left">
            <div class="teacher-avatar-box">
              <img
                src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=240&q=80"
                alt="TS. Nguyễn Minh Triết"
                class="teacher-avatar-img"
              />
              <div class="faceid-verified-icon" title="Đã xác thực Face ID sinh trắc học giảng dạy">
                <i class="fa-solid fa-shield-halved"></i>
              </div>
            </div>

            <div class="teacher-meta-details">
              <div class="teacher-name-row">
                <h1 class="teacher-name">TS. Nguyễn Minh Triết</h1>
                <span class="badge-faceid-verified">
                  <span class="green-dot me-1"></span> Đã xác thực Face ID giảng dạy
                </span>
              </div>

              <div class="teacher-sub-info">
                <span class="badge-teacher-id">Mã GV: TCH-202419</span>
                <span class="meta-dot">•</span>
                <span>Khoa Toán - Tin học</span>
                <span class="meta-dot">•</span>
                <span>
                  <i class="fa-solid fa-building-columns text-primary me-1"></i>
                  Đại học Khoa học Tự Nhiên TP.HCM
                </span>
              </div>
            </div>
          </div>

          <!-- Right: Nút hành động nhanh -->
          <div class="profile-top-actions">
            <button class="btn btn-action-outline" @click="handleEditProfile">
              <i class="fa-regular fa-pen-to-square me-1"></i> Chỉnh sửa hồ sơ
            </button>
            <router-link to="/face-id" class="btn btn-action-outline">
              <i class="fa-solid fa-rotate me-1"></i> Cập nhật Face ID
            </router-link>
            <button class="btn btn-primary btn-download-report" @click="handleDownloadReport">
              <i class="fa-solid fa-download me-1"></i> Xuất báo cáo giảng dạy
            </button>
          </div>
        </div>

        <!-- Dải 4 chỉ số nổi bật (Highlights row) -->
        <div class="profile-stats-bar">
          <!-- Chỉ số 1: Giờ dạy tích lũy -->
          <div class="stat-highlight-item">
            <div class="stat-icon-box icon-blue">
              <i class="fa-regular fa-clock"></i>
            </div>
            <div>
              <div class="stat-top-line">
                <span class="stat-num">320</span> <span class="stat-unit">giờ</span>
              </div>
              <div class="stat-label">Giờ dạy tích lũy</div>
              <div class="stat-sub text-success">
                <i class="fa-solid fa-arrow-trend-up me-1"></i> +12h tuần này
              </div>
            </div>
          </div>

          <!-- Chỉ số 2: Lớp đang phụ trách -->
          <div class="stat-highlight-item">
            <div class="stat-icon-box icon-indigo">
              <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
              <div class="stat-top-line">
                <span class="stat-num">05</span> <span class="stat-unit">lớp học</span>
              </div>
              <div class="stat-label">Lớp đang phụ trách</div>
              <div class="stat-sub text-muted">4 lớp Online Face ID, 1 Offline</div>
            </div>
          </div>

          <!-- Chỉ số 3: Điểm danh chuẩn xác -->
          <div class="stat-highlight-item">
            <div class="stat-icon-box icon-green">
              <i class="fa-regular fa-circle-check"></i>
            </div>
            <div>
              <div class="stat-top-line">
                <span class="stat-num text-success">99.2%</span>
              </div>
              <div class="stat-label">Điểm danh chuẩn xác</div>
              <div class="stat-sub text-success">
                <i class="fa-solid fa-shield-halved me-1"></i> Face ID tự động 100%
              </div>
            </div>
          </div>

          <!-- Chỉ số 4: Đánh giá trung bình -->
          <div class="stat-highlight-item">
            <div class="stat-icon-box icon-orange">
              <i class="fa-regular fa-star"></i>
            </div>
            <div>
              <div class="stat-top-line">
                <span class="stat-num">4.9</span> <span class="stat-unit">/ 5.0</span>
              </div>
              <div class="stat-label">Đánh giá trung bình</div>
              <div class="stat-sub text-muted">Từ 128 học viên đánh giá</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. BỐ CỤC 2 CỘT NỘI DUNG -->
      <div class="profile-layout">
        <!-- CỘT TRÁI: TIẾN ĐỘ, LỚP HỌC & LỊCH DẠY -->
        <div class="profile-left-column">
          <!-- THẺ 1: MỤC TIÊU & CHỈ TIÊU GIẢNG DẠY HỌC KỲ -->
          <div class="content-card">
            <div class="card-header-flex">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-primary">
                  <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                  <h2 class="card-title mb-0">Chỉ tiêu & Tiến độ giảng dạy</h2>
                  <div class="card-sub-label">Kế hoạch đào tạo Kỳ II (Năm học 2024 - 2025)</div>
                </div>
              </div>
              <span class="badge-exam-focus">Học kỳ trọng tâm: Tháng 06/2025</span>
            </div>

            <!-- Khung mục tiêu chính đặt ra -->
            <div class="goal-highlight-box">
              <div class="goal-left">
                <i class="fa-regular fa-flag text-primary goal-flag-icon"></i>
                <div>
                  <div class="goal-label">Chỉ tiêu chuyên môn bộ môn:</div>
                  <div class="goal-desc">
                    Hoàn thành 100% đề cương ôn tập ĐGNL ĐHQG & Huấn luyện học sinh giỏi môn Toán, Tin...
                  </div>
                </div>
              </div>
              <div class="goal-percent-box">
                <div class="goal-number">82%</div>
                <div class="goal-sub">Đạt chỉ tiêu kỳ</div>
              </div>
            </div>

            <!-- Thanh tiến độ tổng quát -->
            <div class="overall-progress-wrap mt-3">
              <div class="progress-labels-row">
                <span class="progress-text-label">Tiến độ giáo trình & bài giảng học kỳ hiện tại</span>
                <span class="progress-val-label">82% (41/50 bài giảng)</span>
              </div>
              <div class="progress custom-progress-bar">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 82%"></div>
              </div>
            </div>

            <!-- 3 Khối tiến độ chuyên đề -->
            <div class="subjects-progress-grid mt-4">
              <!-- Môn 1: Toán nâng cao 301 -->
              <div class="subject-progress-card">
                <div class="subj-header">
                  <span class="subj-name">Toán nâng cao 301</span>
                  <span class="subj-percent text-primary fw-bold">90%</span>
                </div>
                <div class="progress sub-progress">
                  <div class="progress-bar bg-primary" style="width: 90%"></div>
                </div>
                <div class="subj-note">18/20 chuyên đề hoàn tất</div>
              </div>

              <!-- Môn 2: Lập trình Toán ứng dụng -->
              <div class="subject-progress-card">
                <div class="subj-header">
                  <span class="subj-name">Lập trình Toán ứng dụng</span>
                  <span class="subj-percent text-success fw-bold">75%</span>
                </div>
                <div class="progress sub-progress">
                  <div class="progress-bar bg-success" style="width: 75%"></div>
                </div>
                <div class="subj-note">Đang hướng dẫn đồ án thuật toán</div>
              </div>

              <!-- Môn 3: Luyện đề ĐGNL Chuyên sâu -->
              <div class="subject-progress-card">
                <div class="subj-header">
                  <span class="subj-name">Luyện đề ĐGNL Chuyên sâu</span>
                  <span class="subj-percent text-warning-dark fw-bold">80%</span>
                </div>
                <div class="progress sub-progress">
                  <div class="progress-bar bg-warning" style="width: 80%"></div>
                </div>
                <div class="subj-note">16/20 bộ đề chuẩn hóa</div>
              </div>
            </div>
          </div>

          <!-- THẺ 2: LỚP HỌC ĐANG PHỤ TRÁCH (03 LỚP) -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-success">
                  <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <h2 class="card-title mb-0">Lớp học đang phụ trách giảng dạy (03 lớp)</h2>
              </div>
              <router-link to="/my-classes" class="view-history-link">
                Xem tất cả lớp phụ trách <i class="fa-solid fa-chevron-right ms-1 fs-8"></i>
              </router-link>
            </div>

            <div class="enrolled-classes-list">
              <!-- Lớp 1: Toán học nâng cao 301 -->
              <div class="class-enroll-item highlight-border-blue">
                <div class="class-item-top">
                  <div class="class-pills">
                    <span class="pill-badge pill-blue">Kèm 1:1 Đặc biệt</span>
                    <span class="pill-badge pill-green-outline">
                      <i class="fa-solid fa-video me-1"></i> Online Face ID
                    </span>
                  </div>
                  <div class="class-upcoming-badge">
                    <i class="fa-regular fa-clock me-1"></i> Sắp diễn ra: 19:30 Tối nay
                  </div>
                </div>

                <div class="class-item-main">
                  <div>
                    <h3 class="class-item-title">Toán học nâng cao 301 - Giải tích & ĐGNL</h3>
                    <div class="class-item-meta">
                      <span><i class="fa-solid fa-user-graduate me-1 text-primary"></i> Học viên: <strong>Nguyễn Linh Lan (Lớp 12A1)</strong></span>
                      <span class="meta-dot">•</span>
                      <span><i class="fa-regular fa-calendar me-1 text-secondary"></i> Thứ 2 & Thứ 6 (19:30 - 21:00)</span>
                    </div>
                  </div>

                  <div class="class-item-buttons">
                    <button class="btn btn-outline-secondary btn-class-details" @click="openClassDetails('Toán 301 (Linh Lan)')">
                      Chi tiết buổi dạy
                    </button>
                    <router-link to="/face-id" class="btn btn-primary btn-enter-faceid">
                      <i class="fa-solid fa-chalkboard me-1"></i> Vào phòng giảng dạy (Face ID)
                    </router-link>
                  </div>
                </div>
              </div>

              <!-- Lớp 2: Lập trình Web -->
              <div class="class-enroll-item">
                <div class="class-item-top">
                  <div class="class-pills">
                    <span class="pill-badge pill-purple">Nhóm nhỏ (3 - 5 bạn)</span>
                    <span class="pill-badge pill-green-outline">
                      <i class="fa-solid fa-video me-1"></i> Online Face ID
                    </span>
                  </div>
                </div>

                <div class="class-item-main">
                  <div>
                    <h3 class="class-item-title">Lập trình Web Cơ bản & Nâng cao (Frontend React)</h3>
                    <div class="class-item-meta">
                      <span><i class="fa-solid fa-users me-1 text-secondary"></i> Lớp 4 học viên (Trưởng nhóm: Trần Bảo Long)</span>
                      <span class="meta-dot">•</span>
                      <span><i class="fa-regular fa-calendar me-1 text-secondary"></i> Thứ 4 hàng tuần (18:00 - 20:00)</span>
                    </div>
                  </div>

                  <div class="class-item-buttons">
                    <button class="btn btn-outline-secondary btn-class-details" @click="openClassDetails('Lập trình Web React')">
                      Chi tiết buổi dạy
                    </button>
                    <button class="btn btn-outline-primary btn-class-details" @click="showStudentListModal">
                      <i class="fa-solid fa-user-group me-1"></i> Danh sách học viên
                    </button>
                  </div>
                </div>
              </div>

              <!-- Lớp 3: Luyện đề THPT Quốc Gia (Offline) -->
              <div class="class-enroll-item">
                <div class="class-item-top">
                  <div class="class-pills">
                    <span class="pill-badge pill-gray">Lớp luyện thi tập trung</span>
                    <span class="pill-badge pill-gray-outline">
                      <i class="fa-solid fa-location-dot me-1"></i> Offline Cơ sở Q.10
                    </span>
                  </div>
                </div>

                <div class="class-item-main">
                  <div>
                    <h3 class="class-item-title">Luyện đề THPT Quốc Gia môn Toán & Khoa học tự nhiên</h3>
                    <div class="class-item-meta">
                      <span><i class="fa-solid fa-users-viewfinder me-1 text-secondary"></i> 35 học sinh ghi danh</span>
                      <span class="meta-dot">•</span>
                      <span><i class="fa-regular fa-calendar me-1 text-secondary"></i> Chủ nhật (08:00 - 11:30)</span>
                    </div>
                  </div>

                  <div class="class-item-buttons">
                    <button class="btn btn-light btn-class-details text-muted" disabled>
                      <i class="fa-regular fa-envelope me-1"></i> Đã gửi giáo trình bài giảng
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- THẺ 3: KHUNG GIỜ DẠY & LỊCH RẢNH SMARTTRIAL TUẦN NÀY -->
          <div class="content-card mt-4">
            <div class="card-header-flex">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-primary">
                  <i class="fa-regular fa-calendar-days"></i>
                </div>
                <div>
                  <h2 class="card-title mb-0">Khung giờ dạy & Lịch mở nhận học thử</h2>
                  <div class="card-sub-label">Hệ thống EduLink tự động ghép ca học thử và nhận học viên mới</div>
                </div>
              </div>
              <router-link to="/my-schedule" class="btn btn-sm btn-outline-primary btn-update-slots">
                <i class="fa-solid fa-calendar-plus me-1"></i> Mở thêm khung giờ nhận lớp
              </router-link>
            </div>

            <!-- 7 Ô ngày trong tuần -->
            <div class="week-free-slots-grid mt-3">
              <!-- Thứ 2 -->
              <div class="day-slot-card slot-booked">
                <div class="day-label">Thứ 2</div>
                <div class="slot-time-text">19:30 - 21:00</div>
              </div>

              <!-- Thứ 3 -->
              <div class="day-slot-card slot-busy">
                <div class="day-label">Thứ 3</div>
                <div class="slot-time-text">Bận nghiên cứu ĐH</div>
              </div>

              <!-- Thứ 4 -->
              <div class="day-slot-card slot-booked">
                <div class="day-label">Thứ 4</div>
                <div class="slot-time-text">18:00 - 20:00</div>
              </div>

              <!-- Thứ 5 -->
              <div class="day-slot-card slot-available">
                <div class="day-label">Thứ 5</div>
                <div class="slot-time-text text-success fw-bold">19:00 - 21:00 (Mở dạy)</div>
              </div>

              <!-- Thứ 6 -->
              <div class="day-slot-card slot-booked">
                <div class="day-label">Thứ 6</div>
                <div class="slot-time-text">19:30 - 21:00</div>
              </div>

              <!-- Thứ 7 -->
              <div class="day-slot-card slot-available">
                <div class="day-label">Thứ 7</div>
                <div class="slot-time-text text-success fw-bold">14:00 - 17:00 (Mở dạy)</div>
              </div>

              <!-- Chủ nhật -->
              <div class="day-slot-card slot-booked">
                <div class="day-label">Chủ nhật</div>
                <div class="slot-time-text">08:00 - 11:30</div>
              </div>
            </div>

            <!-- Chú thích legend -->
            <div class="slots-legend mt-3">
              <span class="legend-pill"><span class="legend-dot dot-booked"></span> Ca dạy đã xếp lịch</span>
              <span class="legend-pill"><span class="legend-dot dot-available"></span> Khung giờ sẵn sàng nhận ca học thử</span>
            </div>
          </div>

          <!-- THẺ 4: ĐÁNH GIÁ & PHẢN HỒI TỪ HỌC VIÊN GẦN NHẤT -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-danger">
                  <i class="fa-regular fa-comment-dots"></i>
                </div>
                <h2 class="card-title mb-0">Đánh giá & Phản hồi từ học viên gần nhất</h2>
              </div>
            </div>

            <div class="student-feedback-card">
              <div class="feedback-top">
                <div class="student-reviewer-left">
                  <img
                    src="https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=120&q=80"
                    alt="Nguyễn Linh Lan"
                    class="student-reviewer-avatar"
                  />
                  <div>
                    <div class="student-reviewer-name">Nguyễn Linh Lan</div>
                    <div class="student-reviewer-role">Học sinh Lớp 12A1 - THPT Chuyên Lê Hồng Phong</div>
                  </div>
                </div>
                <div class="feedback-stars">
                  <i class="fa-solid fa-star text-warning" v-for="star in 5" :key="star"></i>
                  <span class="stars-num ms-1">5.0 / 5.0</span>
                </div>
              </div>

              <blockquote class="feedback-quote">
                "Thầy Triết giảng bài cực kỳ dễ hiểu, phương pháp bản chất toán học giúp em tự tin giải các câu phân loại 9+ trong đề thi ĐGNL. Điểm danh Face ID vào lớp rất nhanh và thầy phản hồi bài tập rất tận tình sau mỗi buổi học."
              </blockquote>

              <div class="feedback-date text-muted">
                Đánh giá sau buổi học kèm 1:1 ngày 22/02/2025
              </div>
            </div>
          </div>
        </div>

        <!-- CỘT PHẢI: FACE ID, THÙ LAO & TRỢ GIẢNG -->
        <aside class="profile-right-column">
          <!-- CARD 1: SINH TRẮC HỌC FACE ID -->
          <div class="content-card">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-success">
                  <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h3 class="card-title fs-6 mb-0">Sinh trắc học Face ID</h3>
              </div>
            </div>

            <!-- Khung hiển thị kích hoạt Face ID -->
            <div class="faceid-status-box">
              <div class="faceid-icon-circle">
                <i class="fa-solid fa-face-smile"></i>
              </div>
              <div class="faceid-status-title">Đã kích hoạt & Sẵn sàng</div>
              <div class="faceid-match-rate">Độ khớp mẫu khuôn mặt: <strong>99.1%</strong></div>
              <p class="faceid-desc">
                Giảng viên quét khuôn mặt xác thực danh tính để bắt đầu ca dạy trực tuyến và tự động ghi nhận thời lượng buổi học.
              </p>
              <router-link to="/face-id" class="btn btn-outline-secondary btn-rescan-face w-100">
                <i class="fa-solid fa-rotate me-1"></i> Quét lại khuôn mặt mẫu
              </router-link>
            </div>

            <!-- Thông số thiết bị -->
            <div class="device-specs-list mt-3">
              <div class="spec-row">
                <span class="spec-label"><i class="fa-solid fa-laptop me-2 text-secondary"></i> Thiết bị chính:</span>
                <span class="spec-val fw-bold">MacBook Air M2 (macOS 14)</span>
              </div>
              <div class="spec-row mt-2">
                <span class="spec-label"><i class="fa-solid fa-video me-2 text-secondary"></i> Camera HD:</span>
                <span class="spec-val text-success fw-bold">Hoạt động tốt (1080p)</span>
              </div>
            </div>
          </div>

          <!-- CARD 2: TÌNH TRẠNG THÙ LAO GIẢNG DẠY -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-primary">
                  <i class="fa-solid fa-credit-card"></i>
                </div>
                <h3 class="card-title fs-6 mb-0">Tình trạng thù lao</h3>
              </div>
              <i class="fa-solid fa-circle-check text-success fs-5"></i>
            </div>

            <div class="tuition-box">
              <div class="tuition-top-line">
                <div>
                  <div class="tuition-term-name">Thù lao Kỳ II (2024 - 2025)</div>
                  <div class="tuition-package-sub">Hợp đồng Giảng viên Cao cấp</div>
                </div>
                <span class="badge-paid">Đã quyết toán</span>
              </div>

              <div class="tuition-price-row mt-3">
                <span class="tuition-amount">24.850.000</span>
                <span class="tuition-currency">VND / tháng</span>
              </div>

              <div class="tuition-vietqr-status text-success mt-1">
                <i class="fa-solid fa-check me-1"></i> Đã chuyển khoản qua Vietcombank
              </div>
            </div>

            <div class="invoice-meta-info mt-3">
              <div class="invoice-meta-row">
                <span class="text-muted">Mã giao dịch điện tử:</span>
                <span class="fw-bold">TXN - 2025 - 8288</span>
              </div>
              <div class="invoice-meta-row mt-1">
                <span class="text-muted">Hóa đơn gửi về:</span>
                <span>triet.nguyen@smarttrial.edu.vn</span>
              </div>
            </div>

            <button class="btn btn-outline-secondary btn-tuition-history w-100 mt-3" @click="handleViewSalaryReport">
              <i class="fa-regular fa-file-lines me-1"></i> Xem chi tiết bảng kê thù lao
            </button>
          </div>

          <!-- CARD 3: TRỢ GIẢNG & GIÁO VỤ PHỤ TRÁCH -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-info">
                  <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="card-title fs-6 mb-0">Trợ giảng & Giáo vụ phụ trách</h3>
              </div>
            </div>

            <div class="parent-info-card">
              <div class="parent-profile-row">
                <div class="parent-avatar-circle">LT</div>
                <div>
                  <div class="parent-name">Lê Thu Trang</div>
                  <div class="parent-role">Trợ giảng phụ trách lớp (EduLink)</div>
                </div>
              </div>

              <div class="parent-contacts-list mt-3">
                <div class="contact-line">
                  <i class="fa-solid fa-phone me-2 text-secondary"></i>
                  <span class="text-muted">Điện thoại:</span>
                  <a href="tel:0912345678" class="text-primary text-decoration-none fw-bold ms-auto">0912.345.678</a>
                </div>
                <div class="contact-line mt-2">
                  <i class="fa-solid fa-envelope me-2 text-secondary"></i>
                  <span class="text-muted">Email:</span>
                  <a href="mailto:trang.le@edulink.vn" class="text-secondary text-decoration-none ms-auto">trang.le@edulink.vn</a>
                </div>
              </div>

              <div class="parent-note-box mt-3">
                <i class="fa-regular fa-bell text-success me-2"></i>
                <span>Đã liên kết nhận thông báo hỗ trợ kỹ thuật phòng học, điểm danh sinh trắc học và lịch trực từ EduLink.</span>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TeacherDashboard",
  data() {
    return {};
  },
  methods: {
    handleEditProfile() {
      alert("Chức năng chỉnh sửa thông tin hồ sơ giảng viên đang mở!");
    },
    handleDownloadReport() {
      alert("Đang chuẩn bị và tải xuống Báo cáo giảng dạy & Điểm danh định kỳ...");
    },
    openClassDetails(className) {
      alert(`Đang mở chi tiết buổi dạy cho lớp: ${className}`);
    },
    showStudentListModal() {
      alert("Danh sách học viên lớp Lập trình Web:\n1. Trần Bảo Long (Trưởng nhóm)\n2. Phạm Thu Hà\n3. Lê Quốc Khánh\n4. Đỗ Phương Anh");
    },
    handleViewSalaryReport() {
      alert("Đang chuyển tới Bảng kê chi tiết thù lao giảng dạy tháng 02/2025...");
    }
  }
};
</script>

<style scoped>
.teacher-dashboard-page {
  min-height: 100vh;
  background-color: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  padding: 24px 20px 60px;
  display: flex;
  justify-content: center;
}

.profile-container {
  width: 100%;
  max-width: 1240px;
}

/* 1. Breadcrumb & Role Switcher */
.breadcrumb-and-switcher {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  gap: 16px;
  flex-wrap: wrap;
}

.breadcrumb-nav {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 0;
  flex-wrap: wrap;
}

.role-switcher-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  padding: 4px 6px;
  border-radius: 30px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.role-switcher-label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  padding-left: 8px;
}

.role-switcher-pills {
  display: flex;
  gap: 4px;
}

.role-pill {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 20px;
  color: #64748b;
  text-decoration: none;
  transition: all 0.2s ease;
}

.role-pill:hover {
  color: #0060d2;
  background: #f1f5f9;
}

.role-pill.active {
  background: #0060d2;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 96, 210, 0.25);
}

.crumb-link {
  color: #64748b;
  text-decoration: none;
}

.crumb-link:hover {
  color: #0060d2;
}

.crumb-sep {
  color: #94a3b8;
}

.crumb-active {
  color: #0f172a;
  font-weight: 600;
}

/* 2. Top Profile Header Card */
.profile-header-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 28px 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
  margin-bottom: 26px;
}

.profile-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.profile-info-left {
  display: flex;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}

.teacher-avatar-box {
  position: relative;
  width: 96px;
  height: 96px;
  flex-shrink: 0;
}

.teacher-avatar-img {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #ffffff;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
}

.faceid-verified-icon {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background-color: #10b981;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  border: 2.5px solid #ffffff;
}

.teacher-meta-details {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.teacher-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.teacher-name {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  letter-spacing: -0.3px;
}

.badge-faceid-verified {
  background-color: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
}

.green-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #10b981;
}

.teacher-sub-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13.5px;
  color: #475569;
  flex-wrap: wrap;
}

.badge-teacher-id {
  background-color: #eff6ff;
  color: #0060d2;
  font-weight: 600;
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 6px;
}

.meta-dot {
  color: #94a3b8;
}

.profile-top-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-action-outline {
  background-color: #ffffff;
  border: 1px solid #cbd5e1;
  color: #334155;
  font-size: 13.5px;
  font-weight: 600;
  padding: 9px 16px;
  border-radius: 10px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: all 0.15s ease;
}

.btn-action-outline:hover {
  background-color: #f1f5f9;
  border-color: #94a3b8;
  color: #0f172a;
}

.btn-download-report {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 13.5px;
  font-weight: 600;
  padding: 9px 18px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
  display: inline-flex;
  align-items: center;
}

.btn-download-report:hover {
  background-color: #004fb0;
  border-color: #004fb0;
}

/* Stats bar */
.profile-stats-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 28px;
  padding-top: 24px;
  border-top: 1px solid #f1f5f9;
}

.stat-highlight-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 14px;
  background-color: #f8fafc;
  border-radius: 14px;
  border: 1px solid #f1f5f9;
}

.stat-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.icon-blue {
  background-color: #eff6ff;
  color: #0060d2;
}

.icon-indigo {
  background-color: #eef2ff;
  color: #4f46e5;
}

.icon-green {
  background-color: #ecfdf5;
  color: #059669;
}

.icon-orange {
  background-color: #fff7ed;
  color: #ea580c;
}

.stat-top-line {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.stat-num {
  font-size: 22px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.1;
}

.stat-unit {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

.stat-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.stat-sub {
  font-size: 11px;
  font-weight: 500;
  margin-top: 2px;
}

/* 3. Main layout 2 columns */
.profile-layout {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 26px;
  align-items: start;
}

.content-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.015);
}

.card-header-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.card-title-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background-color: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
}

.card-title {
  font-size: 16.5px;
  font-weight: 700;
  color: #0f172a;
}

.card-sub-label {
  font-size: 12px;
  color: #64748b;
}

.badge-exam-focus {
  background-color: #f1f5f9;
  color: #475569;
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
}

/* Goal highlight box */
.goal-highlight-box {
  background-color: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 14px;
  padding: 16px 20px;
  margin-top: 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.goal-left {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.goal-flag-icon {
  font-size: 18px;
  margin-top: 2px;
}

.goal-label {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.goal-desc {
  font-size: 12.5px;
  color: #475569;
  margin-top: 2px;
}

.goal-percent-box {
  text-align: right;
  flex-shrink: 0;
}

.goal-number {
  font-size: 24px;
  font-weight: 800;
  color: #0060d2;
  line-height: 1;
}

.goal-sub {
  font-size: 11px;
  color: #64748b;
  margin-top: 2px;
}

/* Overall progress */
.overall-progress-wrap {
  margin-top: 16px;
}

.progress-labels-row {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  margin-bottom: 6px;
}

.progress-text-label {
  color: #475569;
  font-weight: 500;
}

.progress-val-label {
  color: #0060d2;
  font-weight: 700;
}

.custom-progress-bar {
  height: 8px;
  border-radius: 6px;
  background-color: #e2e8f0;
}

/* 3 Subjects progress grid */
.subjects-progress-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.subject-progress-card {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 12px 14px;
}

.subj-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12.5px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 8px;
}

.sub-progress {
  height: 6px;
  border-radius: 4px;
  background-color: #e2e8f0;
}

.subj-note {
  font-size: 11px;
  color: #64748b;
  margin-top: 6px;
}

.text-warning-dark {
  color: #d97706 !important;
}

/* Classes list */
.view-history-link {
  font-size: 12.5px;
  color: #0060d2;
  text-decoration: none;
  font-weight: 600;
}

.view-history-link:hover {
  text-decoration: underline;
}

.enrolled-classes-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.class-enroll-item {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px 18px;
  background-color: #ffffff;
  transition: all 0.15s ease;
}

.class-enroll-item:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.highlight-border-blue {
  border-color: #bfdbfe;
  background-color: #fcfdff;
}

.class-item-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.class-pills {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pill-badge {
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
}

.pill-blue {
  background-color: #eff6ff;
  color: #0060d2;
}

.pill-purple {
  background-color: #f3e8ff;
  color: #7e22ce;
}

.pill-gray {
  background-color: #f1f5f9;
  color: #475569;
}

.pill-green-outline {
  border: 1px solid #a7f3d0;
  background-color: #f0fdf4;
  color: #15803d;
}

.pill-gray-outline {
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
  color: #64748b;
}

.class-upcoming-badge {
  font-size: 11.5px;
  font-weight: 600;
  color: #d97706;
}

.class-item-main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.class-item-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.class-item-meta {
  font-size: 12.5px;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.class-item-buttons {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-class-details {
  font-size: 12.5px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 8px;
}

.btn-enter-faceid {
  font-size: 12.5px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 8px;
  background-color: #0060d2;
  border-color: #0060d2;
  text-decoration: none;
}

.btn-enter-faceid:hover {
  background-color: #004fb0;
}

/* Week free slots grid */
.btn-update-slots {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 8px;
  text-decoration: none;
}

.week-free-slots-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 8px;
}

.day-slot-card {
  border-radius: 10px;
  padding: 10px 6px;
  text-align: center;
  font-size: 11.5px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 68px;
}

.day-label {
  font-weight: 700;
  color: #334155;
  margin-bottom: 4px;
}

.slot-time-text {
  font-size: 11px;
}

.slot-booked {
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #1e40af;
}

.slot-busy {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #94a3b8;
}

.slot-available {
  background-color: #f0fdf4;
  border: 1px dashed #4ade80;
  color: #166534;
}

.slots-legend {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 12px;
  color: #64748b;
}

.legend-pill {
  display: flex;
  align-items: center;
  gap: 6px;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.dot-booked {
  background-color: #0060d2;
}

.dot-available {
  background-color: #10b981;
}

/* Teacher feedback */
.student-feedback-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 18px 20px;
}

.feedback-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
  flex-wrap: wrap;
  gap: 10px;
}

.student-reviewer-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.student-reviewer-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
}

.student-reviewer-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.student-reviewer-role {
  font-size: 12px;
  color: #64748b;
}

.feedback-stars {
  font-size: 12px;
  display: flex;
  align-items: center;
}

.stars-num {
  font-weight: 700;
  color: #0f172a;
}

.feedback-quote {
  font-size: 13px;
  color: #334155;
  font-style: italic;
  line-height: 1.6;
  margin: 0 0 10px;
}

.feedback-date {
  font-size: 11.5px;
}

/* Right column styles */
.faceid-status-box {
  background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
  border: 1px solid #bbf7d0;
  border-radius: 14px;
  padding: 20px 16px;
  text-align: center;
}

.faceid-icon-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #ecfdf5;
  border: 2px solid #86efac;
  color: #059669;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  margin: 0 auto 12px;
}

.faceid-status-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #065f46;
}

.faceid-match-rate {
  font-size: 12px;
  color: #047857;
  margin-top: 2px;
}

.faceid-desc {
  font-size: 11.5px;
  color: #475569;
  margin: 10px 0 14px;
  line-height: 1.5;
}

.btn-rescan-face {
  font-size: 12px;
  font-weight: 600;
  padding: 7px 12px;
  border-radius: 8px;
  text-decoration: none;
}

.device-specs-list {
  font-size: 12px;
}

.spec-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.spec-label {
  color: #64748b;
}

/* Tuition box */
.tuition-box {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px;
}

.tuition-top-line {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.tuition-term-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.tuition-package-sub {
  font-size: 11.5px;
  color: #64748b;
}

.badge-paid {
  background-color: #ecfdf5;
  color: #059669;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
  border: 1px solid #a7f3d0;
}

.tuition-price-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.tuition-amount {
  font-size: 22px;
  font-weight: 800;
  color: #0f172a;
}

.tuition-currency {
  font-size: 12px;
  color: #64748b;
}

.tuition-vietqr-status {
  font-size: 11.5px;
  font-weight: 600;
}

.invoice-meta-info {
  font-size: 12px;
}

.invoice-meta-row {
  display: flex;
  justify-content: space-between;
}

.btn-tuition-history {
  font-size: 12.5px;
  font-weight: 600;
  padding: 8px 12px;
  border-radius: 8px;
}

/* Parent/Assistant info */
.parent-info-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px;
}

.parent-profile-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.parent-avatar-circle {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background-color: #eff6ff;
  color: #0060d2;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  border: 1px solid #bfdbfe;
}

.parent-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.parent-role {
  font-size: 12px;
  color: #64748b;
}

.parent-contacts-list {
  font-size: 12px;
}

.contact-line {
  display: flex;
  align-items: center;
}

.parent-note-box {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 11px;
  color: #475569;
  line-height: 1.5;
  display: flex;
  align-items: flex-start;
}

/* Responsive */
@media (max-width: 992px) {
  .profile-layout {
    grid-template-columns: 1fr;
  }
  .profile-stats-bar {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .profile-header-card {
    padding: 20px;
  }
  .profile-stats-bar {
    grid-template-columns: 1fr;
  }
  .subjects-progress-grid {
    grid-template-columns: 1fr;
  }
  .week-free-slots-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
