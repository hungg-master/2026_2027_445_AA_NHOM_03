<template>
  <div class="teacher-profile-page">
    <div class="profile-container">
      <!-- 1. BREADCRUMB -->
      <nav class="breadcrumb-nav">
        <i class="fa-solid fa-house me-1"></i>
        <router-link to="/my-schedule" class="crumb-link">Trang chủ</router-link>
        <span class="crumb-sep">›</span>
        <span class="crumb-link">Tìm gia sư / giáo viên</span>
        <span class="crumb-sep">›</span>
        <span class="crumb-active">Hồ sơ giáo viên: TS. Nguyễn Minh Triết (Toán học & KH Máy tính)</span>
      </nav>

      <!-- 2. TOP PROFILE BANNER (THẺ GIẢNG VIÊN) -->
      <div class="profile-header-card">
        <div class="profile-top-row">
          <!-- Left: Avatar & Tên, Chức vị, Đánh giá -->
          <div class="profile-info-left">
            <div class="teacher-avatar-box">
              <img
                src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=240&q=80"
                alt="TS. Nguyễn Minh Triết"
                class="teacher-avatar-img"
              />
              <div class="verified-badge-icon" title="Giảng viên đã xác thực">
                <i class="fa-solid fa-circle-check"></i>
              </div>
            </div>

            <div class="teacher-meta-details">
              <div class="teacher-name-row">
                <h1 class="teacher-name">TS. Nguyễn Minh Triết</h1>
                <span class="badge-verified">
                  <i class="fa-solid fa-shield-halved me-1"></i> Hồ sơ đã kiểm duyệt
                </span>
              </div>

              <div class="teacher-workplace">
                <i class="fa-solid fa-graduation-cap text-primary me-1"></i>
                <span>Giảng viên Khoa Toán - Tin học, Trường ĐH Bách Khoa TP.HCM</span>
              </div>

              <div class="teacher-trust-badges">
                <span class="trust-item rating-text">
                  <i class="fa-solid fa-star text-warning me-1"></i>
                  <strong>4.9</strong> (128 học viên đánh giá)
                </span>
                <span class="trust-sep">•</span>
                <span class="trust-item text-success">
                  <i class="fa-solid fa-certificate me-1"></i>
                  100% bằng cấp chính quy
                </span>
              </div>
            </div>
          </div>

          <!-- Right: Nút hành động nhanh -->
          <div class="profile-top-actions">
            <button class="btn btn-primary btn-book-now" @click="scrollToBooking">
              <i class="fa-solid fa-calendar-check me-2"></i> Đặt lịch học ngay
            </button>
            <button class="btn btn-outline-secondary btn-chat-now" @click="handleChat">
              <i class="fa-regular fa-comment-dots me-1"></i> Nhắn tin
            </button>
            <button
              :class="['btn btn-icon-like', { 'liked': isLiked }]"
              @click="toggleLike"
              :title="isLiked ? 'Đã lưu yêu thích' : 'Lưu yêu thích'"
            >
              <i :class="['fa-heart', isLiked ? 'fa-solid text-danger' : 'fa-regular']"></i>
            </button>
          </div>
        </div>

        <!-- Dải 4 chỉ số nổi bật (Highlights row) -->
        <div class="profile-stats-bar">
          <div class="stat-highlight-item">
            <div class="stat-icon-wrap icon-blue">
              <i class="fa-solid fa-briefcase"></i>
            </div>
            <div>
              <div class="stat-highlight-num">8+ năm</div>
              <div class="stat-highlight-label">Kinh nghiệm giảng dạy</div>
            </div>
          </div>

          <div class="stat-highlight-item">
            <div class="stat-icon-wrap icon-green">
              <i class="fa-solid fa-users"></i>
            </div>
            <div>
              <div class="stat-highlight-num">350+</div>
              <div class="stat-highlight-label">Học sinh theo học</div>
            </div>
          </div>

          <div class="stat-highlight-item">
            <div class="stat-icon-wrap icon-orange">
              <i class="fa-solid fa-bullseye"></i>
            </div>
            <div>
              <div class="stat-highlight-num">96%</div>
              <div class="stat-highlight-label">Tỷ lệ đậu kỳ thi mục tiêu</div>
            </div>
          </div>

          <div class="stat-highlight-item">
            <div class="stat-icon-wrap icon-purple">
              <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
              <div class="stat-highlight-num">&lt; 15 phút</div>
              <div class="stat-highlight-label">Tốc độ phản hồi</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. BỐ CỤC 2 CỘT NỘI DUNG -->
      <div class="profile-layout">
        <!-- CỘT TRÁI: THÔNG TIN CHI TIẾT & LỊCH DẠY -->
        <div class="profile-main-column">
          <!-- THẺ 1: GIỚI THIỆU & PHƯƠNG PHÁP GIẢNG DẠY -->
          <div class="section-card">
            <h2 class="section-heading">
              <span class="heading-accent"></span> Giới thiệu & Phương pháp giảng dạy
            </h2>
            <div class="bio-paragraphs">
              <p>
                Chào quý phụ huynh và các em học sinh! Tôi là Nguyễn Minh Triết, hiện là Tiến sĩ và Giảng viên
                bộ môn Giải tích & Khoa học Dữ liệu. Với hơn 8 năm đồng hành cùng học sinh từ cấp <strong>THPT
                chuẩn bị thi Đánh giá năng lực, Tốt nghiệp THPT Quốc gia</strong>, đến sinh viên khối ngành kỹ thuật,
                triết lý dạy học của tôi tập trung vào <strong>bản chất toán học và tư duy giải quyết vấn đề</strong> thay vì học
                vẹt công thức.
              </p>
              <p>
                Mỗi học sinh đều sở hữu một lộ trình tư duy khác nhau. Khi theo học cùng tôi trên nền tảng
                EduLink, học sinh được phân tích lỗ hổng kiến thức qua các bài kiểm tra logic ban đầu, sau đó
                xây dựng kế hoạch bù đắp từng tuần. Hệ thống học tập kết hợp công cụ trực quan hóa đồ thị
                Geogebra và bài tập lập trình mô phỏng thực tế.
              </p>
            </div>

            <!-- 3 Khối tính năng phương pháp -->
            <div class="teaching-features-grid">
              <div class="feature-box">
                <div class="feature-icon text-primary">
                  <i class="fa-solid fa-compass-drafting"></i>
                </div>
                <h3 class="feature-title">Tư duy mô hình hóa</h3>
                <p class="feature-desc">Giải toán bằng trực giác không gian và thuật toán tối ưu.</p>
              </div>

              <div class="feature-box">
                <div class="feature-icon text-success">
                  <i class="fa-solid fa-user-gear"></i>
                </div>
                <h3 class="feature-title">Cá nhân hóa tiến độ</h3>
                <p class="feature-desc">Báo cáo chỉ số tiến bộ sau mỗi 4 buổi học cho phụ huynh.</p>
              </div>

              <div class="feature-box">
                <div class="feature-icon text-danger">
                  <i class="fa-solid fa-video"></i>
                </div>
                <h3 class="feature-title">Số hóa bài giảng</h3>
                <p class="feature-desc">Truy cập trọn đời kho video ghi lại buổi học trên EduLink.</p>
              </div>
            </div>
          </div>

          <!-- THẺ 2: BẰNG CẤP & CHỨNG NHẬN NĂNG LỰC -->
          <div class="section-card mt-4">
            <h2 class="section-heading">
              <span class="heading-accent"></span> Bằng cấp & Chứng nhận năng lực
            </h2>

            <div class="credentials-grid">
              <!-- Bằng 1 -->
              <div class="credential-item">
                <div class="cred-icon-box cred-blue">
                  <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="cred-info">
                  <h4 class="cred-name">Bằng Tiến sĩ Toán học Ứng dụng</h4>
                  <p class="cred-school">Đại học Bách Khoa TP.HCM (2018)</p>
                  <span class="cred-status text-primary">
                    <i class="fa-solid fa-check me-1"></i> Đã xác thực hồ sơ gốc
                  </span>
                </div>
              </div>

              <!-- Bằng 2 -->
              <div class="credential-item">
                <div class="cred-icon-box cred-green">
                  <i class="fa-solid fa-certificate"></i>
                </div>
                <div class="cred-info">
                  <h4 class="cred-name">Chứng chỉ Sư phạm Quốc tế TESOL</h4>
                  <p class="cred-school">Australian International College (2020)</p>
                  <span class="cred-status text-success">
                    <i class="fa-solid fa-check me-1"></i> Đã kiểm định nghiệp vụ
                  </span>
                </div>
              </div>

              <!-- Bằng 3 -->
              <div class="credential-item">
                <div class="cred-icon-box cred-orange">
                  <i class="fa-solid fa-award"></i>
                </div>
                <div class="cred-info">
                  <h4 class="cred-name">Giảng viên Xuất sắc Cấp Viện</h4>
                  <p class="cred-school">Khen thưởng thi đua năm 2022 & 2023</p>
                  <span class="cred-status text-warning-dark">
                    <i class="fa-solid fa-trophy me-1"></i> Danh hiệu thường niên
                  </span>
                </div>
              </div>

              <!-- Bằng 4 -->
              <div class="credential-item">
                <div class="cred-icon-box cred-gray">
                  <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <div class="cred-info">
                  <h4 class="cred-name">Tác giả Sách Tham khảo Toán 12</h4>
                  <p class="cred-school">Nhà xuất bản Đại học Quốc gia</p>
                  <span class="cred-status text-secondary">
                    <i class="fa-solid fa-book-open me-1"></i> Giáo trình lưu hành
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- THẺ 3: CÁC HÌNH THỨC LỚP HỌC -->
          <div class="section-card mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h2 class="section-heading mb-0">
                <span class="heading-accent"></span> Các hình thức lớp học
              </h2>
              <span class="text-muted fs-7">Thời lượng chuẩn: 90 phút / ca</span>
            </div>

            <div class="class-types-grid">
              <!-- Gói 1: Kèm riêng 1:1 -->
              <div
                :class="['class-package-card', { 'selected-pkg': selectedPackage.id === '1on1' }]"
                @click="selectPackage('1on1')"
              >
                <span class="pkg-badge badge-blue">Kèm riêng 1:1</span>
                <h3 class="pkg-title">Cá nhân hóa sâu 1 kèm 1</h3>
                <p class="pkg-desc">
                  Cam kết điểm số, chỉnh sửa chi tiết từng bài làm, xác thực FaceID bảo đảm học tập tập trung tuyệt đối.
                </p>
                <div class="pkg-price-wrap">
                  <span class="pkg-price">350.000đ</span>
                  <span class="pkg-unit">/ buổi</span>
                </div>
                <button
                  :class="['btn w-100', selectedPackage.id === '1on1' ? 'btn-primary' : 'btn-outline-primary']"
                >
                  {{ selectedPackage.id === '1on1' ? 'Đang chọn lớp 1:1' : 'Chọn lớp 1:1' }}
                </button>
              </div>

              <!-- Gói 2: Nhóm nhỏ (Phổ biến nhất) -->
              <div
                :class="['class-package-card popular-package', { 'selected-pkg': selectedPackage.id === 'group' }]"
                @click="selectPackage('group')"
              >
                <div class="popular-ribbon">PHỔ BIẾN NHẤT</div>
                <span class="pkg-badge badge-green">Nhóm nhỏ (3-5 bạn)</span>
                <h3 class="pkg-title">Học nhóm tương tác cao</h3>
                <p class="pkg-desc">
                  Tạo môi trường thảo luận tranh biện logic, bài tập nhóm tăng phản xạ giải toán cạnh tranh tích cực.
                </p>
                <div class="pkg-price-wrap">
                  <span class="pkg-price text-success">180.000đ</span>
                  <span class="pkg-unit">/ học viên</span>
                </div>
                <button
                  :class="['btn w-100', selectedPackage.id === 'group' ? 'btn-success' : 'btn-outline-success']"
                >
                  {{ selectedPackage.id === 'group' ? 'Đang chọn nhóm nhỏ' : 'Chọn nhóm nhỏ' }}
                </button>
              </div>

              <!-- Gói 3: Lớp đại trà -->
              <div
                :class="['class-package-card', { 'selected-pkg': selectedPackage.id === 'mass' }]"
                @click="selectPackage('mass')"
              >
                <span class="pkg-badge badge-gray">Lớp đại trà</span>
                <h3 class="pkg-title">Chuyên đề trọng tâm</h3>
                <p class="pkg-desc">
                  Hệ thống hóa toàn bộ kiến thức kỳ thi, phương pháp bấm máy tính và kỹ thuật xử lý đề tốc độ cao.
                </p>
                <div class="pkg-price-wrap">
                  <span class="pkg-price">90.000đ</span>
                  <span class="pkg-unit">/ học viên</span>
                </div>
                <button
                  :class="['btn w-100', selectedPackage.id === 'mass' ? 'btn-secondary' : 'btn-outline-secondary']"
                >
                  {{ selectedPackage.id === 'mass' ? 'Đang chọn đại trà' : 'Chọn đại trà' }}
                </button>
              </div>
            </div>
          </div>

          <!-- THẺ 4: LỊCH DẠY & KHUNG GIỜ TRỐNG -->
          <div class="section-card mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h2 class="section-heading mb-0">
                <span class="heading-accent"></span> Lịch dạy & Khung giờ trống tuần này
              </h2>
              <div class="schedule-legend">
                <span class="legend-item"><span class="dot-blue"></span> Trống (Có thể đặt)</span>
                <span class="legend-item"><span class="dot-gray"></span> Đã kín lịch</span>
              </div>
            </div>

            <div class="schedule-matrix-wrapper">
              <table class="schedule-matrix-table">
                <thead>
                  <tr>
                    <th class="matrix-header-col">Ca học</th>
                    <th>T2</th>
                    <th>T3</th>
                    <th>T4</th>
                    <th>T5</th>
                    <th>T6</th>
                    <th>T7</th>
                    <th>CN</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Ca Sáng -->
                  <tr>
                    <td class="shift-name">Sáng (08:00 - 09:30)</td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T2', '08:00') }]" @click="pickSlot('Thứ 2', 'Sáng 08:00')">08:00</button></td>
                    <td><span class="slot-closed">Kín</span></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T4', '08:00') }]" @click="pickSlot('Thứ 4', 'Sáng 08:00')">08:00</button></td>
                    <td><span class="slot-closed">Kín</span></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T6', '08:00') }]" @click="pickSlot('Thứ 6', 'Sáng 08:00')">08:00</button></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T7', '08:00') }]" @click="pickSlot('Thứ 7', 'Sáng 08:00')">08:00</button></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('CN', '08:00') }]" @click="pickSlot('Chủ Nhật', 'Sáng 08:00')">08:00</button></td>
                  </tr>

                  <!-- Ca Chiều -->
                  <tr>
                    <td class="shift-name">Chiều (14:30 - 16:00)</td>
                    <td><span class="slot-closed">Kín</span></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T3', '14:30') }]" @click="pickSlot('Thứ 3', 'Chiều 14:30')">14:30</button></td>
                    <td><span class="slot-closed">Kín</span></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T5', '14:30') }]" @click="pickSlot('Thứ 5', 'Chiều 14:30')">14:30</button></td>
                    <td><span class="slot-closed">Kín</span></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T7', '14:30') }]" @click="pickSlot('Thứ 7', 'Chiều 14:30')">14:30</button></td>
                    <td><span class="slot-closed">Kín</span></td>
                  </tr>

                  <!-- Ca Tối -->
                  <tr>
                    <td class="shift-name">Tối (19:30 - 21:00)</td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T2', '19:30') }]" @click="pickSlot('Thứ 2', 'Tối 19:30')">19:30</button></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T3', '19:30') }]" @click="pickSlot('Thứ 3', 'Tối 19:30')">19:30</button></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T4', '19:30') }]" @click="pickSlot('Thứ 4', 'Tối 19:30')">19:30</button></td>
                    <td><span class="slot-closed">Kín</span></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('T6', '19:30') }]" @click="pickSlot('Thứ 6', 'Tối 19:30')">19:30</button></td>
                    <td><span class="slot-closed">Kín</span></td>
                    <td><button :class="['slot-btn', { 'selected': isSlotActive('CN', '19:30') }]" @click="pickSlot('Chủ Nhật', 'Tối 19:30')">19:30</button></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="schedule-hint-note">
              <i class="fa-solid fa-circle-info text-primary me-2"></i>
              <span>Bấm vào khung giờ xanh để tự động điền vào phiếu đăng ký bên phải.</span>
            </div>
          </div>

          <!-- THẺ 5: ĐÁNH GIÁ TỪ PHỤ HUYNH & HỌC SINH -->
          <div class="section-card mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h2 class="section-heading mb-0">
                <span class="heading-accent"></span> Đánh giá từ phụ huynh & học sinh
              </h2>
              <div class="overall-rating-badge">
                <i class="fa-solid fa-star text-warning me-1"></i>
                <strong>4.9 / 5.0</strong>
              </div>
            </div>

            <div class="reviews-list">
              <!-- Review 1 -->
              <div class="review-box">
                <div class="review-header">
                  <div class="reviewer-left">
                    <div class="reviewer-avatar avatar-blue">TL</div>
                    <div>
                      <div class="reviewer-name">Phụ huynh Trần Lan Hương</div>
                      <div class="reviewer-course">Con học lớp 12 chuyên Tin – Khóa Luyện thi ĐGNL</div>
                    </div>
                  </div>
                  <div class="review-date">3 ngày trước</div>
                </div>

                <div class="review-stars">
                  <i class="fa-solid fa-star text-warning" v-for="i in 5" :key="i"></i>
                </div>

                <p class="review-text">
                  "Thầy Triết dạy <em>rất có tâm và truyền cảm hứng</em>. Con tôi trước đây rất sợ phần hình học không gian giải tích,
                  nhưng sau 2 tháng học <strong>kèm 1:1 cùng thầy</strong>, điểm kiểm tra thử ĐGNL đã tăng từ 65 lên 92 điểm. Hệ thống
                  điểm danh FaceID của EduLink cũng giúp gia đình rất an tâm về giờ giấc học tập của cháu."
                </p>
              </div>

              <!-- Review 2 -->
              <div class="review-box">
                <div class="review-header">
                  <div class="reviewer-left">
                    <div class="reviewer-avatar avatar-green">ĐN</div>
                    <div>
                      <div class="reviewer-name">Đặng Nam Phong (Học sinh lớp 11)</div>
                      <div class="reviewer-course">Nhóm học thuật toán & Giải tích nâng cao</div>
                    </div>
                  </div>
                  <div class="review-date">1 tuần trước</div>
                </div>

                <div class="review-stars">
                  <i class="fa-solid fa-star text-warning" v-for="i in 5" :key="i"></i>
                </div>

                <p class="review-text">
                  "Bài giảng của thầy không bị gò bó, thấy liên hệ toán cao cấp với mô hình trí tuệ nhân tạo khiến tụi em cực
                  kỳ hào hứng. Học nhóm 5 bạn <strong>vừa tiết kiệm học phí mà tương tác hỏi đáp không bị ngợp</strong>. Rất khuyên các
                  bạn nên đăng ký ca học của thầy!"
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- CỘT PHẢI: PHIẾU ĐĂNG KÝ STICKY SIDEBAR -->
        <aside class="profile-sidebar-column">
          <div class="sticky-sidebar-wrapper" id="bookingCard">
            <!-- 1. PHIẾU ĐĂNG KÝ CA HỌC -->
            <div class="booking-card">
              <div class="booking-card-header">
                <h3 class="booking-card-title">
                  <i class="fa-regular fa-calendar-plus me-1 text-primary"></i> Đăng ký ca học
                </h3>
                <span class="badge-accepting">Nhận lớp mới</span>
              </div>

              <form @submit.prevent="handleBooking">
                <!-- 1. Chọn loại hình kèm -->
                <div class="form-group mb-3">
                  <label class="booking-label">1. Chọn loại hình kèm</label>
                  <select v-model="selectedPackageId" class="form-select custom-select" @change="onPackageSelectChange">
                    <option value="1on1">Lớp kèm 1:1 trực tuyến (350.000đ)</option>
                    <option value="group">Học nhóm nhỏ 3-5 bạn (180.000đ)</option>
                    <option value="mass">Lớp chuyên đề đại trà (90.000đ)</option>
                  </select>
                </div>

                <!-- 2. Hình thức học tập -->
                <div class="form-group mb-3">
                  <label class="booking-label">2. Hình thức học tập</label>
                  <div class="radio-options-row">
                    <label class="custom-radio-label">
                      <input type="radio" v-model="learningMode" value="online" class="form-check-input me-1" />
                      <span>Online (FaceID)</span>
                    </label>
                    <label class="custom-radio-label">
                      <input type="radio" v-model="learningMode" value="offline" class="form-check-input me-1" />
                      <span>Trực tiếp tại cơ sở</span>
                    </label>
                  </div>
                </div>

                <!-- 3. Khung giờ đăng ký -->
                <div class="form-group mb-3">
                  <label class="booking-label">3. Khung giờ đăng ký</label>
                  <div class="selected-slot-box">
                    <i class="fa-regular fa-clock me-2 text-primary"></i>
                    <span class="slot-display-text">{{ currentSelectedSlotText }}</span>
                  </div>
                </div>

                <!-- 4. Thông tin học viên -->
                <div class="form-group mb-3">
                  <label class="booking-label">4. Thông tin học viên</label>
                  <input
                    type="text"
                    v-model="bookingForm.studentName"
                    class="form-control mb-2 booking-input"
                    placeholder="Họ tên học sinh"
                    required
                  />
                  <input
                    type="tel"
                    v-model="bookingForm.phone"
                    class="form-control booking-input"
                    placeholder="Số điện thoại phụ huynh / Zalo"
                    required
                  />
                </div>

                <!-- Bảng tính học phí -->
                <div class="fee-calculation-box">
                  <div class="fee-row">
                    <span class="text-muted">Học phí ca 90 phút:</span>
                    <span class="fw-bold">{{ formatVND(selectedPackage.fee) }}</span>
                  </div>
                  <div class="fee-row">
                    <span class="text-muted">Phí nền tảng EduLink:</span>
                    <span class="text-success fw-medium">Miễn phí 100%</span>
                  </div>
                  <hr class="fee-divider" />
                  <div class="fee-total-row">
                    <span class="total-label">Tổng thanh toán:</span>
                    <span class="total-price">{{ formatVND(selectedPackage.fee) }}</span>
                  </div>
                </div>

                <!-- Nút xác nhận đặt lịch -->
                <button type="submit" class="btn btn-primary btn-confirm-booking w-100">
                  <i class="fa-solid fa-circle-check me-2"></i> Xác nhận đặt lịch & Giữ chỗ
                </button>
              </form>
            </div>

            <!-- 2. CAM KẾT ĐỘC QUYỀN TỪ EDULINK -->
            <div class="commitment-card mt-3">
              <h4 class="commitment-title">
                <i class="fa-solid fa-shield-halved text-success me-2"></i> Cam kết độc quyền từ EduLink
              </h4>
              <ul class="commitment-list">
                <li>
                  <i class="fa-solid fa-rotate-left list-icon text-success"></i>
                  <span><strong>Học thử 1 buổi đầu:</strong> Hoàn tiền 100% nếu không phù hợp với phương pháp dạy của thầy.</span>
                </li>
                <li>
                  <i class="fa-solid fa-user-shield list-icon text-primary"></i>
                  <span><strong>Bảo đảm tập trung FaceID:</strong> Tự động nhắc nhở và báo cáo mức độ chuyên cần cho gia đình.</span>
                </li>
                <li>
                  <i class="fa-solid fa-bell list-icon text-warning"></i>
                  <span><strong>Nhắc lịch thông minh:</strong> Tự động gửi SMS & Email trước giờ học 120 phút.</span>
                </li>
              </ul>
            </div>

            <!-- 3. CẦN TƯ VẤN XẾP LỊCH ĐẶC BIỆT? -->
            <div class="special-advice-card mt-3">
              <h4 class="advice-title">Cần tư vấn xếp lịch học đặc biệt?</h4>
              <p class="advice-desc">
                Đội ngũ Cố vấn học vụ EduLink luôn sẵn sàng hỗ trợ sắp xếp lịch riêng phù hợp với thời khóa biểu trường lớp của học sinh.
              </p>
              <div class="advice-contacts">
                <div class="contact-pill">
                  <i class="fa-solid fa-phone text-primary me-2"></i>
                  <span>Hotline tư vấn (8:00 - 22:00): <strong>1900 8899 (Phím 1)</strong></span>
                </div>
                <div class="contact-pill mt-2">
                  <i class="fa-regular fa-envelope text-primary me-2"></i>
                  <span>Email phòng giáo vụ: <strong>giaovu@edulink.vn</strong></span>
                </div>
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
  name: "SmartTrialTeacherProfile",
  data() {
    return {
      isLiked: false,
      selectedPackageId: "1on1",
      learningMode: "online",
      currentSelectedSlotText: "Thứ 2, Tối 19:30",
      activeDayKey: "T2",
      activeTimeKey: "19:30",
      bookingForm: {
        studentName: "",
        phone: ""
      },
      packages: {
        "1on1": { id: "1on1", name: "Lớp kèm 1:1 trực tuyến", fee: 350000 },
        "group": { id: "group", name: "Học nhóm nhỏ 3-5 bạn", fee: 180000 },
        "mass": { id: "mass", name: "Lớp chuyên đề đại trà", fee: 90000 }
      }
    };
  },
  computed: {
    selectedPackage() {
      return this.packages[this.selectedPackageId] || this.packages["1on1"];
    }
  },
  methods: {
    formatVND(amount) {
      return new Intl.NumberFormat("vi-VN").format(amount) + "đ";
    },
    selectPackage(pkgId) {
      this.selectedPackageId = pkgId;
    },
    onPackageSelectChange() {
      // triggers computed update
    },
    pickSlot(dayText, timeText) {
      this.currentSelectedSlotText = `${dayText}, ${timeText}`;
      // extract key
      if (dayText.includes("2")) this.activeDayKey = "T2";
      else if (dayText.includes("3")) this.activeDayKey = "T3";
      else if (dayText.includes("4")) this.activeDayKey = "T4";
      else if (dayText.includes("5")) this.activeDayKey = "T5";
      else if (dayText.includes("6")) this.activeDayKey = "T6";
      else if (dayText.includes("7")) this.activeDayKey = "T7";
      else if (dayText.includes("Nhật")) this.activeDayKey = "CN";

      if (timeText.includes("08:00")) this.activeTimeKey = "08:00";
      else if (timeText.includes("14:30")) this.activeTimeKey = "14:30";
      else if (timeText.includes("19:30")) this.activeTimeKey = "19:30";
    },
    isSlotActive(day, time) {
      return this.activeDayKey === day && this.activeTimeKey === time;
    },
    toggleLike() {
      this.isLiked = !this.isLiked;
    },
    scrollToBooking() {
      const el = document.getElementById("bookingCard");
      if (el) {
        el.scrollIntoView({ behavior: "smooth" });
      }
    },
    handleChat() {
      alert("Đang mở cửa sổ chat trực tuyến với TS. Nguyễn Minh Triết...");
    },
    handleBooking() {
      if (!this.bookingForm.studentName || !this.bookingForm.phone) {
        alert("Vui lòng nhập đầy đủ họ tên học sinh và số điện thoại!");
        return;
      }
      alert(
        `Chúc mừng bạn! Đã giữ chỗ thành công ca học "${this.currentSelectedSlotText}" cho học sinh ${this.bookingForm.studentName}. Thầy Triết và phòng đào tạo EduLink sẽ liên hệ qua SĐT ${this.bookingForm.phone} trong 15 phút!`
      );
    }
  }
};
</script>

<style scoped>
.teacher-profile-page {
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

/* 1. Breadcrumb */
.breadcrumb-nav {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 20px;
  flex-wrap: wrap;
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

/* 2. Top Profile Card */
.profile-header-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 28px 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
  margin-bottom: 28px;
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

.teacher-avatar-box {
  position: relative;
  width: 96px;
  height: 96px;
  flex-shrink: 0;
}

.teacher-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 16px;
  object-fit: cover;
}

.verified-badge-icon {
  position: absolute;
  bottom: -4px;
  right: -4px;
  background-color: #ffffff;
  color: #0060d2;
  font-size: 18px;
  border-radius: 50%;
  line-height: 1;
}

.teacher-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}

.teacher-name {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0;
  letter-spacing: -0.4px;
}

.badge-verified {
  background-color: #eff6ff;
  border: 1px solid #dbeafe;
  color: #0060d2;
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 50px;
}

.teacher-workplace {
  font-size: 13.5px;
  color: #475569;
  margin-bottom: 8px;
}

.teacher-trust-badges {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
}

.trust-sep {
  color: #cbd5e1;
}

.profile-top-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-book-now {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  font-weight: 700;
  font-size: 14px;
  height: 42px;
  padding: 0 20px;
  border-radius: 8px;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.2);
}

.btn-book-now:hover {
  background-color: #004fb0;
  transform: translateY(-1px);
}

.btn-chat-now {
  height: 42px;
  padding: 0 16px;
  font-size: 13.5px;
  font-weight: 600;
  border-radius: 8px;
}

.btn-icon-like {
  width: 42px;
  height: 42px;
  border: 1px solid #e2e8f0;
  background-color: #ffffff;
  color: #64748b;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  transition: all 0.2s;
}

.btn-icon-like:hover {
  background-color: #fef2f2;
  color: #ef4444;
  border-color: #fecaca;
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
  gap: 12px;
}

.stat-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  flex-shrink: 0;
}

.icon-blue { background-color: #eff6ff; color: #0060d2; }
.icon-green { background-color: #f0fdf4; color: #16a34a; }
.icon-orange { background-color: #fff7ed; color: #ea580c; }
.icon-purple { background-color: #faf5ff; color: #9333ea; }

.stat-highlight-num {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
}

.stat-highlight-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

/* 3. Bố cục 2 cột chính */
.profile-layout {
  display: flex;
  gap: 28px;
  align-items: flex-start;
}

.profile-main-column {
  flex: 1 1 64%;
}

.profile-sidebar-column {
  flex: 1 1 36%;
}

/* Section Card chung */
.section-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px 28px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.section-heading {
  font-size: 17.5px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  letter-spacing: -0.3px;
}

.heading-accent {
  width: 4px;
  height: 18px;
  background-color: #0060d2;
  border-radius: 4px;
  display: inline-block;
}

.bio-paragraphs {
  font-size: 14px;
  color: #334155;
  line-height: 1.65;
}

.bio-paragraphs p {
  margin-bottom: 12px;
}

.teaching-features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-top: 20px;
}

.feature-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 16px;
}

.feature-icon {
  font-size: 20px;
  margin-bottom: 10px;
}

.feature-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.feature-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.45;
  margin-bottom: 0;
}

/* Credentials Grid */
.credentials-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.credential-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 16px;
}

.cred-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.cred-blue { background-color: #eff6ff; color: #0060d2; }
.cred-green { background-color: #f0fdf4; color: #16a34a; }
.cred-orange { background-color: #fff7ed; color: #ea580c; }
.cred-gray { background-color: #f1f5f9; color: #475569; }

.cred-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 2px;
}

.cred-school {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 6px;
}

.cred-status {
  font-size: 11.5px;
  font-weight: 600;
}

.text-warning-dark {
  color: #b45309;
}

/* Class Packages */
.class-types-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.class-package-card {
  position: relative;
  background-color: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 20px 16px;
  display: flex;
  flex-direction: column;
  cursor: pointer;
  transition: all 0.2s ease;
}

.class-package-card:hover {
  border-color: #0060d2;
  box-shadow: 0 6px 18px rgba(0, 96, 210, 0.08);
}

.class-package-card.selected-pkg {
  border-color: #0060d2;
  background-color: #fbfdff;
}

.popular-package {
  border-color: #16a34a;
}

.popular-ribbon {
  position: absolute;
  top: -10px;
  right: 12px;
  background-color: #16a34a;
  color: #ffffff;
  font-size: 10px;
  font-weight: 800;
  padding: 2px 10px;
  border-radius: 50px;
  letter-spacing: 0.5px;
}

.pkg-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 50px;
  display: inline-block;
  margin-bottom: 10px;
  max-width: fit-content;
}

.badge-blue { background-color: #eff6ff; color: #0060d2; }
.badge-green { background-color: #f0fdf4; color: #16a34a; }
.badge-gray { background-color: #f1f5f9; color: #475569; }

.pkg-title {
  font-size: 14.5px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 6px;
}

.pkg-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.45;
  flex-grow: 1;
  margin-bottom: 16px;
}

.pkg-price-wrap {
  margin-bottom: 14px;
}

.pkg-price {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
}

.pkg-unit {
  font-size: 12px;
  color: #64748b;
  margin-left: 4px;
}

/* Schedule Matrix */
.schedule-legend {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 12px;
  color: #64748b;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.dot-blue {
  width: 8px;
  height: 8px;
  background-color: #0060d2;
  border-radius: 50%;
}

.dot-gray {
  width: 8px;
  height: 8px;
  background-color: #cbd5e1;
  border-radius: 50%;
}

.schedule-matrix-wrapper {
  overflow-x: auto;
}

.schedule-matrix-table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
}

.schedule-matrix-table th,
.schedule-matrix-table td {
  border: 1px solid #f1f5f9;
  padding: 10px 6px;
  vertical-align: middle;
}

.schedule-matrix-table th {
  background-color: #f8fafc;
  font-size: 12.5px;
  font-weight: 700;
  color: #475569;
}

.matrix-header-col {
  width: 140px;
  text-align: left;
  padding-left: 12px !important;
}

.shift-name {
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  padding-left: 12px !important;
  background-color: #f8fafc;
}

.slot-btn {
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #0060d2;
  font-size: 12px;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
  max-width: 68px;
}

.slot-btn:hover {
  background-color: #0060d2;
  color: #ffffff;
  border-color: #0060d2;
}

.slot-btn.selected {
  background-color: #0060d2;
  color: #ffffff;
  border-color: #0060d2;
  box-shadow: 0 2px 8px rgba(0, 96, 210, 0.3);
}

.slot-closed {
  font-size: 11.5px;
  color: #94a3b8;
  background-color: #f8fafc;
  padding: 6px 12px;
  border-radius: 6px;
  display: inline-block;
}

.schedule-hint-note {
  display: flex;
  align-items: center;
  font-size: 12.5px;
  color: #64748b;
  margin-top: 14px;
}

/* Reviews */
.overall-rating-badge {
  font-size: 14px;
  color: #0f172a;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.review-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 18px 20px;
}

.review-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.reviewer-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.reviewer-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 13.5px;
}

.avatar-blue { background-color: #eff6ff; color: #0060d2; }
.avatar-green { background-color: #f0fdf4; color: #16a34a; }

.reviewer-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.reviewer-course {
  font-size: 11.5px;
  color: #64748b;
}

.review-date {
  font-size: 12px;
  color: #94a3b8;
}

.review-stars {
  font-size: 11px;
  margin-bottom: 8px;
}

.review-text {
  font-size: 13px;
  color: #334155;
  line-height: 1.55;
  margin-bottom: 0;
}

/* Sidebar Booking */
.sticky-sidebar-wrapper {
  position: sticky;
  top: 24px;
}

.booking-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
}

.booking-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.booking-card-title {
  font-size: 16.5px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0;
}

.badge-accepting {
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 50px;
}

.booking-label {
  font-size: 12.5px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.custom-select,
.booking-input {
  height: 42px;
  font-size: 13.5px;
  border-radius: 8px;
  border: 1px solid #d8dee4;
}

.radio-options-row {
  display: flex;
  gap: 16px;
}

.custom-radio-label {
  font-size: 13px;
  color: #334155;
  display: flex;
  align-items: center;
  cursor: pointer;
}

.selected-slot-box {
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
  color: #0060d2;
  font-weight: 700;
  font-size: 13.5px;
}

.fee-calculation-box {
  background-color: #f8fafc;
  border-radius: 10px;
  padding: 14px 16px;
  margin: 18px 0;
}

.fee-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 6px;
}

.fee-divider {
  margin: 10px 0;
  border-color: #e2e8f0;
}

.fee-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total-label {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.total-price {
  font-size: 20px;
  font-weight: 800;
  color: #0060d2;
}

.btn-confirm-booking {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  height: 46px;
  border-radius: 10px;
  font-size: 14.5px;
  font-weight: 700;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
}

.btn-confirm-booking:hover {
  background-color: #004fb0;
  transform: translateY(-1px);
}

/* Commitment card */
.commitment-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px 22px;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
}

.commitment-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 12px;
}

.commitment-list {
  list-style: none;
  padding-left: 0;
  margin-bottom: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.commitment-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 12.5px;
  color: #334155;
  line-height: 1.45;
}

.list-icon {
  margin-top: 3px;
  font-size: 13px;
}

/* Special Advice card */
.special-advice-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px;
}

.advice-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 6px;
}

.advice-desc {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.45;
  margin-bottom: 14px;
}

.contact-pill {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 12px;
  color: #334155;
  display: flex;
  align-items: center;
}

/* Responsive */
@media (max-width: 991px) {
  .profile-layout {
    flex-direction: column;
  }
  .profile-main-column,
  .profile-sidebar-column {
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
  .teaching-features-grid,
  .class-types-grid {
    grid-template-columns: 1fr;
  }
  .credentials-grid {
    grid-template-columns: 1fr;
  }
}
</style>
