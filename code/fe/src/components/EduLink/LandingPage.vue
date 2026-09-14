<template>
  <div class="edulink-landing-page">
    <!-- 1. TOP NAVBAR -->
    <header class="main-header">
      <div class="header-container">
        <!-- Logo -->
        <router-link to="/" class="brand-logo">
          <div class="logo-icon-wrap">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <span class="brand-name">Edu<span>Link</span></span>
        </router-link>

        <!-- Navigation Links -->
        <nav class="nav-links">
          <a href="#tutors" class="nav-item">Tìm gia sư</a>
          <a href="#subjects" class="nav-item">Môn học</a>
          <router-link to="/my-schedule" class="nav-item">Lịch rảnh & Ghép lịch</router-link>
          <router-link to="/face-id" class="nav-item">Tính năng Face ID</router-link>
          <router-link to="/thanh-toan" class="nav-item">Bảng giá học phí</router-link>
          <a href="javascript:void(0)" @click="openFeedbackModal" class="nav-item">Đóng góp ý kiến</a>
          <router-link to="/dang-ky" class="nav-item">Đăng nhập</router-link>
        </nav>

        <!-- Header Actions -->
        <div class="header-actions">
          <button class="btn btn-primary btn-book-trial" @click="openTrialModal('Đăng ký học thử tổng quát')">
            Đặt lịch học thử
          </button>
          <router-link to="/hoc-vien" class="user-avatar-btn" title="Xem hồ sơ cá nhân">
            <i class="fa-regular fa-user"></i>
          </router-link>
        </div>
      </div>
    </header>

    <!-- 2. HERO SECTION -->
    <section class="hero-section">
      <div class="hero-container">
        <!-- Hero Left Column -->
        <div class="hero-content-left">
          <div class="hero-pill-badge">
            <span class="sparkle-icon">✨</span> Nền tảng kết nối gia sư thông minh hàng đầu
          </div>

          <h1 class="hero-title">
            Kết nối Gia sư chất lượng cao - Tối ưu lộ trình học với công nghệ <span class="highlight-ai">AI & Face ID</span>
          </h1>

          <p class="hero-desc">
            EduLink giúp phụ huynh và học sinh tìm đúng gia sư chuẩn mực, sắp xếp lịch học thông minh theo tuần, tự động điểm danh nâng cao và cam kết tiến bộ rõ rệt qua từng buổi học.
          </p>

          <!-- Floating Search & Filter Bar -->
          <div class="hero-search-card">
            <div class="search-inputs-grid">
              <!-- Select Subject & Grade -->
              <div class="filter-col">
                <label class="filter-label">Chọn môn học & Lớp</label>
                <div class="input-with-icon">
                  <i class="fa-solid fa-book-open select-icon"></i>
                  <select v-model="searchSubject" class="form-select filter-select">
                    <option value="Toán học (Lớp 1 - 12)">Toán học (Lớp 1 - 12)</option>
                    <option value="Tiếng Anh & IELTS">Tiếng Anh & IELTS</option>
                    <option value="Vật lý & Hóa học">Vật lý & Hóa học</option>
                    <option value="Lập trình & Tin học">Lập trình & Tin học</option>
                    <option value="Ngữ văn & Viết luận">Ngữ văn & Viết luận</option>
                  </select>
                </div>
              </div>

              <!-- Select Location -->
              <div class="filter-col">
                <label class="filter-label">Khu vực / Cơ sở</label>
                <div class="input-with-icon">
                  <i class="fa-solid fa-location-dot select-icon"></i>
                  <select v-model="searchLocation" class="form-select filter-select">
                    <option value="Quận 1 / Trực tuyến">Quận 1 / Trực tuyến</option>
                    <option value="Toàn quốc (Online Face ID)">Toàn quốc (Online Face ID)</option>
                    <option value="Quận 10 - Cơ sở trực tiếp">Quận 10 - Cơ sở trực tiếp</option>
                    <option value="Quận 5 - Cơ sở trực tiếp">Quận 5 - Cơ sở trực tiếp</option>
                    <option value="Hà Nội & TP.HCM">Hà Nội & TP.HCM</option>
                  </select>
                </div>
              </div>

              <!-- Select Time Slot -->
              <div class="filter-col">
                <label class="filter-label">Khung giờ học</label>
                <div class="input-with-icon">
                  <i class="fa-regular fa-clock select-icon"></i>
                  <select v-model="searchTimeSlot" class="form-select filter-select">
                    <option value="Tối (18:00 - 21:00)">Tối (18:00 - 21:00)</option>
                    <option value="Sáng (08:00 - 11:30)">Sáng (08:00 - 11:30)</option>
                    <option value="Chiều (14:00 - 17:30)">Chiều (14:00 - 17:30)</option>
                    <option value="Cuối tuần (T7 & CN)">Cuối tuần (T7 & CN)</option>
                  </select>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="filter-submit-col">
                <button class="btn btn-primary btn-search-tutor" @click="handleSearchTutor">
                  <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm gia sư ngay
                </button>
              </div>
            </div>

            <div class="search-footer-note">
              <i class="fa-solid fa-star text-warning me-1"></i>
              Hơn <strong>1,200+</strong> gia sư đã qua kiểm duyệt chuyên môn đang sẵn sàng.
            </div>
          </div>

          <!-- 3 Stats Counter -->
          <div class="hero-stats-row">
            <div class="stat-box">
              <div class="stat-number">10,000+</div>
              <div class="stat-desc">Gia sư, chuyên gia kiểm duyệt</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-box">
              <div class="stat-number">98.2%</div>
              <div class="stat-desc">Học sinh tăng điểm sau 30 ngày</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-box">
              <div class="stat-number">4.89 <span class="star-gold">★</span></div>
              <div class="stat-desc">Từ 28,000+ ý kiến phụ huynh</div>
            </div>
          </div>
        </div>

        <!-- Hero Right Column: Interactive Tutor Preview Card -->
        <div class="hero-content-right">
          <div class="hero-featured-card-wrap">
            <!-- Main Teacher Floating Card -->
            <div class="hero-tutor-card">
              <div class="tutor-header-row">
                <img
                  src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=160&q=80"
                  alt="TS. Nguyễn Minh Triết"
                  class="tutor-card-avatar"
                />
                <div class="tutor-header-meta">
                  <div class="tutor-name-line">
                    <span class="tutor-card-name">TS. Nguyễn Minh Triết</span>
                    <span class="badge-online-ready">● Sẵn sàng</span>
                  </div>
                  <div class="tutor-card-role">Giảng viên ĐH Bách Khoa TP.HCM • 8+ năm kinh nghiệm</div>
                  <div class="tutor-card-rating">
                    <i class="fa-solid fa-star text-warning"></i>
                    <strong class="text-dark ms-1">4.9</strong>
                    <span class="text-muted">(128 đánh giá)</span>
                    <span class="meta-dot">•</span>
                    <span class="text-primary fw-bold">Toán Chuyên & ĐGNL</span>
                  </div>
                </div>
              </div>

              <!-- Available Slot Row -->
              <div class="tutor-slot-box mt-3">
                <div class="slot-left-text">
                  <i class="fa-regular fa-calendar-check text-primary me-2"></i>
                  <span>Lịch rảnh khả dụng: <strong>T2, T4, T6 (19:30 - 21:00)</strong></span>
                </div>
                <router-link to="/giao-vien" class="btn btn-sm btn-outline-primary btn-view-cal">
                  Xem lịch
                </router-link>
              </div>

              <!-- Verified Certification Bar -->
              <div class="tutor-cert-row mt-3">
                <div class="cert-left">
                  <i class="fa-solid fa-shield-check text-success me-1"></i>
                  <span>Kiểm định thông tin & Bằng cấp</span>
                </div>
                <span class="badge-match-100">100% Khớp</span>
              </div>
            </div>

            <!-- Floating Matching Pill -->
            <div class="floating-match-pill">
              <div class="pulsing-green-dot"></div>
              <span>Đang ghép gia sư trong <strong>1 giây</strong></span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 3. SECTION: TẠI SAO CHỌN EDULINK -->
    <section class="section-features">
      <div class="section-container text-center">
        <div class="section-tag">ĐẶC QUYỀN CÔNG NGHỆ</div>
        <h2 class="section-heading">Tại sao hơn 28,000 phụ huynh chọn EduLink?</h2>
        <p class="section-sub">
          Sự kết hợp hoàn hảo giữa công nghệ sinh trắc học hiện đại và mạng lưới gia sư sư phạm được kiểm định độc lập.
        </p>

        <!-- 4 Grid Cards -->
        <div class="features-grid">
          <!-- Card 1: Face ID -->
          <div class="feature-card">
            <div class="feature-icon-box icon-bg-blue">
              <i class="fa-solid fa-fingerprint"></i>
            </div>
            <h3 class="feature-title">Sinh trắc học Face ID an toàn</h3>
            <p class="feature-desc">
              Tự động nhận diện học sinh và gia sư khi bước vào lớp học trực tuyến. Cam kết học đúng người, bảo mật buổi học 100%, không gian lận giờ dạy.
            </p>
            <router-link to="/face-id" class="feature-link">
              Tìm hiểu công nghệ Face ID <i class="fa-solid fa-arrow-right ms-1"></i>
            </router-link>
          </div>

          <!-- Card 2: Smart Schedule -->
          <div class="feature-card">
            <div class="feature-icon-box icon-bg-green">
              <i class="fa-solid fa-calendar-days"></i>
            </div>
            <h3 class="feature-title">Gợi ý lịch học thông minh</h3>
            <p class="feature-desc">
              Học sinh chỉ cần thả khung thời gian rảnh, thuật toán thông minh sẽ tự động ghép nối gia sư tương thích theo môn, trình độ và khoảng cách địa lý.
            </p>
            <router-link to="/my-schedule" class="feature-link">
              Khám phá thuật toán Smart Match <i class="fa-solid fa-arrow-right ms-1"></i>
            </router-link>
          </div>

          <!-- Card 3: 5-step Verification -->
          <div class="feature-card">
            <div class="feature-icon-box icon-bg-purple">
              <i class="fa-solid fa-id-card-clip"></i>
            </div>
            <h3 class="feature-title">Kiểm duyệt gia sư 5 bước</h3>
            <p class="feature-desc">
              100% hồ sơ được thẩm định văn bằng, lý lịch tư pháp, phỏng vấn và kiểm tra kỹ năng sư phạm trực tiếp qua camera video.
            </p>
            <router-link to="/giao-vien" class="feature-link">
              Quy trình tuyển dụng gia sư <i class="fa-solid fa-arrow-right ms-1"></i>
            </router-link>
          </div>

          <!-- Card 4: VietQR & Refund -->
          <div class="feature-card">
            <div class="feature-icon-box icon-bg-orange">
              <i class="fa-solid fa-qrcode"></i>
            </div>
            <h3 class="feature-title">Minh bạch học phí & VietQR</h3>
            <p class="feature-desc">
              Thanh toán linh hoạt từng buổi hoặc theo tháng qua VietQR, ví Momo tự động. Chính sách hoàn tiền minh bạch nếu không hài lòng, cam kết 100% không phát sinh phụ phí.
            </p>
            <router-link to="/thanh-toan" class="feature-link">
              Xem bảng giá học phí <i class="fa-solid fa-arrow-right ms-1"></i>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- 4. SECTION: CÁC MÔN HỌC & LỘ TRÌNH ĐƯỢC QUAN TÂM NHẤT -->
    <section class="section-subjects" id="subjects">
      <div class="section-container">
        <div class="subjects-header-flex">
          <div>
            <div class="section-tag">CHƯƠNG TRÌNH TỪNG CẤP BẬC</div>
            <h2 class="section-heading mb-0">Các môn học & Lộ trình được quan tâm nhất</h2>
          </div>
          <a href="javascript:void(0)" @click="filterSubject('all')" class="view-all-link">
            Xem toàn bộ 24+ chuyên đề <i class="fa-solid fa-arrow-right ms-1"></i>
          </a>
        </div>

        <!-- 6 Subjects Grid (3 cols x 2 rows) -->
        <div class="subjects-grid mt-4">
          <!-- Subject 1: Toán học -->
          <div class="subject-card">
            <div class="subj-top-row">
              <div class="subj-icon-circle icon-blue">
                <i class="fa-solid fa-calculator"></i>
              </div>
              <span class="subj-badge badge-popular">Phổ biến nhất</span>
            </div>
            <h3 class="subj-title">Toán học (Lớp 1 - 12 & THPT)</h3>
            <p class="subj-desc">
              Bám sát khung BGD, ĐGNL, Toán Quốc Tế, bồi dưỡng học sinh giỏi và lấy lại căn bản cấp tốc.
            </p>
            <div class="subj-footer-row">
              <div class="subj-price-wrap">
                <span class="price-sub">Học phí từ</span>
                <span class="price-val">120.000đ</span>
                <span class="price-unit">/ buổi</span>
              </div>
              <button class="btn-circle-arrow" @click="openTrialModal('Toán học (Lớp 1 - 12 & THPT)')">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- Subject 2: Tiếng Anh IELTS -->
          <div class="subject-card">
            <div class="subj-top-row">
              <div class="subj-icon-circle icon-green">
                <i class="fa-solid fa-language"></i>
              </div>
              <span class="subj-badge badge-deal">Đang có ưu đãi</span>
            </div>
            <h3 class="subj-title">Tiếng Anh & Luyện thi IELTS</h3>
            <p class="subj-desc">
              Gia sư bản ngữ & 8.0+ IELTS, giáo trình tương tác Speaking 1-1, tiếng Anh giao tiếp và chuẩn bị du học.
            </p>
            <div class="subj-footer-row">
              <div class="subj-price-wrap">
                <span class="price-sub">Học phí từ</span>
                <span class="price-val">150.000đ</span>
                <span class="price-unit">/ buổi</span>
              </div>
              <button class="btn-circle-arrow" @click="openTrialModal('Tiếng Anh & Luyện thi IELTS')">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- Subject 3: KHTN Lý - Hóa - Sinh -->
          <div class="subject-card">
            <div class="subj-top-row">
              <div class="subj-icon-circle icon-orange">
                <i class="fa-solid fa-flask"></i>
              </div>
              <span class="subj-badge badge-score">Nâng cao điểm số</span>
            </div>
            <h3 class="subj-title">Khoa học Tự nhiên (Lý - Hóa - Sinh)</h3>
            <p class="subj-desc">
              Nắm vững bản chất chuyên sâu, thực hành thí nghiệm ảo, chuẩn bị cho các kỳ thi tuyển sinh Quốc gia.
            </p>
            <div class="subj-footer-row">
              <div class="subj-price-wrap">
                <span class="price-sub">Học phí từ</span>
                <span class="price-val">130.000đ</span>
                <span class="price-unit">/ buổi</span>
              </div>
              <button class="btn-circle-arrow" @click="openTrialModal('Khoa học Tự nhiên (Lý - Hóa - Sinh)')">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- Subject 4: Lập trình máy tính -->
          <div class="subject-card">
            <div class="subj-top-row">
              <div class="subj-icon-circle icon-purple">
                <i class="fa-solid fa-code"></i>
              </div>
              <span class="subj-badge badge-future">Định hướng tương lai</span>
            </div>
            <h3 class="subj-title">Lập trình & Khoa học Máy tính</h3>
            <p class="subj-desc">
              Lập trình Python, Web cơ bản & nâng cao, AI, thuật toán thuật giải cho học sinh từ THCS đến THPT.
            </p>
            <div class="subj-footer-row">
              <div class="subj-price-wrap">
                <span class="price-sub">Học phí từ</span>
                <span class="price-val">180.000đ</span>
                <span class="price-unit">/ buổi</span>
              </div>
              <button class="btn-circle-arrow" @click="openTrialModal('Lập trình & Khoa học Máy tính')">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- Subject 5: Ngữ Văn -->
          <div class="subject-card">
            <div class="subj-top-row">
              <div class="subj-icon-circle icon-teal">
                <i class="fa-solid fa-feather-pointed"></i>
              </div>
              <span class="subj-badge badge-high">Đạt điểm cao</span>
            </div>
            <h3 class="subj-title">Ngữ Văn & Kỹ năng Viết luận</h3>
            <p class="subj-desc">
              Phát triển tư duy logic và cảm thụ, rèn luyện kỹ năng viết luận học thuật, cảm nhận tác phẩm sáng tạo.
            </p>
            <div class="subj-footer-row">
              <div class="subj-price-wrap">
                <span class="price-sub">Học phí từ</span>
                <span class="price-val">120.000đ</span>
                <span class="price-unit">/ buổi</span>
              </div>
              <button class="btn-circle-arrow" @click="openTrialModal('Ngữ Văn & Kỹ năng Viết luận')">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- Subject 6: Ôn Chuyên HSG -->
          <div class="subject-card">
            <div class="subj-top-row">
              <div class="subj-icon-circle icon-amber">
                <i class="fa-solid fa-award"></i>
              </div>
              <span class="subj-badge badge-expert">Học sinh giỏi</span>
            </div>
            <h3 class="subj-title">Ôn Chuyên & Học sinh Giỏi cấp Tỉnh/QG</h3>
            <p class="subj-desc">
              Lộ trình đặc biệt được hướng dẫn bởi các thủ khoa, giải Quốc gia và giáo viên các trường Chuyên hàng đầu.
            </p>
            <div class="subj-footer-row">
              <div class="subj-price-wrap">
                <span class="price-sub">Học phí từ</span>
                <span class="price-val">200.000đ</span>
                <span class="price-unit">/ buổi</span>
              </div>
              <button class="btn-circle-arrow" @click="openTrialModal('Ôn Chuyên & Học sinh Giỏi')">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. SECTION: GIA SƯ TIÊU BIỂU ĐƯỢC YÊU THÍCH NHẤT -->
    <section class="section-tutors" id="tutors">
      <div class="section-container">
        <div class="tutors-header-flex">
          <div>
            <div class="section-tag">ĐỘI NGŨ GIẢNG VIÊN VÀ GIA SƯ</div>
            <h2 class="section-heading mb-0">Gia sư tiêu biểu được yêu thích nhất</h2>
          </div>
          <div class="carousel-nav-btns">
            <button class="btn-nav-circle" @click="prevTutor"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="btn-nav-circle" @click="nextTutor"><i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>

        <!-- 3 Tutor Cards Grid -->
        <div class="tutors-grid mt-4">
          <!-- Tutor 1: TS. Nguyễn Minh Triết -->
          <div class="tutor-showcase-card">
            <div class="tutor-cover-wrap">
              <img
                src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80"
                alt="TS. Nguyễn Minh Triết"
                class="tutor-cover-img"
              />
              <span class="tutor-badge-tag tag-verified">
                <i class="fa-solid fa-shield-check me-1"></i> Gia sư xuất sắc EduLink
              </span>
              <span class="tutor-mode-tag tag-blue">Kèm 1:1 & Lớp nhóm</span>
            </div>

            <div class="tutor-card-body">
              <div class="tutor-card-name-row">
                <h3 class="tutor-main-name">TS. Nguyễn Minh Triết</h3>
                <div class="tutor-star-rating">
                  <i class="fa-solid fa-star text-warning"></i>
                  <span class="star-score">4.9</span>
                  <span class="review-count">(128)</span>
                </div>
              </div>

              <p class="tutor-short-bio">
                Giảng viên Khoa Toán - Tin học ĐH Khoa học Tự Nhiên. 8+ năm bồi dưỡng học sinh giỏi và luyện thi ĐGNL.
              </p>

              <div class="tutor-skill-pills">
                <span class="skill-pill">Toán Chuyên 12</span>
                <span class="skill-pill">Đại số nâng cao</span>
                <span class="skill-pill">Toán ĐGNL ĐHQG</span>
              </div>

              <div class="tutor-card-action-bar">
                <div class="tutor-price-info">
                  <span class="price-prefix">Học phí:</span>
                  <span class="price-number">350.000đ</span>
                  <span class="price-suffix">/ một buổi</span>
                </div>
                <button class="btn btn-primary btn-book-tutor" @click="openTrialModal('TS. Nguyễn Minh Triết - Toán')">
                  Đặt lịch thử
                </button>
              </div>
            </div>
          </div>

          <!-- Tutor 2: ThS. Lê Phương Thảo -->
          <div class="tutor-showcase-card">
            <div class="tutor-cover-wrap">
              <img
                src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80"
                alt="ThS. Lê Phương Thảo"
                class="tutor-cover-img"
              />
              <span class="tutor-badge-tag tag-verified">
                <i class="fa-solid fa-shield-check me-1"></i> Gia sư xuất sắc EduLink
              </span>
              <span class="tutor-mode-tag tag-green">IELTS 8.5</span>
            </div>

            <div class="tutor-card-body">
              <div class="tutor-card-name-row">
                <h3 class="tutor-main-name">ThS. Lê Phương Thảo</h3>
                <div class="tutor-star-rating">
                  <i class="fa-solid fa-star text-warning"></i>
                  <span class="star-score">5.0</span>
                  <span class="review-count">(96)</span>
                </div>
              </div>

              <p class="tutor-short-bio">
                Thạc sĩ Ngôn ngữ Ứng dụng, 6+ năm luyện thi IELTS & Tiếng Anh chuyên sâu (IELTS 8.5, Band 8.5 Speaking 1:1).
              </p>

              <div class="tutor-skill-pills">
                <span class="skill-pill">IELTS 8.5+</span>
                <span class="skill-pill">Speaking 1 kèm 1</span>
                <span class="skill-pill">Giao tiếp phản xạ</span>
              </div>

              <div class="tutor-card-action-bar">
                <div class="tutor-price-info">
                  <span class="price-prefix">Học phí:</span>
                  <span class="price-number">300.000đ</span>
                  <span class="price-suffix">/ một buổi</span>
                </div>
                <button class="btn btn-primary btn-book-tutor" @click="openTrialModal('ThS. Lê Phương Thảo - IELTS')">
                  Đặt lịch thử
                </button>
              </div>
            </div>
          </div>

          <!-- Tutor 3: ThS. Trần Hoàng Nam -->
          <div class="tutor-showcase-card">
            <div class="tutor-cover-wrap">
              <img
                src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80"
                alt="ThS. Trần Hoàng Nam"
                class="tutor-cover-img"
              />
              <span class="tutor-badge-tag tag-tech">
                <i class="fa-solid fa-code me-1"></i> Chuyên gia Công nghệ
              </span>
              <span class="tutor-mode-tag tag-purple">Lập trình & STEM</span>
            </div>

            <div class="tutor-card-body">
              <div class="tutor-card-name-row">
                <h3 class="tutor-main-name">ThS. Trần Hoàng Nam</h3>
                <div class="tutor-star-rating">
                  <i class="fa-solid fa-star text-warning"></i>
                  <span class="star-score">4.9</span>
                  <span class="review-count">(114)</span>
                </div>
              </div>

              <p class="tutor-short-bio">
                Senior Tech Lead tại Top Fintech công nghệ, 5+ năm giảng dạy lập trình cho thanh thiếu niên, chuyên gia AI & React.
              </p>

              <div class="tutor-skill-pills">
                <span class="skill-pill">Frontend React</span>
                <span class="skill-pill">Python cơ bản</span>
                <span class="skill-pill">Thuật toán lập trình</span>
              </div>

              <div class="tutor-card-action-bar">
                <div class="tutor-price-info">
                  <span class="price-prefix">Học phí:</span>
                  <span class="price-number">280.000đ</span>
                  <span class="price-suffix">/ một buổi</span>
                </div>
                <button class="btn btn-primary btn-book-tutor" @click="openTrialModal('ThS. Trần Hoàng Nam - Lập trình')">
                  Đặt lịch thử
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 6. SECTION: 4 BƯỚC BẮT ĐẦU HỌC CÙNG EDULINK -->
    <section class="section-process">
      <div class="section-container text-center">
        <div class="section-tag">QUY TRÌNH ĐƠN GIẢN</div>
        <h2 class="section-heading">4 bước bắt đầu học cùng EduLink</h2>
        <p class="section-sub">
          Từ việc chọn mục tiêu đến khi bước vào phòng học, thông minh hơn, sâu sát hơn và tiện lợi trong chưa đầy 5 phút.
        </p>

        <div class="process-steps-grid mt-5">
          <!-- Step 1 -->
          <div class="step-item">
            <div class="step-circle-icon">
              <span class="step-number-pill">1</span>
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h4 class="step-title">Chọn môn & Lịch rảnh</h4>
            <p class="step-desc">
              Chọn môn học, cấp độ hiện tại, đặt mục tiêu điểm số và định dạng các buổi rảnh trong tuần của học sinh.
            </p>
          </div>

          <!-- Step 2 -->
          <div class="step-item">
            <div class="step-circle-icon">
              <span class="step-number-pill">2</span>
              <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <h4 class="step-title">AI ghép nối & Học thử</h4>
            <p class="step-desc">
              Hệ thống AI xuất sắc (Smart Match) kết nối gia sư phù hợp nhất. Phụ huynh đặt lịch học thử 1 buổi hoàn toàn miễn phí.
            </p>
          </div>

          <!-- Step 3 -->
          <div class="step-item">
            <div class="step-circle-icon">
              <span class="step-number-pill">3</span>
              <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h4 class="step-title">Điểm danh Face ID 3s</h4>
            <p class="step-desc">
              Vào lớp qua EduLink, hệ thống đối soát sinh trắc học khuôn mặt tự động, ghi nhận giờ học chuẩn xác tuyệt đối.
            </p>
          </div>

          <!-- Step 4 -->
          <div class="step-item">
            <div class="step-circle-icon">
              <span class="step-number-pill">4</span>
              <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <h4 class="step-title">Đón nhận & Bứt phá</h4>
            <p class="step-desc">
              Nhận bảng đánh giá sau mỗi buổi học và tiến bộ từng tuần, đạt điểm số mục tiêu trong kỳ thi.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. SECTION: CẢM NHẬN TỪ PHỤ HUYNH & HỌC VIÊN -->
    <section class="section-testimonials">
      <div class="section-container text-center">
        <div class="section-tag">ĐƯỢC TIN CẬY & YÊU QUÝ</div>
        <h2 class="section-heading">Cảm nhận từ Phụ huynh & Học viên</h2>
        <p class="section-sub">
          Lắng nghe chia sẻ chân thật nhất khi đồng hành cùng EduLink.
        </p>

        <div class="testimonials-grid mt-4">
          <!-- Testimonial 1 -->
          <div class="testimonial-card">
            <div class="testimonial-stars">
              <i class="fa-solid fa-star" v-for="i in 5" :key="i"></i>
            </div>
            <p class="testimonial-quote">
              "Trước đây tôi rất ngại tìm gia sư online vì sợ không biết giáo viên có dạy đúng người không. Khi dùng EduLink có Face ID điểm danh và gửi video báo cáo buổi học, tôi rất an tâm. Điểm số của con tăng rõ rệt sau 2 tháng học."
            </p>
            <div class="testimonial-author">
              <img
                src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=100&q=80"
                alt="Phụ huynh Mai Lan"
                class="author-avatar"
              />
              <div class="author-meta text-start">
                <div class="author-name">Phụ huynh Mai Lan</div>
                <div class="author-role">Phụ huynh bé Hoàng Lan (Lớp 11 Chuyên Ngoại ngữ - Amsterdam)</div>
              </div>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="testimonial-card">
            <div class="testimonial-stars">
              <i class="fa-solid fa-star" v-for="i in 5" :key="i"></i>
            </div>
            <p class="testimonial-quote">
              "Em học IELTS cùng cô Phương Thảo qua EduLink từ mốc 6.0 mà sau 3 tháng đã đạt 7.5 Overall. Tính năng xếp lịch thông minh giúp em không bị trùng với lịch học bận rộn ở trường. Cảm ơn EduLink rất nhiều!"
            </p>
            <div class="testimonial-author">
              <img
                src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=100&q=80"
                alt="Lê Anh Khôi"
                class="author-avatar"
              />
              <div class="author-meta text-start">
                <div class="author-name">Lê Anh Khôi</div>
                <div class="author-role">Học viên xuất sắc IELTS 7.5 (Hà Nội)</div>
              </div>
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="testimonial-card">
            <div class="testimonial-stars">
              <i class="fa-solid fa-star" v-for="i in 5" :key="i"></i>
            </div>
            <p class="testimonial-quote">
              "Tôi đặc biệt thích cơ chế thanh toán qua VietQR minh bạch và báo cáo sau mỗi buổi học. Con tôi tự giác học hơn hẳn khi thấy kết quả cải thiện rõ ràng qua từng tuần. Rất đáng tin cậy!"
            </p>
            <div class="testimonial-author">
              <img
                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80"
                alt="Anh Lê Quốc Trọng"
                class="author-avatar"
              />
              <div class="author-meta text-start">
                <div class="author-name">Anh Lê Quốc Trọng</div>
                <div class="author-role">Phụ huynh học sinh Lớp 8 (Đà Nẵng)</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 8. SECTION CTA BANNER -->
    <section class="section-cta">
      <div class="cta-banner-container">
        <div class="cta-badge">
          <i class="fa-solid fa-circle-check me-1"></i> Cam kết chất lượng gia sư 100% hoặc đổi gia sư miễn phí trong 48 tiếng
        </div>
        <h2 class="cta-title">Sẵn sàng bứt phá điểm số cùng gia sư EduLink ngay hôm nay?</h2>
        <p class="cta-sub">
          Đăng ký để được chuyên viên tư vấn lộ trình học tập miễn phí, ghép nối gia sư giỏi trong 24 giờ và nhận ngay 1 buổi học thử miễn phí.
        </p>
        <div class="cta-actions">
          <button class="btn btn-cta-white" @click="openTrialModal('Đăng ký học thử từ CTA')">
            Đăng ký học thử miễn phí
          </button>
          <router-link to="/dang-ky" class="btn btn-cta-outline">
            Đăng ký trở thành gia sư
          </router-link>
        </div>
      </div>
    </section>

    <!-- 9. FOOTER -->
    <footer class="main-footer">
      <div class="footer-container">
        <div class="footer-grid">
          <!-- Col 1: Brand Info -->
          <div class="footer-col footer-brand-col">
            <div class="brand-logo">
              <div class="logo-icon-wrap">
                <i class="fa-solid fa-graduation-cap"></i>
              </div>
              <span class="brand-name">Edu<span>Link</span></span>
            </div>
            <p class="footer-brand-desc mt-3">
              Nền tảng kết nối gia sư chất lượng cao hàng đầu Việt Nam. Ứng dụng công nghệ Face ID nhận diện thông minh kết hợp thuật toán ghép lớp học tự động.
            </p>
            <div class="footer-trust-badges mt-3">
              <span class="badge-trust"><i class="fa-solid fa-shield-halved text-success me-1"></i> Verified Face ID</span>
              <span class="badge-trust"><i class="fa-solid fa-star text-warning me-1"></i> 4.9/5 Rating</span>
            </div>
          </div>

          <!-- Col 2: Khám phá EduLink -->
          <div class="footer-col">
            <h4 class="footer-col-title">Khám phá EduLink</h4>
            <ul class="footer-links-list">
              <li><router-link to="/giao-vien">Tìm gia sư 1:1</router-link></li>
              <li><router-link to="/my-classes">Khóa học luyện thi</router-link></li>
              <li><router-link to="/my-schedule">Quy trình ghép lớp</router-link></li>
              <li><router-link to="/thanh-toan">Bảng giá học phí</router-link></li>
            </ul>
          </div>

          <!-- Col 3: Công nghệ & An toàn -->
          <div class="footer-col">
            <h4 class="footer-col-title">Công nghệ & An toàn</h4>
            <ul class="footer-links-list">
              <li><router-link to="/face-id">Cơ chế Face ID</router-link></li>
              <li><router-link to="/thanh-toan">Chính sách hoàn tiền 100%</router-link></li>
              <li><router-link to="/face-id">Bảo mật sinh trắc học</router-link></li>
              <li><router-link to="/ho-so-giang-vien">Kiểm định gia sư</router-link></li>
            </ul>
          </div>

          <!-- Col 4: Tải EduLink App -->
          <div class="footer-col">
            <h4 class="footer-col-title">Tải EduLink App</h4>
            <p class="footer-app-desc">
              Nhận thông báo lịch học, điểm danh Face ID nhanh chóng trên điện thoại.
            </p>
            <div class="app-download-buttons mt-3">
              <a href="javascript:void(0)" class="btn-app-store" @click="handleAppStore">
                <i class="fa-brands fa-apple app-icon"></i>
                <div class="app-text">
                  <span class="app-sub">Tải trên</span>
                  <span class="app-main">App Store (iOS)</span>
                </div>
              </a>
              <a href="javascript:void(0)" class="btn-app-store mt-2" @click="handleGooglePlay">
                <i class="fa-brands fa-google-play app-icon"></i>
                <div class="app-text">
                  <span class="app-sub">Tải trên</span>
                  <span class="app-main">Google Play</span>
                </div>
              </a>
            </div>
          </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="footer-bottom-bar">
          <div class="copy-text">
            © 2025 EduLink Vietnam. Một sản phẩm công nghệ giáo dục chất lượng cao của SmartTrial.
          </div>
          <div class="footer-contact-links">
            <span><i class="fa-regular fa-envelope me-1"></i> hotro@edulink.vn</span>
            <span class="contact-sep">•</span>
            <span><i class="fa-solid fa-phone me-1"></i> 1900 8899</span>
          </div>
        </div>
      </div>
    </footer>

    <!-- MODAL: ĐẶT LỊCH HỌC THỬ NHANH -->
    <div v-if="showTrialModal" class="custom-modal-backdrop" @click.self="closeTrialModal">
      <div class="custom-modal-card">
        <div class="modal-card-header">
          <div class="d-flex align-items-center gap-2">
            <div class="modal-icon-wrap">
              <i class="fa-solid fa-calendar-check text-primary"></i>
            </div>
            <div>
              <h3 class="modal-title mb-0">Đăng ký Học thử Miễn phí 100%</h3>
              <div class="modal-subtitle">EduLink kết nối gia sư phù hợp trong 24 giờ</div>
            </div>
          </div>
          <button class="btn-close-modal" @click="closeTrialModal">&times;</button>
        </div>

        <div class="modal-card-body">
          <div class="alert alert-info py-2 px-3 fs-8 mb-3">
            <i class="fa-solid fa-circle-info me-1"></i> Bạn đang quan tâm: <strong>{{ selectedProgram }}</strong>
          </div>

          <div class="form-group mb-3">
            <label class="form-label fs-8 fw-bold">Họ và tên học sinh / Phụ huynh:</label>
            <input type="text" v-model="trialForm.name" class="form-control" placeholder="Ví dụ: Nguyễn Linh Lan" />
          </div>

          <div class="form-group mb-3">
            <label class="form-label fs-8 fw-bold">Số điện thoại / Zalo nhận lịch:</label>
            <input type="tel" v-model="trialForm.phone" class="form-control" placeholder="Ví dụ: 0908 123 456" />
          </div>

          <div class="form-group mb-3">
            <label class="form-label fs-8 fw-bold">Hình thức học mong muốn:</label>
            <div class="d-flex gap-3">
              <label class="form-check-label d-flex align-items-center gap-1">
                <input type="radio" value="online" v-model="trialForm.mode" /> Online (Face ID)
              </label>
              <label class="form-check-label d-flex align-items-center gap-1">
                <input type="radio" value="offline" v-model="trialForm.mode" /> Học trực tiếp tại cơ sở
              </label>
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="form-label fs-8 fw-bold">Mục tiêu điểm số / Cần cải thiện:</label>
            <textarea v-model="trialForm.note" class="form-control" rows="2" placeholder="Ví dụ: Lấy lại gốc Toán hình học 12, nâng band IELTS từ 5.5 lên 7.0..."></textarea>
          </div>
        </div>

        <div class="modal-card-footer">
          <button class="btn btn-secondary btn-sm" @click="closeTrialModal">Hủy bỏ</button>
          <button class="btn btn-primary btn-sm px-3" @click="submitTrialBooking">
            <i class="fa-solid fa-check me-1"></i> Xác nhận giữ chỗ học thử
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LandingPage",
  data() {
    return {
      searchSubject: "Toán học (Lớp 1 - 12)",
      searchLocation: "Quận 1 / Trực tuyến",
      searchTimeSlot: "Tối (18:00 - 21:00)",
      showTrialModal: false,
      selectedProgram: "Chương trình Học thử EduLink",
      trialForm: {
        name: "",
        phone: "",
        mode: "online",
        note: ""
      }
    };
  },
  methods: {
    handleSearchTutor() {
      alert(`Đang tìm kiếm gia sư theo tiêu chí:\n- Môn: ${this.searchSubject}\n- Khu vực: ${this.searchLocation}\n- Ca học: ${this.searchTimeSlot}\n\nHệ thống AI Smart Match đang tìm thấy 24 gia sư tương thích!`);
      this.$router.push("/my-schedule");
    },
    openTrialModal(programName) {
      this.selectedProgram = programName;
      this.showTrialModal = true;
    },
    closeTrialModal() {
      this.showTrialModal = false;
    },
    submitTrialBooking() {
      if (!this.trialForm.name || !this.trialForm.phone) {
        alert("Vui lòng nhập họ tên và số điện thoại liên hệ để nhận lịch học thử!");
        return;
      }
      alert(`🎉 Đăng ký thành công!\nCảm ơn bạn ${this.trialForm.name}.\nChuyên viên EduLink sẽ liên hệ qua SĐT ${this.trialForm.phone} trong 30 phút để kích hoạt tài khoản học thử miễn phí kèm Face ID!`);
      this.showTrialModal = false;
      this.trialForm = { name: "", phone: "", mode: "online", note: "" };
    },
    openFeedbackModal() {
      alert("Cảm ơn bạn đã quan tâm! Bạn có thể gửi đóng góp ý kiến trực tiếp qua email: hotro@edulink.vn hoặc hotline: 1900 8899.");
    },
    filterSubject(category) {
      alert("Đang hiển thị toàn bộ 24+ chuyên đề đào tạo từ Tiểu học đến Luyện thi Đại học Quốc gia!");
    },
    prevTutor() {
      alert("Đang hiển thị danh sách gia sư trước đó.");
    },
    nextTutor() {
      alert("Đang chuyển sang danh sách gia sư tiếp theo được đánh giá cao.");
    },
    handleAppStore() {
      alert("Ứng dụng EduLink iOS sẽ sớm có mặt trên App Store. Vui lòng trải nghiệm phiên bản Web App mượt mà!");
    },
    handleGooglePlay() {
      alert("Ứng dụng EduLink Android sẽ sớm có mặt trên Google Play. Vui lòng trải nghiệm phiên bản Web App mượt mà!");
    }
  }
};
</script>

<style scoped>
.edulink-landing-page {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #1e293b;
  background-color: #ffffff;
  min-height: 100vh;
}

/* 1. HEADER */
.main-header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.03);
}

.header-container {
  max-width: 1240px;
  margin: 0 auto;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  font-size: 22px;
  font-weight: 800;
  color: #0060d2;
  letter-spacing: -0.5px;
}

.logo-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, #0060d2 0%, #004fb0 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.brand-name span {
  color: #0f172a;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 22px;
}

.nav-item {
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 600;
  color: #475569;
  transition: color 0.15s ease;
}

.nav-item:hover {
  color: #0060d2;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-book-trial {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 13.5px;
  font-weight: 600;
  padding: 8px 18px;
  border-radius: 20px;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
  transition: all 0.15s ease;
}

.btn-book-trial:hover {
  background-color: #004fb0;
  border-color: #004fb0;
}

.user-avatar-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background-color: #f1f5f9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-size: 15px;
  transition: all 0.15s ease;
}

.user-avatar-btn:hover {
  background-color: #e2e8f0;
  color: #0060d2;
}

/* 2. HERO SECTION */
.hero-section {
  background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
  padding: 50px 20px 60px;
}

.hero-container {
  max-width: 1240px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 40px;
  align-items: center;
}

.hero-pill-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 12.5px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 20px;
  border: 1px solid #bfdbfe;
  margin-bottom: 20px;
}

.hero-title {
  font-size: 40px;
  font-weight: 800;
  line-height: 1.25;
  color: #0f172a;
  letter-spacing: -0.5px;
  margin-bottom: 18px;
}

.highlight-ai {
  color: #0060d2;
  position: relative;
}

.hero-desc {
  font-size: 15px;
  color: #475569;
  line-height: 1.65;
  margin-bottom: 28px;
}

/* Floating Search Bar */
.hero-search-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 20px 22px;
  box-shadow: 0 10px 30px rgba(0, 96, 210, 0.07);
}

.search-inputs-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr auto;
  gap: 12px;
  align-items: flex-end;
}

.filter-label {
  font-size: 11.5px;
  font-weight: 700;
  color: #64748b;
  margin-bottom: 6px;
  display: block;
}

.input-with-icon {
  position: relative;
}

.select-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 13px;
  pointer-events: none;
}

.filter-select {
  padding-left: 32px;
  font-size: 13px;
  border-radius: 10px;
  border-color: #cbd5e1;
  background-color: #f8fafc;
  height: 42px;
  font-weight: 500;
}

.filter-select:focus {
  background-color: #ffffff;
  border-color: #0060d2;
  box-shadow: 0 0 0 3px rgba(0, 96, 210, 0.15);
}

.btn-search-tutor {
  height: 42px;
  padding: 0 20px;
  border-radius: 10px;
  font-size: 13.5px;
  font-weight: 700;
  background-color: #0060d2;
  border-color: #0060d2;
  white-space: nowrap;
}

.search-footer-note {
  font-size: 12px;
  color: #64748b;
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px solid #f1f5f9;
}

/* 3 Stats Row */
.hero-stats-row {
  display: flex;
  align-items: center;
  gap: 28px;
  margin-top: 36px;
}

.stat-number {
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.1;
}

.star-gold {
  color: #eab308;
  font-size: 20px;
}

.stat-desc {
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
}

.stat-divider {
  width: 1px;
  height: 36px;
  background-color: #e2e8f0;
}

/* Hero Right: Tutor Floating Showcase Card */
.hero-content-right {
  display: flex;
  justify-content: center;
}

.hero-featured-card-wrap {
  position: relative;
  width: 100%;
  max-width: 440px;
}

.hero-tutor-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
}

.tutor-header-row {
  display: flex;
  gap: 16px;
}

.tutor-card-avatar {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  object-fit: cover;
  border: 2.5px solid #0060d2;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.tutor-header-meta {
  flex: 1;
}

.tutor-name-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.tutor-card-name {
  font-size: 17px;
  font-weight: 700;
  color: #0f172a;
}

.badge-online-ready {
  background-color: #ecfdf5;
  color: #059669;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 12px;
  border: 1px solid #a7f3d0;
}

.tutor-card-role {
  font-size: 12px;
  color: #64748b;
  margin: 3px 0 6px;
}

.tutor-card-rating {
  font-size: 12px;
}

.meta-dot {
  margin: 0 4px;
  color: #cbd5e1;
}

.tutor-slot-box {
  background-color: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 12px;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
}

.btn-view-cal {
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 8px;
  text-decoration: none;
}

.tutor-cert-row {
  background-color: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 10px;
  padding: 10px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  color: #166534;
  font-weight: 600;
}

.badge-match-100 {
  background-color: #15803d;
  color: #ffffff;
  font-size: 10.5px;
  padding: 2px 8px;
  border-radius: 10px;
}

.floating-match-pill {
  position: absolute;
  bottom: -18px;
  right: 16px;
  background-color: #0f172a;
  color: #ffffff;
  font-size: 12px;
  padding: 8px 16px;
  border-radius: 30px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  gap: 8px;
}

.pulsing-green-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.4);
}

/* 3. FEATURES SECTION */
.section-features {
  padding: 80px 20px;
  background-color: #ffffff;
}

.section-container {
  max-width: 1240px;
  margin: 0 auto;
}

.section-tag {
  font-size: 11.5px;
  font-weight: 800;
  letter-spacing: 1px;
  color: #0060d2;
  margin-bottom: 10px;
}

.section-heading {
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.5px;
  margin-bottom: 12px;
}

.section-sub {
  font-size: 14.5px;
  color: #64748b;
  max-width: 680px;
  margin: 0 auto 48px;
  line-height: 1.6;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  text-align: left;
}

.feature-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 26px 22px;
  display: flex;
  flex-direction: column;
  transition: all 0.2s ease;
}

.feature-card:hover {
  transform: translateY(-4px);
  border-color: #cbd5e1;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.05);
}

.feature-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-bottom: 18px;
}

.icon-bg-blue {
  background-color: #eff6ff;
  color: #0060d2;
}

.icon-bg-green {
  background-color: #ecfdf5;
  color: #059669;
}

.icon-bg-purple {
  background-color: #f3e8ff;
  color: #7e22ce;
}

.icon-bg-orange {
  background-color: #fff7ed;
  color: #ea580c;
}

.feature-title {
  font-size: 16.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 10px;
}

.feature-desc {
  font-size: 13px;
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 18px;
  flex-grow: 1;
}

.feature-link {
  font-size: 12.5px;
  font-weight: 600;
  color: #0060d2;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}

.feature-link:hover {
  text-decoration: underline;
}

/* 4. SUBJECTS SECTION */
.section-subjects {
  padding: 80px 20px;
  background-color: #f8fafc;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
}

.subjects-header-flex {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
}

.view-all-link {
  font-size: 13.5px;
  font-weight: 600;
  color: #0060d2;
  text-decoration: none;
}

.view-all-link:hover {
  text-decoration: underline;
}

.subjects-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 22px;
}

.subject-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 22px;
  display: flex;
  flex-direction: column;
  transition: all 0.2s ease;
}

.subject-card:hover {
  border-color: #bfdbfe;
  box-shadow: 0 8px 24px rgba(0, 96, 210, 0.05);
}

.subj-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.subj-icon-circle {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
}

.icon-blue { background-color: #eff6ff; color: #0060d2; }
.icon-green { background-color: #ecfdf5; color: #059669; }
.icon-orange { background-color: #fff7ed; color: #ea580c; }
.icon-purple { background-color: #f3e8ff; color: #7e22ce; }
.icon-teal { background-color: #f0fdfa; color: #0d9488; }
.icon-amber { background-color: #fefce8; color: #ca8a04; }

.subj-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
}

.badge-popular { background-color: #eff6ff; color: #0060d2; }
.badge-deal { background-color: #ecfdf5; color: #059669; }
.badge-score { background-color: #fff7ed; color: #ea580c; }
.badge-future { background-color: #f3e8ff; color: #7e22ce; }
.badge-high { background-color: #f0fdfa; color: #0d9488; }
.badge-expert { background-color: #fefce8; color: #ca8a04; }

.subj-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 8px;
}

.subj-desc {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.55;
  margin-bottom: 18px;
  flex-grow: 1;
}

.subj-footer-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
}

.price-sub {
  font-size: 11px;
  color: #94a3b8;
  display: block;
}

.price-val {
  font-size: 16px;
  font-weight: 800;
  color: #0060d2;
}

.price-unit {
  font-size: 11.5px;
  color: #64748b;
}

.btn-circle-arrow {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid #cbd5e1;
  background-color: #ffffff;
  color: #334155;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-circle-arrow:hover {
  background-color: #0060d2;
  border-color: #0060d2;
  color: #ffffff;
}

/* 5. TUTORS SHOWCASE SECTION */
.section-tutors {
  padding: 80px 20px;
  background-color: #ffffff;
}

.tutors-header-flex {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
}

.carousel-nav-btns {
  display: flex;
  gap: 8px;
}

.btn-nav-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid #cbd5e1;
  background-color: #ffffff;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-nav-circle:hover {
  background-color: #f1f5f9;
  color: #0f172a;
}

.tutors-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 26px;
}

.tutor-showcase-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  transition: all 0.2s ease;
}

.tutor-showcase-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.tutor-cover-wrap {
  position: relative;
  height: 200px;
  background-color: #f1f5f9;
}

.tutor-cover-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tutor-badge-tag {
  position: absolute;
  top: 14px;
  left: 14px;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
}

.tag-verified {
  background-color: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
}

.tag-tech {
  background-color: #f3e8ff;
  color: #7e22ce;
  border: 1px solid #d8b4fe;
}

.tutor-mode-tag {
  position: absolute;
  top: 14px;
  right: 14px;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
}

.tag-blue { background-color: #0060d2; color: #ffffff; }
.tag-green { background-color: #10b981; color: #ffffff; }
.tag-purple { background-color: #8b5cf6; color: #ffffff; }

.tutor-card-body {
  padding: 20px;
}

.tutor-card-name-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
}

.tutor-main-name {
  font-size: 16.5px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.tutor-star-rating {
  font-size: 12px;
}

.star-score {
  font-weight: 700;
  color: #0f172a;
  margin-left: 3px;
}

.review-count {
  color: #94a3b8;
  font-size: 11px;
}

.tutor-short-bio {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.55;
  margin-bottom: 14px;
  min-height: 38px;
}

.tutor-skill-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 18px;
}

.skill-pill {
  font-size: 11px;
  font-weight: 600;
  background-color: #f1f5f9;
  color: #475569;
  padding: 3px 8px;
  border-radius: 6px;
}

.tutor-card-action-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
}

.price-prefix {
  font-size: 11px;
  color: #94a3b8;
  margin-right: 4px;
}

.price-number {
  font-size: 15px;
  font-weight: 800;
  color: #0060d2;
}

.price-suffix {
  font-size: 11px;
  color: #64748b;
}

.btn-book-tutor {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 12.5px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 8px;
}

/* 6. PROCESS SECTION */
.section-process {
  padding: 80px 20px;
  background-color: #f8fafc;
}

.process-steps-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.step-item {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 28px 20px;
  text-align: center;
}

.step-circle-icon {
  position: relative;
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background-color: #eff6ff;
  color: #0060d2;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  margin: 0 auto 20px;
}

.step-number-pill {
  position: absolute;
  top: -4px;
  left: -4px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: #0060d2;
  color: #ffffff;
  font-size: 11.5px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #ffffff;
}

.step-title {
  font-size: 15.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 10px;
}

.step-desc {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.55;
  margin: 0;
}

/* 7. TESTIMONIALS SECTION */
.section-testimonials {
  padding: 80px 20px;
  background-color: #ffffff;
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  text-align: left;
}

.testimonial-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 26px 22px;
  display: flex;
  flex-direction: column;
}

.testimonial-stars {
  color: #eab308;
  font-size: 13px;
  margin-bottom: 14px;
}

.testimonial-quote {
  font-size: 13px;
  color: #334155;
  line-height: 1.65;
  font-style: italic;
  margin-bottom: 20px;
  flex-grow: 1;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 12px;
  border-top: 1px solid #e2e8f0;
  padding-top: 14px;
}

.author-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
}

.author-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.author-role {
  font-size: 11.5px;
  color: #64748b;
}

/* 8. CTA SECTION */
.section-cta {
  padding: 40px 20px 80px;
  background-color: #ffffff;
}

.cta-banner-container {
  max-width: 1240px;
  margin: 0 auto;
  background: linear-gradient(135deg, #0060d2 0%, #00459c 100%);
  border-radius: 24px;
  padding: 48px 40px;
  text-align: center;
  color: #ffffff;
  box-shadow: 0 16px 40px rgba(0, 96, 210, 0.2);
}

.cta-badge {
  display: inline-flex;
  align-items: center;
  background-color: rgba(255, 255, 255, 0.15);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 18px;
}

.cta-title {
  font-size: 30px;
  font-weight: 800;
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.cta-sub {
  font-size: 14.5px;
  max-width: 660px;
  margin: 0 auto 28px;
  opacity: 0.9;
  line-height: 1.6;
}

.cta-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
}

.btn-cta-white {
  background-color: #ffffff;
  color: #0060d2;
  font-size: 14px;
  font-weight: 700;
  padding: 10px 24px;
  border-radius: 12px;
  border: none;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
  transition: all 0.15s ease;
}

.btn-cta-white:hover {
  background-color: #f1f5f9;
}

.btn-cta-outline {
  background-color: transparent;
  color: #ffffff;
  border: 1.5px solid rgba(255, 255, 255, 0.6);
  font-size: 14px;
  font-weight: 700;
  padding: 9px 24px;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.15s ease;
}

.btn-cta-outline:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-color: #ffffff;
  color: #ffffff;
}

/* 9. FOOTER */
.main-footer {
  background-color: #ffffff;
  border-top: 1px solid #e2e8f0;
  padding: 60px 20px 28px;
}

.footer-container {
  max-width: 1240px;
  margin: 0 auto;
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
  gap: 40px;
  margin-bottom: 40px;
}

.footer-brand-desc {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.6;
  max-width: 320px;
}

.footer-trust-badges {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.badge-trust {
  font-size: 11px;
  font-weight: 600;
  background-color: #f8fafc;
  color: #334155;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.footer-col-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 16px;
}

.footer-links-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-links-list a {
  font-size: 12.5px;
  color: #64748b;
  text-decoration: none;
  transition: color 0.15s ease;
}

.footer-links-list a:hover {
  color: #0060d2;
}

.footer-app-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.5;
}

.btn-app-store {
  display: flex;
  align-items: center;
  gap: 12px;
  background-color: #0f172a;
  color: #ffffff;
  padding: 8px 16px;
  border-radius: 10px;
  text-decoration: none;
  transition: opacity 0.15s ease;
  width: fit-content;
}

.btn-app-store:hover {
  opacity: 0.9;
  color: #ffffff;
}

.app-icon {
  font-size: 22px;
}

.app-text {
  display: flex;
  flex-direction: column;
}

.app-sub {
  font-size: 9.5px;
  opacity: 0.7;
  text-transform: uppercase;
}

.app-main {
  font-size: 12px;
  font-weight: 700;
}

.footer-bottom-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #f1f5f9;
  padding-top: 24px;
  font-size: 12px;
  color: #94a3b8;
  flex-wrap: wrap;
  gap: 12px;
}

.footer-contact-links {
  display: flex;
  align-items: center;
  gap: 12px;
}

.contact-sep {
  color: #cbd5e1;
}

/* MODAL */
.custom-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 20px;
}

.custom-modal-card {
  background-color: #ffffff;
  border-radius: 18px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
  overflow: hidden;
  animation: modalFadeIn 0.2s ease-out;
}

@keyframes modalFadeIn {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}

.modal-card-header {
  padding: 18px 24px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background-color: #eff6ff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}

.modal-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
}

.modal-subtitle {
  font-size: 12px;
  color: #64748b;
}

.btn-close-modal {
  background: none;
  border: none;
  font-size: 24px;
  color: #94a3b8;
  cursor: pointer;
}

.btn-close-modal:hover {
  color: #0f172a;
}

.modal-card-body {
  padding: 24px;
}

.modal-card-footer {
  padding: 14px 24px;
  border-top: 1px solid #f1f5f9;
  background-color: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
}

/* RESPONSIVE BREAKPOINTS */
@media (max-width: 1024px) {
  .hero-container {
    grid-template-columns: 1fr;
  }
  .features-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .subjects-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .tutors-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .process-steps-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .testimonials-grid {
    grid-template-columns: 1fr;
  }
  .footer-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 768px) {
  .nav-links {
    display: none;
  }
  .search-inputs-grid {
    grid-template-columns: 1fr;
  }
  .hero-stats-row {
    flex-wrap: wrap;
    gap: 16px;
  }
  .features-grid {
    grid-template-columns: 1fr;
  }
  .subjects-grid {
    grid-template-columns: 1fr;
  }
  .tutors-grid {
    grid-template-columns: 1fr;
  }
  .process-steps-grid {
    grid-template-columns: 1fr;
  }
  .footer-grid {
    grid-template-columns: 1fr;
  }
}
</style>
