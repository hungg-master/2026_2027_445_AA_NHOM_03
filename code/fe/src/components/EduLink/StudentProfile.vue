<template>
  <div class="student-profile-page">
    <div class="profile-container">
      <!-- 1. BREADCRUMB (ĐÃ BỎ SWITCHER GIÁO VIÊN) -->
      <div class="breadcrumb-and-switcher">
        <nav class="breadcrumb-nav">
          <i class="fa-solid fa-house me-1"></i>
          <router-link to="/" class="crumb-link">Trang chủ</router-link>
          <span class="crumb-sep">›</span>
          <span class="crumb-link">Học viên</span>
          <span class="crumb-sep">›</span>
          <span class="crumb-active">Hồ sơ cá nhân: {{ ten_nguoi_dung }}</span>
        </nav>
      </div>

      <!-- 2. TOP PROFILE BANNER (THẺ HỌC VIÊN) -->
      <div class="profile-header-card">
        <div class="profile-top-row">
          <!-- Left: Avatar & Tên, Lớp, Trường -->
          <div class="profile-info-left">
            <div class="student-avatar-box">
              <img
                src="https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=240&q=80"
                alt="Nguyễn Linh Lan"
                class="student-avatar-img"
              />
              <div class="faceid-verified-icon" title="Chưa xác thực danh tính">
                <i class="fa-solid fa-shield-halved"></i>
              </div>
            </div>

            <div class="student-meta-details">
              <div class="student-name-row">
                <h1 class="student-name">{{ ten_nguoi_dung }}</h1>
                <span :class="da_xac_minh ? 'badge-faceid-verified' : 'badge-faceid-unverified'">
                  <i :class="da_xac_minh ? 'bx bxs-check-shield text-success me-1' : 'bx bx-error-circle text-warning me-1'"></i>
                  {{ da_xac_minh ? 'Đã xác thực Face ID sinh trắc học' : 'Chưa xác thực Face ID' }}
                </span>
              </div>

              <div class="student-sub-info">
                <span class="badge-student-id">Mã HV: EDU-202488</span>
                <span class="meta-dot">•</span>
                <span>Lớp 12 - Chuyên Tự Nhiên (12A1)</span>
                <span class="meta-dot">•</span>
                <span>
                  <i class="fa-solid fa-graduation-cap text-primary me-1"></i>
                  THPT Chuyên Lê Hồng Phong
                </span>
              </div>
            </div>
          </div>

          <!-- Right: Nút hành động nhanh -->
          <div class="profile-top-actions">
            <button class="btn btn-action-outline" @click="handleEditProfile">
              <i class="fa-regular fa-pen-to-square me-1"></i> Chỉnh sửa thông tin
            </button>
            <button class="btn btn-action-outline" @click="scrollToFaceId">
              <i class="fa-solid fa-rotate me-1"></i> Cập nhật Face ID
            </button>
            <button class="btn btn-primary btn-download-report" @click="handleDownloadReport">
              <i class="fa-solid fa-download me-1"></i> Tải bảng điểm học tập
            </button>
          </div>
        </div>

        <!-- Dải 4 chỉ số nổi bật (Highlights row) -->
        <div class="profile-stats-bar">
          <!-- Chỉ số 1: Giờ học tích lũy -->
          <div class="stat-highlight-item">
            <div class="stat-icon-box icon-blue">
              <i class="fa-regular fa-clock"></i>
            </div>
            <div>
              <div class="stat-top-line">
                <span class="stat-num">48</span> <span class="stat-unit">giờ</span>
              </div>
              <div class="stat-label">Giờ học tích lũy</div>
              <div class="stat-sub text-success">
                <i class="fa-solid fa-arrow-trend-up me-1"></i> +4h tuần này
              </div>
            </div>
          </div>

          <!-- Chỉ số 2: Môn đang theo kèm -->
          <div class="stat-highlight-item">
            <div class="stat-icon-box icon-indigo">
              <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
              <div class="stat-top-line">
                <span class="stat-num">04</span> <span class="stat-unit">môn học</span>
              </div>
              <div class="stat-label">Môn đang theo kèm</div>
              <div class="stat-sub text-muted">3 lớp Online, 1 Offline</div>
            </div>
          </div>

          <!-- Chỉ số 3: Điểm chuyên cần -->
          <div class="stat-highlight-item">
            <div class="stat-icon-box icon-green">
              <i class="fa-regular fa-circle-check"></i>
            </div>
            <div>
              <div class="stat-top-line">
                <span class="stat-num text-success">98%</span>
              </div>
              <div class="stat-label">Điểm chuyên cần</div>
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
                <span class="stat-num">4.8</span> <span class="stat-unit">/ 5.0</span>
              </div>
              <div class="stat-label">Đánh giá trung bình</div>
              <div class="stat-sub text-muted">Từ 3 giáo viên hướng dẫn</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. BỐ CỤC 2 CỘT NỘI DUNG -->
      <div class="profile-layout">
        <!-- CỘT TRÁI: TIẾN ĐỘ, LỚP HỌC & LỊCH RẢNH -->
        <div class="profile-left-column">
          <!-- THẺ 1: MỤC TIÊU & TIẾN ĐỘ HỌC TẬP -->
          <div class="content-card">
            <div class="card-header-flex">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-primary">
                  <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                  <h2 class="card-title mb-0">Mục tiêu & Tiến độ học tập</h2>
                  <div class="card-sub-label">Kế hoạch kỳ II (Năm học 2024 - 2025)</div>
                </div>
              </div>
              <span class="badge-exam-focus">Kỳ thi trọng tâm: Tháng 06/2025</span>
            </div>

            <!-- Khung mục tiêu chính đặt ra -->
            <div class="goal-highlight-box">
              <div class="goal-left">
                <i class="fa-regular fa-flag text-primary goal-flag-icon"></i>
                <div>
                  <div class="goal-label">Mục tiêu chính đặt ra:</div>
                  <div class="goal-desc">
                    Ôn thi Đánh giá năng lực (ĐGNL) ĐHQG & Đạt điểm 9.0+ môn Toán, Tin ...
                  </div>
                </div>
              </div>
              <div class="goal-percent-box">
                <div class="goal-number">75%</div>
                <div class="goal-sub">Hoàn thành kỳ</div>
              </div>
            </div>

            <!-- Thanh tiến độ tổng quát -->
            <div class="overall-progress-wrap mt-3">
              <div class="progress-labels-row">
                <span class="progress-text-label">Tiến độ chương trình học kỳ hiện tại</span>
                <span class="progress-val-label">75% (36/48 chuyên đề)</span>
              </div>
              <div class="progress custom-progress-bar">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
              </div>
            </div>

            <!-- 3 Khối tiến độ môn học -->
            <div class="subjects-progress-grid mt-4">
              <!-- Môn 1: Toán nâng cao -->
              <div class="subject-progress-card">
                <div class="subj-header">
                  <span class="subj-name">Toán nâng cao</span>
                  <span class="subj-percent text-primary fw-bold">85%</span>
                </div>
                <div class="progress sub-progress">
                  <div class="progress-bar bg-primary" style="width: 85%"></div>
                </div>
                <div class="subj-note">17/20 dạng bài hoàn tất</div>
              </div>

              <!-- Môn 2: Lập trình Web -->
              <div class="subject-progress-card">
                <div class="subj-header">
                  <span class="subj-name">Lập trình Web</span>
                  <span class="subj-percent text-success fw-bold">70%</span>
                </div>
                <div class="progress sub-progress">
                  <div class="progress-bar bg-success" style="width: 70%"></div>
                </div>
                <div class="subj-note">Đang làm đồ án Mini CMS</div>
              </div>

              <!-- Môn 3: Tiếng Anh IELTS -->
              <div class="subject-progress-card">
                <div class="subj-header">
                  <span class="subj-name">Tiếng Anh IELTS</span>
                  <span class="subj-percent text-warning-dark fw-bold">60%</span>
                </div>
                <div class="progress sub-progress">
                  <div class="progress-bar bg-warning" style="width: 60%"></div>
                </div>
                <div class="subj-note">Band dự kiến: 7.0+</div>
              </div>
            </div>
          </div>

          <!-- THẺ 2: LỚP HỌC ĐANG THEO HỌC (03 LỚP) -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-success">
                  <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <h2 class="card-title mb-0">Lớp học đang theo học (03 lớp)</h2>
              </div>
              <router-link to="/my-classes" class="view-history-link">
                Xem lịch sử lớp <i class="fa-solid fa-chevron-right ms-1 fs-8"></i>
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
                    <i class="fa-regular fa-clock me-1"></i> Sắp diễn ra: 20:00 Tối nay
                  </div>
                </div>

                <div class="class-item-main">
                  <div>
                    <h3 class="class-item-title">Toán học nâng cao 301 - Giải tích & ĐGNL</h3>
                    <div class="class-item-meta">
                      <span><i class="fa-regular fa-user me-1 text-secondary"></i> TS. Nguyễn Minh Triết</span>
                      <span class="meta-dot">•</span>
                      <span><i class="fa-regular fa-calendar me-1 text-secondary"></i> Thứ 2 & Thứ 6 (19:30 - 21:00)</span>
                    </div>
                  </div>

                  <div class="class-item-buttons">
                    <button class="btn btn-outline-secondary btn-class-details" @click="openClassDetails('Toán 301')">
                      Chi tiết buổi học
                    </button>
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
                      <span><i class="fa-regular fa-user me-1 text-secondary"></i> ThS. Trần Hoàng Nam</span>
                      <span class="meta-dot">•</span>
                      <span><i class="fa-regular fa-calendar me-1 text-secondary"></i> Thứ 4 hàng tuần (18:00 - 20:00)</span>
                    </div>
                  </div>

                  <div class="class-item-buttons">
                    <button class="btn btn-outline-secondary btn-class-details" @click="openClassDetails('Lập trình Web')">
                      Chi tiết buổi học
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
                      <span><i class="fa-regular fa-user me-1 text-secondary"></i> Tổ bộ môn THPT Chuyên</span>
                      <span class="meta-dot">•</span>
                      <span><i class="fa-regular fa-calendar me-1 text-secondary"></i> Chủ nhật (08:00 - 11:30)</span>
                    </div>
                  </div>

                  <div class="class-item-buttons">
                    <button class="btn btn-light btn-class-details text-muted" disabled>
                      <i class="fa-regular fa-envelope me-1"></i> Đã gửi email địa chỉ
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- THẺ 3: KHUNG GIỜ RẢNH ĐÃ ĐĂNG KÝ TUẦN NÀY -->
          <div class="content-card mt-4">
            <div class="card-header-flex">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-primary">
                  <i class="fa-regular fa-calendar-days"></i>
                </div>
                <div>
                  <h2 class="card-title mb-0">Khung giờ rảnh đã đăng ký tuần này</h2>
                  <div class="card-sub-label">Hệ thống EduLink tự động ghép lịch học phù hợp với gia sư</div>
                </div>
              </div>
              <router-link to="/my-schedule" class="btn btn-sm btn-outline-primary btn-update-slots">
                <i class="fa-solid fa-calendar-plus me-1"></i> Cập nhật lịch rảnh mới
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
                <div class="slot-time-text">Bận học trường</div>
              </div>

              <!-- Thứ 4 -->
              <div class="day-slot-card slot-booked">
                <div class="day-label">Thứ 4</div>
                <div class="slot-time-text">18:00 - 20:00</div>
              </div>

              <!-- Thứ 5 -->
              <div class="day-slot-card slot-available">
                <div class="day-label">Thứ 5</div>
                <div class="slot-time-text text-success fw-bold">19:00 - 21:00 (Rảnh)</div>
              </div>

              <!-- Thứ 6 -->
              <div class="day-slot-card slot-booked">
                <div class="day-label">Thứ 6</div>
                <div class="slot-time-text">19:30 - 21:00</div>
              </div>

              <!-- Thứ 7 -->
              <div class="day-slot-card slot-available">
                <div class="day-label">Thứ 7</div>
                <div class="slot-time-text text-success fw-bold">14:00 - 17:00 (Rảnh)</div>
              </div>

              <!-- Chủ nhật -->
              <div class="day-slot-card slot-booked">
                <div class="day-label">Chủ nhật</div>
                <div class="slot-time-text">08:00 - 11:30</div>
              </div>
            </div>

            <!-- Chú thích legend -->
            <div class="slots-legend mt-3">
              <span class="legend-pill"><span class="legend-dot dot-booked"></span> Lớp học đã xếp</span>
              <span class="legend-pill"><span class="legend-dot dot-available"></span> Khung giờ sẵn sàng nhận gia sư</span>
            </div>
          </div>

          <!-- THẺ 4: NHẬN XÉT TỪ GIÁO VIÊN HƯỚNG DẪN GẦN NHẤT -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-danger">
                  <i class="fa-regular fa-comment-dots"></i>
                </div>
                <h2 class="card-title mb-0">Nhận xét từ giáo viên hướng dẫn gần nhất</h2>
              </div>
            </div>

            <div class="teacher-feedback-card">
              <div class="feedback-top">
                <div class="teacher-reviewer-left">
                  <img
                    src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=120&q=80"
                    alt="TS. Nguyễn Minh Triết"
                    class="teacher-reviewer-avatar"
                  />
                  <div>
                    <div class="teacher-reviewer-name">TS. Nguyễn Minh Triết</div>
                    <div class="teacher-reviewer-role">Giảng viên bộ môn Giải tích - Đại học KHTN</div>
                  </div>
                </div>
                <div class="feedback-stars">
                  <i class="fa-solid fa-star text-warning" v-for="i in 5" :key="i"></i>
                  <span class="stars-num ms-1">5.0 / 5.0</span>
                </div>
              </div>

              <blockquote class="feedback-quote">
                "Học sinh Linh Lan tiếp thu nhanh các dạng bài tổ hợp và phương trình vi phân nâng cao,
                luôn chủ động đặt câu hỏi phản biện. Điểm danh vào lớp chuẩn xác và đúng giờ qua Face ID,
                thái độ học tập rất tích cực và kiên trì."
              </blockquote>

              <div class="feedback-date text-muted">
                Nhận xét sau buổi học ngày 22/02/2025
              </div>
            </div>
          </div>
        </div>

        <!-- CỘT PHẢI: FACE ID, HỌC PHÍ & PHỤ HUYNH -->
        <aside class="profile-right-column">
          <!-- CARD 1: XÁC THỰC FACE ID AI -->
          <div class="content-card" id="face-id-section" ref="faceIdSection" style="border-radius: 16px;">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="d-flex justify-content-center align-items-center rounded me-2 shadow-sm"
                     style="background-color: #fff7ed; width: 38px; height: 38px;">
                  <i class="bx bx-face fs-4" style="color: #ea580c;"></i>
                </div>
                <div>
                  <h3 class="card-title fs-6 mb-0 fw-bolder text-dark">Xác thực Face ID AI</h3>
                  <div class="card-sub-label">Bảo mật phòng học với công nghệ nhận diện AI</div>
                </div>
              </div>
            </div>

            <!-- Trạng thái: ĐÃ XÁC MINH -->
            <template v-if="da_xac_minh">
              <div class="text-center py-3">
                <div class="position-relative d-inline-block mb-3">
                  <div class="rounded-circle d-flex justify-content-center align-items-center shadow-sm mx-auto"
                       style="width: 130px; height: 130px; background-color: #f0fdf4; border: 2px solid #bbf7d0;">
                    <i class="bx bx-check-double" style="font-size: 4rem; color: #22c55e;"></i>
                  </div>
                  <div class="position-absolute bottom-0 end-0 bg-success rounded-circle d-flex justify-content-center align-items-center border border-2 border-white"
                       style="width: 36px; height: 36px;">
                    <i class="bx bxs-shield-alt-2 text-white"></i>
                  </div>
                </div>

                <h5 class="fw-bolder text-dark mb-1">Tài khoản đã xác thực</h5>
                <p class="text-muted mx-auto mb-3 px-2" style="font-size: 0.85rem; line-height: 1.45;">
                  Hệ thống AI đã ghi nhận mẫu khuôn mặt của bạn. Danh tính của bạn hiện đã được bảo vệ và sẵn sàng tham gia lớp học.
                </p>

                <div class="mb-3">
                  <span class="badge px-3 py-2 fw-bold"
                        style="background-color: #dcfce7; color: #15803d; border-radius: 30px; font-size: 0.8rem;">
                    <i class="bx bxs-lock-alt me-1"></i> Mã hóa sinh trắc học
                  </span>
                </div>

                <button @click="startFaceScan" class="btn btn-sm btn-outline-secondary px-3 py-1" style="border-radius: 8px; font-size: 0.8rem;">
                  <i class="bx bx-refresh me-1"></i> Quét lại khuôn mặt
                </button>
              </div>
            </template>

            <!-- Trạng thái: CHƯA XÁC MINH / ĐANG QUÉT -->
            <template v-else>
              <div class="text-center py-2">
                <div class="d-flex justify-content-center">
                  <div class="position-relative d-flex justify-content-center align-items-center"
                       style="width: 200px; height: 200px;">
                    <!-- Placeholder khi chua bat camera -->
                    <div v-show="!isScanning" class="position-relative w-100 h-100 d-flex justify-content-center align-items-center">
                      <div class="position-absolute rounded-circle"
                           style="width: 200px; height: 200px; border: 2px solid #fed7aa; background: #fff7ed;">
                      </div>
                      <div class="position-absolute rounded-circle d-flex justify-content-center align-items-center"
                           style="width: 150px; height: 150px; background: #f1f5f9;">
                        <i class="bx bx-user" style="font-size: 4rem; color: #cbd5e1;"></i>
                      </div>
                      <div class="position-absolute bottom-0 bg-dark text-white px-3 py-1 rounded-pill"
                           style="font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 8px;">
                        SẴN SÀNG QUÉT
                      </div>
                    </div>

                    <!-- Video & Canvas webcam truc tiep -->
                    <div v-show="isScanning" class="position-relative w-100 h-100 d-flex justify-content-center align-items-center">
                      <video ref="videoElement" autoplay muted playsinline
                             style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid #ea580c; transform: scaleX(-1); z-index: 10;">
                      </video>
                      <canvas ref="overlayCanvas"
                              style="position: absolute; top: 0; left: 0; width: 200px; height: 200px; border-radius: 50%; z-index: 11; pointer-events: none;">
                      </canvas>
                      <div class="scan-line-circle"></div>
                    </div>
                  </div>
                </div>

                <div class="mt-3 text-center" style="min-height: 24px;">
                  <small v-if="isModelLoading" class="status-text-anim text-warning">
                    <i class="bx bx-loader-alt bx-spin me-1"></i>
                    {{ scanStatus || 'Đang tải dữ liệu mô hình AI...' }}
                  </small>
                  <small v-else-if="isScanning" class="status-text-anim"
                         :style="{ color: isScanning ? '#ea580c' : '#64748b' }">
                    <i class="bx bx-loader-alt bx-spin me-1"></i>
                    {{ scanStatus }}
                  </small>
                  <small v-else-if="scanErrorMessage" class="text-danger fw-bold d-block text-break px-2" style="font-size: 0.8rem;">
                    <i class="bx bx-error-circle me-1"></i>
                    {{ scanErrorMessage }}
                  </small>
                  <small v-else class="text-muted" style="font-size: 0.8rem;">
                    Nhìn thẳng vào camera để AI quét dữ liệu sinh trắc học
                  </small>
                </div>

                <div v-if="scanSuccessMessage" class="alert alert-success py-2 px-3 mt-2" style="font-size: 13px;">
                  {{ scanSuccessMessage }}
                </div>

                <div class="mt-3">
                  <button v-if="!isScanning" :disabled="isModelLoading" @click="startFaceScan"
                          class="btn text-white fw-bold w-100 py-2 d-inline-flex justify-content-center align-items-center gap-2"
                          style="background-color: #0f172a; border-radius: 10px; font-size: 0.95rem;">
                    <span v-if="isModelLoading" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="bx bx-scan fs-5"></i>
                    {{ isModelLoading ? 'Đang chuẩn bị camera & AI...' : 'Bắt đầu quét khuôn mặt' }}
                  </button>
                  <button v-else @click="stopFaceScan"
                          class="btn btn-outline-danger fw-bold w-100 py-2"
                          style="border-radius: 10px;">
                    Dừng quét
                  </button>
                </div>
              </div>
            </template>
          </div>

          <!-- CARD 2: TÌNH TRẠNG HỌC PHÍ -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-primary">
                  <i class="fa-solid fa-credit-card"></i>
                </div>
                <h3 class="card-title fs-6 mb-0">Tình trạng học phí</h3>
              </div>
              <i class="fa-solid fa-circle-check text-success fs-5"></i>
            </div>

            <div class="tuition-box">
              <div class="tuition-top-line">
                <div>
                  <div class="tuition-term-name">Học phí Kỳ II (2024 - 2025)</div>
                  <div class="tuition-package-sub">Gói học tập Định kỳ THPT</div>
                </div>
                <span class="badge-paid">Đã thanh toán</span>
              </div>

              <div class="tuition-price-row mt-3">
                <span class="tuition-amount">1.125.000</span>
                <span class="tuition-currency">VND / tháng</span>
              </div>

              <div class="tuition-vietqr-status text-success mt-1">
                <i class="fa-solid fa-check me-1"></i> Đã hoàn thành qua VietQR
              </div>
            </div>

            <div class="invoice-meta-info mt-3">
              <div class="invoice-meta-row">
                <span class="text-muted">Mã hóa đơn điện tử:</span>
                <span class="fw-bold">INV - 2025 - 8288</span>
              </div>
              <div class="invoice-meta-row mt-1">
                <span class="text-muted">Hóa đơn gửi về:</span>
                <span>linhlan.nguyen@edu.vn</span>
              </div>
            </div>

            <router-link to="/thanh-toan" class="btn btn-outline-secondary btn-tuition-history w-100 mt-3">
              <i class="fa-regular fa-file-lines me-1"></i> Xem chi tiết lịch sử học phí
            </router-link>
          </div>

          <!-- CARD 3: PHỤ HUYNH ĐẠI DIỆN -->
          <div class="content-card mt-4">
            <div class="card-header-flex mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="card-title-icon text-info">
                  <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="card-title fs-6 mb-0">Phụ huynh đại diện</h3>
              </div>
            </div>

            <div class="parent-info-card">
              <div class="parent-profile-row">
                <div class="parent-avatar-circle">NV</div>
                <div>
                  <div class="parent-name">Nguyễn Văn Hùng</div>
                  <div class="parent-role">Phụ huynh học sinh (Bố)</div>
                </div>
              </div>

              <div class="parent-contacts-list mt-3">
                <div class="contact-line">
                  <i class="fa-solid fa-phone me-2 text-secondary"></i>
                  <span class="text-muted">Điện thoại:</span>
                  <strong class="ms-auto">0987.654.321</strong>
                </div>
                <div class="contact-line mt-2">
                  <i class="fa-regular fa-envelope me-2 text-secondary"></i>
                  <span class="text-muted">Email:</span>
                  <span class="ms-auto text-break">hung.nguyen@gmail.com</span>
                </div>
              </div>

              <div class="parent-linked-notification mt-3">
                <i class="fa-regular fa-bell text-success me-2 mt-1 flex-shrink-0"></i>
                <span>Đã liên kết nhận SMS & Email thông báo lịch học, kết quả kiểm tra định kỳ từ EduLink.</span>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
import * as faceapi from 'face-api.js';
import axios from 'axios';
import { API_BASE } from '../../services/api';

export default {
  name: "SmartTrialStudentProfile",
  data() {
    return {
      showUserDropdown: false,
      userAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
      isScanning: false,
      isModelLoading: false,
      isModelLoaded: false,
      scanStatus: '',
      scanErrorMessage: '',
      scanSuccessMessage: '',
      detectedFaces: 0,
      faceSaved: false,
      luong_video: null,
      vong_lap_nhan_dien: null,
      dem_thoi_gian: 0,
      giay_can_thiet: 15,
      da_xac_minh_phu: false,
      user_data: null,

      // Modals
      showPasswordModal: false,
      passwordLoading: false,
      passwordMessage: '',
      passwordSuccess: false,
      passwordForm: {
        current_password: '',
        password: '',
        password_confirmation: ''
      },

      showAvatarModal: false,
      avatarLoading: false,
      avatarMessage: '',
      avatarSuccess: false,
      avatarInputUrl: '',
      avatarPreview: ''
    };
  },
  mounted() {
    this.dongBoNguoiDungTuLocalStorage();
    document.addEventListener("click", this.closeDropdownOnClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.closeDropdownOnClickOutside);
    this.stopFaceScan();
  },
  computed: {
    da_xac_minh() {
      if (this.da_xac_minh_phu) return true;
      return !!(this.user_data && this.user_data.du_lieu_khuon_mat);
    },
    ten_nguoi_dung() {
      return this.user_data?.ho_ten || this.user_data?.name || "Nguyễn Linh Lan";
    }
  },
  methods: {
    dongBoNguoiDungTuLocalStorage() {
      try {
        const str = localStorage.getItem('user');
        this.user_data = str ? JSON.parse(str) : null;
        if (this.user_data) {
          if (this.user_data.avatar || this.user_data.hinh_anh) {
            this.userAvatar = this.user_data.avatar || this.user_data.hinh_anh;
          }
          if (this.user_data.du_lieu_khuon_mat) {
            this.da_xac_minh_phu = true;
          }
        }
      } catch (e) {
        this.user_data = null;
      }
    },
    toggleUserDropdown(e) {
      if (e) e.stopPropagation();
      this.showUserDropdown = !this.showUserDropdown;
    },
    closeDropdownOnClickOutside(e) {
      const wrapper = this.$el?.querySelector('.user-dropdown-wrapper');
      if (wrapper && !wrapper.contains(e.target)) {
        this.showUserDropdown = false;
      }
    },
    logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("role");
      localStorage.removeItem("user");
      this.$router.push("/");
    },
    handleEditProfile() {
      alert("Đang mở biểu mẫu chỉnh sửa thông tin học viên...");
    },
    handleDownloadReport() {
      alert("Đang xuất file Bảng điểm học tập PDF...");
    },
    openClassDetails(className) {
      alert("Đang mở chi tiết buổi học của lớp: " + className);
    },
    openPasswordModal() {
      this.showPasswordModal = true;
      this.passwordMessage = '';
      this.passwordSuccess = false;
      this.passwordForm = { current_password: '', password: '', password_confirmation: '' };
      this.showUserDropdown = false;
    },
    openAvatarModal() {
      this.showAvatarModal = true;
      this.avatarMessage = '';
      this.avatarSuccess = false;
      this.avatarInputUrl = '';
      this.avatarPreview = this.userAvatar;
      this.showUserDropdown = false;
    },
    onAvatarFileChange(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => {
          this.avatarPreview = ev.target.result;
          this.avatarInputUrl = ev.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    async submitChangePassword() {
      if (!this.passwordForm.current_password || !this.passwordForm.password) {
        this.passwordMessage = 'Vui lòng điền đầy đủ thông tin!';
        this.passwordSuccess = false;
        return;
      }
      if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
        this.passwordMessage = 'Xác nhận mật khẩu không khớp!';
        this.passwordSuccess = false;
        return;
      }
      this.passwordLoading = true;
      this.passwordMessage = '';
      try {
        const token = localStorage.getItem('token');
        const res = await axios.post(`${API_BASE}/hoc-vien/profile/change-password`, this.passwordForm, {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data?.status) {
          this.passwordSuccess = true;
          this.passwordMessage = res.data.message || 'Đổi mật khẩu thành công!';
          setTimeout(() => { this.showPasswordModal = false; }, 1500);
        } else {
          this.passwordSuccess = false;
          this.passwordMessage = res.data?.message || 'Đổi mật khẩu thất bại!';
        }
      } catch (err) {
        this.passwordSuccess = false;
        this.passwordMessage = err.response?.data?.message || 'Có lỗi xảy ra khi đổi mật khẩu.';
      } finally {
        this.passwordLoading = false;
      }
    },
    async submitUpdateAvatar() {
      const newAvatar = this.avatarPreview || this.avatarInputUrl;
      if (!newAvatar) {
        this.avatarMessage = 'Vui lòng chọn hoặc nhập đường dẫn ảnh!';
        this.avatarSuccess = false;
        return;
      }
      this.avatarLoading = true;
      this.avatarMessage = '';
      try {
        const token = localStorage.getItem('token');
        await axios.post(`${API_BASE}/hoc-vien/profile/update`, {
          hinh_anh: newAvatar
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.userAvatar = newAvatar;
        if (this.user_data) {
          this.user_data.avatar = newAvatar;
          this.user_data.hinh_anh = newAvatar;
          localStorage.setItem('user', JSON.stringify(this.user_data));
        }
        this.avatarSuccess = true;
        this.avatarMessage = 'Cập nhật ảnh đại diện thành công!';
        setTimeout(() => { this.showAvatarModal = false; }, 1500);
      } catch (err) {
        this.userAvatar = newAvatar;
        if (this.user_data) {
          this.user_data.avatar = newAvatar;
          localStorage.setItem('user', JSON.stringify(this.user_data));
        }
        this.avatarSuccess = true;
        this.avatarMessage = 'Đã cập nhật ảnh đại diện!';
        setTimeout(() => { this.showAvatarModal = false; }, 1500);
      } finally {
        this.avatarLoading = false;
      }
    },
    scrollToFaceId() {
      const el = this.$refs.faceIdSection || document.getElementById("face-id-section");
      if (el) {
        el.scrollIntoView({ behavior: "smooth", block: "center" });
        el.style.boxShadow = "0 0 0 3px #ea580c";
        setTimeout(() => { el.style.boxShadow = ""; }, 2000);
      }
    },
    async tai_mo_hinh_ai() {
      if (this.isModelLoaded) return true;
      this.isModelLoading = true;
      this.scanStatus = 'Đang tải dữ liệu mô hình AI (vui lòng chờ)...';
      const DUONG_DAN_MODELS = '/model';
      try {
        await Promise.all([
          faceapi.nets.tinyFaceDetector.loadFromUri(DUONG_DAN_MODELS),
          faceapi.nets.faceLandmark68Net.loadFromUri(DUONG_DAN_MODELS),
          faceapi.nets.faceRecognitionNet.loadFromUri(DUONG_DAN_MODELS)
        ]);
        this.isModelLoaded = true;
        return true;
      } catch (loi) {
        this.scanStatus = 'Lỗi tải dữ liệu AI!';
        this.scanErrorMessage = 'Không thể tải mô hình nhận diện khuôn mặt.';
        console.error('Lỗi tải mô hình:', loi);
        return false;
      } finally {
        this.isModelLoading = false;
      }
    },
    async startFaceScan() {
      this.da_xac_minh_phu = false;
      this.faceSaved = false;
      this.dem_thoi_gian = 0;
      this.scanErrorMessage = '';
      this.scanSuccessMessage = '';

      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        this.scanErrorMessage = 'Trình duyệt không hỗ trợ truy cập Camera (vui lòng sử dụng Chrome, Edge hoặc chạy trên localhost/HTTPS).';
        return;
      }

      this.isModelLoading = true;
      const tai_xong = await this.tai_mo_hinh_ai();
      if (!tai_xong) {
        this.isModelLoading = false;
        return;
      }

      this.isScanning = true;
      this.scanStatus = 'Đang mở camera...';

      try {
        this.luong_video = await navigator.mediaDevices.getUserMedia({
          video: { width: { ideal: 400 }, height: { ideal: 400 }, facingMode: 'user' }
        });

        await this.$nextTick();
        const video = this.$refs.videoElement;
        const canvas = this.$refs.overlayCanvas;

        if (video) {
          video.srcObject = this.luong_video;
          video.onloadedmetadata = async () => {
            try {
              await video.play();
              if (canvas) {
                canvas.width = video.videoWidth || 200;
                canvas.height = video.videoHeight || 200;
              }
              this.bat_dau_nhan_dien();
            } catch (playErr) {
              console.error('Lỗi play video:', playErr);
            }
          };
        }
      } catch (loi) {
        console.error('Lỗi mở camera:', loi);
        this.scanStatus = 'Lỗi truy cập camera!';
        this.scanErrorMessage = loi.name === 'NotAllowedError'
          ? 'Trình duyệt đã chặn quyền truy cập Camera. Vui lòng bấm vào biểu tượng Camera/Ổ khóa trên thanh địa chỉ và chọn Cho phép (Allow).'
          : 'Không thể kết nối đến camera: ' + (loi.message || loi.name);
        this.isScanning = false;
      } finally {
        this.isModelLoading = false;
      }
    },
    bat_dau_nhan_dien() {
      const video = this.$refs.videoElement;
      const canvas = this.$refs.overlayCanvas;
      if (!video || !canvas) return;

      const kich_thuoc_hien_thi = { width: 200, height: 200 };
      canvas.width = kich_thuoc_hien_thi.width;
      canvas.height = kich_thuoc_hien_thi.height;
      faceapi.matchDimensions(canvas, kich_thuoc_hien_thi);

      this.scanStatus = 'Đang tìm kiếm khuôn mặt...';

      this.vong_lap_nhan_dien = setInterval(async () => {
        if (!this.isScanning || !video || video.paused || video.ended) return;

        try {
          const ket_qua = await faceapi.detectAllFaces(
            video,
            new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.35 })
          ).withFaceLandmarks().withFaceDescriptors();

          this.detectedFaces = ket_qua.length;

          const ctx = canvas.getContext('2d');
          ctx.clearRect(0, 0, canvas.width, canvas.height);

          if (ket_qua.length === 1) {
            this.dem_thoi_gian++;
            const phan_tram = Math.min(Math.round((this.dem_thoi_gian / this.giay_can_thiet) * 100), 100);
            this.scanStatus = `Đang phân tích sinh trắc học... ${phan_tram}%`;

            if (this.dem_thoi_gian >= this.giay_can_thiet && !this.faceSaved) {
              this.faceSaved = true;
              this.scanStatus = 'Xác nhận thực thể sống thành công!';
              const vec_to = Array.from(ket_qua[0].descriptor);
              this.gui_du_lieu_len_laravel(vec_to);
            }
          } else {
            this.dem_thoi_gian = 0;
            if (ket_qua.length === 0) {
              this.scanStatus = 'Vui lòng nhìn thẳng vào camera...';
            } else {
              this.scanStatus = 'Cảnh báo: Phát hiện quá nhiều người!';
            }
          }
        } catch (e) {
          console.error('Lỗi nhận diện:', e);
        }
      }, 200);
    },
    async gui_du_lieu_len_laravel(mang_so) {
      try {
        this.scanStatus = 'Đang lưu vào hệ thống...';
        clearInterval(this.vong_lap_nhan_dien);

        const token = localStorage.getItem('token');
        const role = localStorage.getItem('role') || 'hoc_vien';
        const prefix = role === 'giao_vien' ? 'giao-vien' : 'hoc-vien';

        const res = await axios.post(`${API_BASE}/${prefix}/xac-thuc-khuon-mat`, {
          id: this.user_data?.id,
          du_lieu_khuon_mat: JSON.stringify(mang_so)
        }, {
          headers: token ? { Authorization: `Bearer ${token}` } : {}
        });

        if (res.data.status || res.data.success) {
          this.scanStatus = 'Cập nhật thành công!';
          this.scanSuccessMessage = 'Đăng ký Face ID thành công!';
          
          let user = JSON.parse(localStorage.getItem('user') || '{}');
          user.du_lieu_khuon_mat = JSON.stringify(mang_so);
          localStorage.setItem('user', JSON.stringify(user));
          this.user_data = user;
          this.da_xac_minh_phu = true;

          setTimeout(() => {
            this.stopFaceScan();
          }, 1500);
        }
      } catch (loi) {
        if (loi.response && (loi.response.status === 400 || loi.response.data?.message)) {
          const msg = loi.response.data.message || 'Lỗi dữ liệu sinh trắc học';
          this.scanStatus = msg;
          this.scanErrorMessage = msg;
        } else {
          this.scanStatus = 'Lỗi kết nối server!';
          this.scanErrorMessage = 'Có lỗi xảy ra, vui lòng thử lại.';
        }
        this.faceSaved = false;
        this.isScanning = false;
        console.error(loi);
      }
    },
    stopFaceScan() {
      this.isScanning = false;
      this.scanStatus = '';
      this.detectedFaces = 0;
      this.faceSaved = false;

      if (this.vong_lap_nhan_dien) {
        clearInterval(this.vong_lap_nhan_dien);
        this.vong_lap_nhan_dien = null;
      }

      if (this.luong_video) {
        this.luong_video.getTracks().forEach(track => track.stop());
        this.luong_video = null;
      }

      const video = this.$refs.videoElement;
      const canvas = this.$refs.overlayCanvas;
      if (video) {
        video.style.display = 'none';
        video.srcObject = null;
      }
      if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        canvas.style.display = 'none';
      }
    }
  }
};
</script>

<style scoped>
.student-profile-page {
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
  padding-bottom: 24px;
  border-bottom: 1px solid #f1f5f9;
}

.profile-info-left {
  display: flex;
  align-items: center;
  gap: 22px;
}

.student-avatar-box {
  position: relative;
  width: 96px;
  height: 96px;
  flex-shrink: 0;
}

.student-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 18px;
  object-fit: cover;
}

.faceid-verified-icon {
  position: absolute;
  bottom: -4px;
  right: -4px;
  background-color: #ffffff;
  color: #16a34a;
  font-size: 18px;
  border-radius: 50%;
  line-height: 1;
}

.student-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}

.student-name {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0;
  letter-spacing: -0.4px;
}

.badge-faceid-verified {
  background-color: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #16a34a;
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 12px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
}

.green-dot {
  width: 7px;
  height: 7px;
  background-color: #16a34a;
  border-radius: 50%;
  display: inline-block;
}

.student-sub-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13.5px;
  color: #475569;
  flex-wrap: wrap;
}

.badge-student-id {
  background-color: #eff6ff;
  color: #0060d2;
  font-weight: 700;
  font-size: 12px;
  padding: 2px 10px;
  border-radius: 6px;
}

.meta-dot {
  color: #cbd5e1;
}

.profile-top-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-action-outline {
  height: 40px;
  padding: 0 16px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 8px;
  background-color: #ffffff;
  border: 1px solid #d8dee4;
  color: #334155;
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-action-outline:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
  color: #0f172a;
}

.btn-download-report {
  height: 40px;
  padding: 0 18px;
  font-size: 13px;
  font-weight: 700;
  border-radius: 8px;
  background-color: #0060d2;
  border: none;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.2);
}

.btn-download-report:hover {
  background-color: #004fb0;
}

/* Stats Highlights Bar */
.profile-stats-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  padding-top: 22px;
}

.stat-highlight-item {
  display: flex;
  align-items: center;
  gap: 14px;
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

.icon-blue { background-color: #eff6ff; color: #0060d2; }
.icon-indigo { background-color: #e0e7ff; color: #4338ca; }
.icon-green { background-color: #f0fdf4; color: #16a34a; }
.icon-orange { background-color: #fff7ed; color: #ea580c; }

.stat-top-line {
  line-height: 1.1;
}

.stat-num {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
}

.stat-unit {
  font-size: 12px;
  color: #64748b;
  margin-left: 2px;
}

.stat-label {
  font-size: 12.5px;
  color: #475569;
  font-weight: 500;
  margin-top: 2px;
}

.stat-sub {
  font-size: 11.5px;
  margin-top: 2px;
}

/* 3. Bố cục 2 cột chính */
.profile-layout {
  display: flex;
  gap: 28px;
  align-items: flex-start;
}

.profile-left-column {
  flex: 1 1 64%;
}

.profile-right-column {
  flex: 1 1 36%;
}

/* Content Card chung */
.content-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.card-header-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.card-title {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.3px;
}

.card-sub-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.card-title-icon {
  font-size: 18px;
}

.badge-exam-focus {
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 50px;
}

/* Khung mục tiêu */
.goal-highlight-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 16px 20px;
  margin-top: 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.goal-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.goal-flag-icon {
  font-size: 20px;
}

.goal-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 600;
}

.goal-desc {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
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

/* Progress bar */
.overall-progress-wrap {
  margin-top: 16px;
}

.progress-labels-row {
  display: flex;
  justify-content: space-between;
  font-size: 12.5px;
  margin-bottom: 6px;
}

.progress-text-label {
  color: #475569;
  font-weight: 600;
}

.progress-val-label {
  color: #0f172a;
  font-weight: 700;
}

.custom-progress-bar {
  height: 8px;
  border-radius: 50px;
  background-color: #f1f5f9;
}

/* 3 Môn học Progress Grid */
.subjects-progress-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.subject-progress-card {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 14px 16px;
}

.subj-header {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 6px;
}

.subj-name {
  font-weight: 700;
  color: #0f172a;
}

.sub-progress {
  height: 6px;
  border-radius: 50px;
  background-color: #e2e8f0;
  margin-bottom: 6px;
}

.subj-note {
  font-size: 11.5px;
  color: #64748b;
}

.text-warning-dark {
  color: #b45309;
}

/* Enrolled classes */
.view-history-link {
  font-size: 12.5px;
  color: #0060d2;
  text-decoration: none;
  font-weight: 600;
}

.enrolled-classes-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.class-enroll-item {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 16px 20px;
}

.class-enroll-item.highlight-border-blue {
  border-left: 4px solid #0060d2;
  background-color: #fbfdff;
}

.class-item-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.class-pills {
  display: flex;
  gap: 8px;
}

.pill-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 50px;
}

.pill-blue { background-color: #eff6ff; color: #0060d2; }
.pill-purple { background-color: #f3e8ff; color: #9333ea; }
.pill-gray { background-color: #f1f5f9; color: #475569; }

.pill-green-outline {
  border: 1px solid #86efac;
  color: #16a34a;
  background-color: #f0fdf4;
}

.pill-gray-outline {
  border: 1px solid #cbd5e1;
  color: #64748b;
  background-color: #ffffff;
}

.class-upcoming-badge {
  font-size: 12px;
  font-weight: 600;
  color: #ea580c;
}

.class-item-main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
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
}

.class-item-buttons {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.btn-class-details {
  font-size: 12px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 8px;
}

.btn-enter-faceid {
  font-size: 12px;
  font-weight: 700;
  padding: 6px 16px;
  border-radius: 8px;
  background-color: #0060d2;
  border: none;
  text-decoration: none;
  color: #ffffff;
}

.btn-enter-faceid:hover {
  background-color: #004fb0;
}

/* Khung giờ rảnh đã đăng ký */
.btn-update-slots {
  font-size: 12px;
  font-weight: 600;
  border-radius: 8px;
}

.week-free-slots-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 8px;
}

.day-slot-card {
  border-radius: 10px;
  padding: 12px 6px;
  text-align: center;
  border: 1px solid #e2e8f0;
}

.day-label {
  font-size: 12px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.slot-time-text {
  font-size: 11px;
}

.slot-booked {
  background-color: #eff6ff;
  border-color: #bfdbfe;
  color: #0060d2;
  font-weight: 600;
}

.slot-busy {
  background-color: #f8fafc;
  color: #94a3b8;
}

.slot-available {
  background-color: #f0fdf4;
  border-color: #bbf7d0;
}

.slots-legend {
  display: flex;
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

.dot-booked { background-color: #0060d2; }
.dot-available { background-color: #16a34a; }

/* Nhận xét giáo viên */
.teacher-feedback-card {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 20px;
}

.feedback-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.teacher-reviewer-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.teacher-reviewer-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
}

.teacher-reviewer-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.teacher-reviewer-role {
  font-size: 11.5px;
  color: #64748b;
}

.feedback-stars {
  font-size: 11px;
}

.stars-num {
  font-size: 12px;
  font-weight: 700;
  color: #0f172a;
}

.feedback-quote {
  font-size: 13.5px;
  color: #334155;
  line-height: 1.6;
  margin-bottom: 10px;
  font-style: italic;
}

.feedback-date {
  font-size: 11.5px;
}

/* RIGHT COLUMN */
/* Face ID status box */
.faceid-status-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 20px;
  text-align: center;
}

.faceid-icon-circle {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}

.faceid-status-title {
  font-size: 15px;
  font-weight: 800;
  color: #16a34a;
  margin-bottom: 2px;
}

.faceid-match-rate {
  font-size: 12px;
  color: #475569;
  margin-bottom: 10px;
}

.faceid-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.45;
  margin-bottom: 14px;
}

.btn-rescan-face {
  font-size: 12.5px;
  font-weight: 600;
  border-radius: 8px;
  padding: 7px 14px;
}

.device-specs-list {
  font-size: 12.5px;
}

.spec-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.spec-label {
  color: #64748b;
}

/* Tuition box */
.tuition-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 16px 18px;
}

.tuition-top-line {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.tuition-term-name {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.tuition-package-sub {
  font-size: 11.5px;
  color: #64748b;
}

.badge-paid {
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 50px;
}

.tuition-price-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.tuition-amount {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

.tuition-currency {
  font-size: 12px;
  color: #64748b;
}

.tuition-vietqr-status {
  font-size: 12px;
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
  border-radius: 8px;
  padding: 7px 14px;
}

/* Parent info */
.parent-profile-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.parent-avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 14px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
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
  font-size: 12.5px;
}

.contact-line {
  display: flex;
  align-items: center;
}

.parent-linked-notification {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 10px;
  padding: 10px 12px;
  display: flex;
  align-items: flex-start;
  font-size: 11.5px;
  color: #475569;
  line-height: 1.45;
}

/* Responsive */
@media (max-width: 991px) {
  .profile-layout {
    flex-direction: column;
  }
  .profile-left-column,
  .profile-right-column {
    flex: 1 1 100%;
    width: 100%;
  }
  .profile-top-row {
    flex-direction: column;
    align-items: flex-start;
  }
  .profile-stats-bar {
    grid-template-columns: repeat(2, 1fr);
  }
  .subjects-progress-grid {
    grid-template-columns: 1fr;
  }
  .week-free-slots-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* ===== FACE ID UPLOAD SECTION ===== */
.faceid-current-photo-wrap {
  position: relative;
  display: flex;
  justify-content: center;
  margin-bottom: 4px;
}

.faceid-current-img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e2e8f0;
}

.faceid-verified-badge {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  background: #dcfce7;
  color: #16a34a;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 50px;
  white-space: nowrap;
}

.faceid-pending-badge {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  background: #fff7ed;
  color: #ea580c;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 50px;
  white-space: nowrap;
}

.faceid-upload-area {
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  padding: 20px 12px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
  background: #f8fafc;
}

.faceid-upload-area:hover {
  border-color: #0060d2;
  background: #eff6ff;
}

.faceid-upload-icon {
  font-size: 28px;
  color: #94a3b8;
  margin-bottom: 8px;
  display: block;
}

.faceid-upload-text {
  font-size: 13px;
  color: #475569;
  margin-bottom: 2px;
}

.faceid-upload-hint {
  font-size: 11.5px;
  color: #94a3b8;
  margin-bottom: 0;
}

/* Thanh laser quet chuyen dong doc cho khuon mat */
.scan-line-circle {
  position: absolute;
  width: 180px;
  height: 3px;
  background: linear-gradient(to right, transparent, #ea580c, transparent);
  box-shadow: 0 0 12px #ea580c;
  z-index: 12;
  animation: scan-vertical 2s ease-in-out infinite;
}

.status-text-anim {
  font-weight: bold;
  color: #ea580c;
  transition: all 0.3s ease;
}

@keyframes scan-vertical {
  0% {
    top: 10%;
    opacity: 0;
  }
  50% {
    top: 50%;
    opacity: 1;
  }
  90% {
    top: 90%;
    opacity: 0;
  }
  100% {
    top: 10%;
    opacity: 0;
  }
}

.badge-faceid-unverified {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  color: #854d0e;
  background: #fef9c3;
  padding: 4px 10px;
  border-radius: 20px;
  font-weight: 600;
}

.badge-faceid-verified {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  color: #15803d;
  background: #dcfce7;
  padding: 4px 10px;
  border-radius: 20px;
  font-weight: 600;
}
</style>
