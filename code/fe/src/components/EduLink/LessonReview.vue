<template>
  <div class="lesson-review-page">
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

        <!-- Nav links -->
        <nav class="nav-links">
          <router-link to="/giao-vien" class="nav-item">Tìm gia sư</router-link>
          <router-link to="/#subjects" class="nav-item">Môn học</router-link>
          <router-link to="/my-schedule" class="nav-item">Lịch rảnh & Ghép lớp</router-link>
          <router-link to="/face-id" class="nav-item">Tính năng Face ID</router-link>
          <router-link to="/thanh-toan" class="nav-item">Bảng giá học phí</router-link>
          <router-link to="/dang-ky" class="nav-item">Đăng ký làm gia sư</router-link>
          <router-link to="/dang-ky" class="nav-item">Đăng nhập</router-link>
        </nav>

        <!-- Actions -->
        <div class="header-actions">
          <router-link to="/my-schedule" class="btn btn-primary btn-start-trial">
            Bắt đầu học thử
          </router-link>
          <router-link to="/hoc-vien" class="user-avatar-btn" title="Hồ sơ học viên">
            <i class="fa-regular fa-user"></i>
          </router-link>
        </div>
      </div>
    </header>

    <!-- 2. MAIN CONTAINER -->
    <main class="review-main-content">
      <div class="review-container">
        <!-- Breadcrumbs -->
        <nav class="breadcrumb-nav">
          <i class="fa-solid fa-house me-1"></i>
          <router-link to="/" class="crumb-link">Trang chủ</router-link>
          <span class="crumb-sep">›</span>
          <router-link to="/my-classes" class="crumb-link">Quản lý học tập</router-link>
          <span class="crumb-sep">›</span>
          <span class="crumb-active">Đánh giá buổi học & Gia sư</span>
        </nav>

        <!-- Page Header Title & Reward Badge -->
        <div class="review-header-row">
          <div class="header-text-left">
            <div class="review-pill-tag">
              <i class="fa-regular fa-comments me-1"></i> Phản hồi chất lượng buổi học
            </div>
            <h1 class="review-page-title">Đánh giá & Góp ý chất lượng giảng dạy</h1>
            <p class="review-page-subtitle">
              Ý kiến đóng góp quý giá của bạn giúp EduLink liên tục nâng cao chất lượng gia sư và tối ưu trải nghiệm lớp học AI minh bạch.
            </p>
          </div>

          <!-- Reward Box (Right) -->
          <div class="reward-edupoints-card">
            <div class="reward-icon-circle">
              <i class="fa-solid fa-coins"></i>
            </div>
            <div class="reward-info">
              <div class="reward-sub">Phần thưởng đánh giá</div>
              <div class="reward-val">+50 Xu EduPoints</div>
            </div>
          </div>
        </div>

        <!-- 3. TWO-COLUMN LAYOUT -->
        <div class="review-layout-grid">
          <!-- LEFT COLUMN: FORM -->
          <div class="review-left-column">
            <!-- Card 1: Completed Lesson Details -->
            <div class="content-card lesson-info-card">
              <div class="lesson-top-meta">
                <span class="badge-mode-online">
                  <i class="fa-solid fa-video me-1"></i> Kèm 1:1 Trực tuyến Face ID
                </span>
                <span class="badge-completed">
                  <i class="fa-solid fa-circle-check me-1"></i> Buổi học đã hoàn thành
                </span>
              </div>

              <h2 class="lesson-title mt-2">
                Toán học nâng cao 301 - Chuyên đề Tích phân & Ứng dụng
              </h2>

              <!-- Teacher Info Row -->
              <div class="teacher-detail-row mt-3">
                <div class="teacher-avatar-wrap">
                  <img
                    src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=140&q=80"
                    alt="TS. Nguyễn Minh Triết"
                    class="teacher-avatar-img"
                  />
                  <div class="verified-faceid-check" title="Đã xác thực Face ID">
                    <i class="fa-solid fa-check"></i>
                  </div>
                </div>

                <div class="teacher-text-meta">
                  <div class="teacher-name-line">
                    <span class="teacher-name">TS. Nguyễn Minh Triết</span>
                    <span class="badge-verified-pill">
                      <span class="green-dot me-1"></span> Đã xác thực Face ID
                    </span>
                  </div>
                  <div class="teacher-affiliation">
                    Khoa Toán - Tin ĐH Bách Khoa TP.HCM • 9 năm kinh nghiệm
                  </div>
                </div>
              </div>

              <!-- Time and Attendance Specs -->
              <div class="lesson-specs-bar mt-3">
                <div class="spec-item">
                  <i class="fa-regular fa-clock text-primary me-2"></i>
                  <span>Thời gian thực tế: <strong>19:30 - 21:00 (90 phút)</strong> • Tối qua</span>
                </div>
                <div class="spec-item">
                  <i class="fa-solid fa-fingerprint text-success me-2"></i>
                  <span>Trạng thái điểm danh AI: <strong class="text-success">Face ID hoàn tất 100%</strong> • Đúng giờ</span>
                </div>
              </div>
            </div>

            <!-- Card 2: Rating Criteria & Feedback Form -->
            <div class="content-card rating-form-card mt-4">
              <div class="card-heading-wrap">
                <h3 class="card-section-title">Chấm điểm chất lượng buổi dạy</h3>
                <p class="card-section-desc">Chạm hoặc click vào các ngôi sao để xếp hạng từng tiêu chí cụ thể</p>
              </div>

              <!-- 4 Criteria Rows -->
              <div class="criteria-list-wrap mt-3">
                <!-- Criterion 1 -->
                <div class="criterion-item">
                  <div class="criterion-left">
                    <div class="crit-icon-box icon-blue">
                      <i class="fa-solid fa-book-bookmark"></i>
                    </div>
                    <div>
                      <div class="crit-title">Chất lượng chuyên môn & Sự am hiểu bài giảng</div>
                      <div class="crit-desc">Kiến thức chuyên sâu, giải đáp bài tập rõ ràng</div>
                    </div>
                  </div>

                  <div class="criterion-stars-wrap">
                    <div class="star-rating-stars">
                      <i
                        v-for="star in 5"
                        :key="star"
                        class="fa-star"
                        :class="[star <= ratings.expertise ? 'fa-solid text-warning' : 'fa-regular text-muted']"
                        @click="setRating('expertise', star)"
                        @mouseover="hoverRating('expertise', star)"
                        @mouseleave="resetHover('expertise')"
                      ></i>
                    </div>
                    <span class="crit-score-num">{{ ratings.expertise.toFixed(1) }}</span>
                  </div>
                </div>

                <!-- Criterion 2 -->
                <div class="criterion-item">
                  <div class="criterion-left">
                    <div class="crit-icon-box icon-green">
                      <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <div>
                      <div class="crit-title">Phương pháp truyền đạt & Tương tác gợi mở</div>
                      <div class="crit-desc">Lắng nghe, kích thích tư duy, tương tác chủ động</div>
                    </div>
                  </div>

                  <div class="criterion-stars-wrap">
                    <div class="star-rating-stars">
                      <i
                        v-for="star in 5"
                        :key="star"
                        class="fa-star"
                        :class="[star <= ratings.pedagogy ? 'fa-solid text-warning' : 'fa-regular text-muted']"
                        @click="setRating('pedagogy', star)"
                        @mouseover="hoverRating('pedagogy', star)"
                        @mouseleave="resetHover('pedagogy')"
                      ></i>
                    </div>
                    <span class="crit-score-num">{{ ratings.pedagogy.toFixed(1) }}</span>
                  </div>
                </div>

                <!-- Criterion 3 -->
                <div class="criterion-item">
                  <div class="criterion-left">
                    <div class="crit-icon-box icon-orange">
                      <i class="fa-regular fa-clock"></i>
                    </div>
                    <div>
                      <div class="crit-title">Đúng giờ & Tinh thần trách nhiệm</div>
                      <div class="crit-desc">Vào lớp đúng giờ, bảo đảm đủ 90 phút giảng dạy</div>
                    </div>
                  </div>

                  <div class="criterion-stars-wrap">
                    <div class="star-rating-stars">
                      <i
                        v-for="star in 5"
                        :key="star"
                        class="fa-star"
                        :class="[star <= ratings.punctuality ? 'fa-solid text-warning' : 'fa-regular text-muted']"
                        @click="setRating('punctuality', star)"
                        @mouseover="hoverRating('punctuality', star)"
                        @mouseleave="resetHover('punctuality')"
                      ></i>
                    </div>
                    <span class="crit-score-num">{{ ratings.punctuality.toFixed(1) }}</span>
                  </div>
                </div>

                <!-- Criterion 4 -->
                <div class="criterion-item">
                  <div class="criterion-left">
                    <div class="crit-icon-box icon-purple">
                      <i class="fa-solid fa-fingerprint"></i>
                    </div>
                    <div>
                      <div class="crit-title">Chất lượng âm thanh, hình ảnh & Face ID</div>
                      <div class="crit-desc">Tín hiệu mạng mượt mà, camera nét, điểm danh chuẩn xác</div>
                    </div>
                  </div>

                  <div class="criterion-stars-wrap">
                    <div class="star-rating-stars">
                      <i
                        v-for="star in 5"
                        :key="star"
                        class="fa-star"
                        :class="[star <= ratings.technical ? 'fa-solid text-warning' : 'fa-regular text-muted']"
                        @click="setRating('technical', star)"
                        @mouseover="hoverRating('technical', star)"
                        @mouseleave="resetHover('technical')"
                      ></i>
                    </div>
                    <span class="crit-score-num">{{ ratings.technical.toFixed(1) }}</span>
                  </div>
                </div>
              </div>

              <!-- Quick Tags Section -->
              <div class="quick-tags-section mt-4">
                <label class="tags-label">Chọn nhanh cảm nhận buổi học:</label>
                <div class="quick-tags-flex mt-2">
                  <button
                    v-for="tag in availableTags"
                    :key="tag.text"
                    type="button"
                    class="btn-quick-tag"
                    :class="{ active: isTagSelected(tag.text) }"
                    @click="toggleTag(tag.text)"
                  >
                    <span class="tag-icon me-1">{{ tag.icon }}</span> {{ tag.text }}
                  </button>
                </div>
              </div>

              <!-- Detailed Text Comment -->
              <div class="comment-textarea-wrap mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="form-label mb-0 fw-bold fs-7">Nhận xét chi tiết (Góp ý để cải thiện)</label>
                  <span class="char-count" :class="{ 'text-danger': comment.length > 500 }">
                    {{ comment.length }} / 500 ký tự (Tối thiểu 20 ký tự)
                  </span>
                </div>
                <textarea
                  v-model="comment"
                  class="form-control review-textarea"
                  rows="4"
                  placeholder="Chia sẻ chi tiết cảm nhận của bạn về phương pháp giảng bài, sự tương tác của thầy Triết hoặc điểm cần bổ sung trong buổi tiếp theo..."
                ></textarea>

                <!-- Attach Images Option -->
                <div class="attach-images-row mt-3">
                  <button type="button" class="btn btn-outline-secondary btn-sm btn-attach" @click="triggerUpload">
                    <i class="fa-regular fa-image me-1"></i> Đính kèm hình ảnh vở bài tập / ghi chú (Tối đa 3 ảnh)
                  </button>
                  <span class="upload-note">Định dạng JPG, PNG &lt; 5MB</span>
                  <input type="file" ref="fileInput" multiple accept="image/*" class="d-none" @change="handleFileChange" />
                </div>

                <!-- Preview Uploaded Files -->
                <div v-if="uploadedFiles.length > 0" class="uploaded-previews mt-2">
                  <div v-for="(file, idx) in uploadedFiles" :key="idx" class="preview-badge">
                    <i class="fa-regular fa-file-image me-1"></i> {{ file.name }}
                    <span class="remove-file-btn" @click="removeFile(idx)">&times;</span>
                  </div>
                </div>
              </div>

              <!-- Anonymous Option & Security Banner -->
              <div class="security-options-box mt-4">
                <label class="form-check d-flex align-items-start gap-2 mb-2">
                  <input type="checkbox" v-model="isAnonymous" class="form-check-input mt-1" />
                  <div>
                    <span class="fw-bold fs-7">Đánh giá ẩn danh</span>
                    <div class="text-muted fs-8">
                      Chỉ hiển thị tên viết tắt (ví dụ: N.L.L) đối với gia sư. Thông tin cá nhân vẫn được bảo lưu trong hệ thống kiểm định EduLink.
                    </div>
                  </div>
                </label>

                <div class="confidentiality-notice">
                  <i class="fa-solid fa-shield-halved text-success me-2 fs-6"></i>
                  <span>EduLink cam kết bảo mật & đảm bảo tính khách quan, tuyệt đối không ảnh hưởng tới tiến trình học tập của bạn.</span>
                </div>
              </div>

              <!-- Footer Buttons -->
              <div class="review-form-footer mt-4">
                <button type="button" class="btn btn-link btn-remind-later text-secondary" @click="handleRemindLater">
                  Để sau (Nhắc tôi trong 24h tới)
                </button>
                <button type="button" class="btn btn-primary btn-submit-review" @click="submitReview">
                  Gửi đánh giá & Nhận 50 EduPoints <i class="fa-solid fa-arrow-right ms-1"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN: SIDEBAR -->
          <aside class="review-right-column">
            <!-- Card 1: Tutor Reputation & Stats -->
            <div class="content-card tutor-reputation-card">
              <div class="reputation-top-line">
                <h3 class="card-section-title fs-6 mb-0">Uy tín gia sư</h3>
                <span class="badge-top-percent">
                  <i class="fa-solid fa-trophy me-1"></i> Top 1% tháng 10
                </span>
              </div>

              <div class="rating-overview-flex mt-3">
                <div class="big-rating-box">
                  <div class="big-rating-val">4.9</div>
                  <div class="big-rating-stars">
                    <i class="fa-solid fa-star text-warning" v-for="i in 5" :key="i"></i>
                  </div>
                  <div class="rating-total-reviews">128 đánh giá</div>
                </div>

                <div class="stars-breakdown-bars">
                  <div class="bar-row">
                    <span class="star-label">5 sao</span>
                    <div class="progress custom-breakdown-bar">
                      <div class="progress-bar bg-success" style="width: 92%"></div>
                    </div>
                    <span class="pct-label">92%</span>
                  </div>
                  <div class="bar-row">
                    <span class="star-label">4 sao</span>
                    <div class="progress custom-breakdown-bar">
                      <div class="progress-bar bg-primary" style="width: 6%"></div>
                    </div>
                    <span class="pct-label">6%</span>
                  </div>
                  <div class="bar-row">
                    <span class="star-label">3 sao</span>
                    <div class="progress custom-breakdown-bar">
                      <div class="progress-bar bg-warning" style="width: 2%"></div>
                    </div>
                    <span class="pct-label">2%</span>
                  </div>
                  <div class="bar-row">
                    <span class="star-label">2 sao</span>
                    <div class="progress custom-breakdown-bar">
                      <div class="progress-bar bg-secondary" style="width: 0%"></div>
                    </div>
                    <span class="pct-label">0%</span>
                  </div>
                  <div class="bar-row">
                    <span class="star-label">1 sao</span>
                    <div class="progress custom-breakdown-bar">
                      <div class="progress-bar bg-secondary" style="width: 0%"></div>
                    </div>
                    <span class="pct-label">0%</span>
                  </div>
                </div>
              </div>

              <div class="trust-pill-grid mt-4">
                <div class="trust-badge-box">
                  <i class="fa-solid fa-shield-check text-primary fs-5 mb-1"></i>
                  <div class="trust-main">100% Bằng cấp</div>
                  <div class="trust-sub">Kiểm định bởi ĐHQG</div>
                </div>
                <div class="trust-badge-box">
                  <i class="fa-regular fa-clock text-success fs-5 mb-1"></i>
                  <div class="trust-main">100% Giờ dạy</div>
                  <div class="trust-sub">Chấm công Face ID</div>
                </div>
              </div>
            </div>

            <!-- Card 2: Actual Reviews from Students -->
            <div class="content-card student-reviews-sidebar mt-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="card-section-title fs-6 mb-0">Đánh giá thực tế từ học viên</h3>
                <a href="javascript:void(0)" class="view-all-reviews-link" @click="viewAllReviews">Xem tất cả</a>
              </div>

              <!-- Review 1: Nguyễn Linh Lan -->
              <div class="sidebar-review-item">
                <div class="review-author-row">
                  <img
                    src="https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=100&q=80"
                    alt="Nguyễn Linh Lan"
                    class="author-avatar"
                  />
                  <div class="author-meta-info">
                    <div class="author-name-line">
                      <span class="author-name">Nguyễn Linh Lan</span>
                      <i class="fa-solid fa-circle-check text-primary fs-8 ms-1"></i>
                    </div>
                    <div class="author-sub">Lớp 12A1 • 2 ngày trước</div>
                  </div>
                  <div class="author-rating-stars ms-auto">
                    <i class="fa-solid fa-star text-warning" v-for="i in 5" :key="i"></i>
                  </div>
                </div>

                <p class="review-body-text mt-2">
                  "Thầy Triết giải thích phần đồ họa không gian 3D rất trực quan, nhờ thầy mà em hiểu trọn vẹn phần tích phân tính thể tích khối tròn xoay!"
                </p>

                <!-- Tutor Reply Box -->
                <div class="tutor-reply-card mt-2">
                  <div class="tutor-reply-header">
                    <i class="fa-solid fa-reply me-1 text-primary"></i>
                    <strong>TS. Nguyễn Minh Triết (Phản hồi):</strong>
                  </div>
                  <div class="tutor-reply-text">
                    "Cảm ơn em Lan, tinh thần tự học của em rất tốt. Buổi sau thầy sẽ nâng cao thêm dạng đề thi ĐGNL nhé!"
                  </div>
                </div>
              </div>

              <!-- Review 2: Trần Quang Huy -->
              <div class="sidebar-review-item mt-3 pt-3 border-top">
                <div class="review-author-row">
                  <img
                    src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=100&q=80"
                    alt="Trần Quang Huy"
                    class="author-avatar"
                  />
                  <div class="author-meta-info">
                    <div class="author-name-line">
                      <span class="author-name">Trần Quang Huy</span>
                      <i class="fa-solid fa-circle-check text-primary fs-8 ms-1"></i>
                    </div>
                    <div class="author-sub">Lớp 12 Chuyên Tin • 5 ngày trước</div>
                  </div>
                  <div class="author-rating-stars ms-auto">
                    <i class="fa-solid fa-star text-warning" v-for="i in 5" :key="i"></i>
                  </div>
                </div>

                <p class="review-body-text mt-2">
                  "Rất thích tính năng vào lớp Face ID 3 giây là bắt đầu học ngay, thầy dạy tập trung, không lan man và sửa chi tiết từng bài thi thử."
                </p>

                <div class="review-hashtags-list mt-2">
                  <span class="review-hashtag">#DungGio</span>
                  <span class="review-hashtag">#MinhHoaTrucQuan</span>
                </div>
              </div>
            </div>

            <!-- Card 3: 100% Transparency Commitment -->
            <div class="content-card transparency-commitment-card mt-4">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fa-solid fa-shield-halved text-primary fs-5"></i>
                <h4 class="transparency-title mb-0">Cam kết minh bạch 100%</h4>
              </div>
              <p class="transparency-desc">
                Mọi đánh giá trên EduLink đều bắt buộc xác thực phiên học thành công qua công nghệ sinh trắc học Face ID. Hệ thống loại bỏ hoàn toàn các đánh giá ảo, tự tạo hoặc gian lận điểm số.
              </p>
              <a href="javascript:void(0)" class="transparency-link" @click="showPolicyModal">
                Tìm hiểu quy trình kiểm duyệt đánh giá <i class="fa-solid fa-arrow-right ms-1"></i>
              </a>
            </div>
          </aside>
        </div>
      </div>
    </main>

    <!-- 4. FOOTER -->
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
              Nền tảng EdTech tiên phong kết nối gia sư và học sinh chuẩn chất lượng cao. Ứng dụng công nghệ Face ID chấm công minh bạch và thuật toán AI tối ưu lịch học tự động.
            </p>
            <div class="footer-trust-badges mt-3">
              <span class="badge-trust"><i class="fa-solid fa-shield-halved text-success me-1"></i> Face ID Verified</span>
              <span class="badge-trust"><i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Smart Match</span>
            </div>
          </div>

          <!-- Col 2: Khám phá EduLink -->
          <div class="footer-col">
            <h4 class="footer-col-title">Khám phá EduLink</h4>
            <ul class="footer-links-list">
              <li><router-link to="/giao-vien">Tìm gia sư 1:1</router-link></li>
              <li><router-link to="/#subjects">Khóa học & Môn học</router-link></li>
              <li><router-link to="/my-schedule">Ghép lớp thông minh</router-link></li>
              <li><router-link to="/thanh-toan">Bảng học phí chuẩn</router-link></li>
            </ul>
          </div>

          <!-- Col 3: Công nghệ & An toàn -->
          <div class="footer-col">
            <h4 class="footer-col-title">Công nghệ & An toàn</h4>
            <ul class="footer-links-list">
              <li><router-link to="/face-id">Xác thực Face ID</router-link></li>
              <li><router-link to="/ho-so-giang-vien">Chính sách kiểm định gia sư</router-link></li>
              <li><router-link to="/face-id">Bảo mật sinh trắc học</router-link></li>
              <li><router-link to="/thanh-toan">Điều khoản dịch vụ</router-link></li>
            </ul>
          </div>

          <!-- Col 4: Tải EduLink App -->
          <div class="footer-col">
            <h4 class="footer-col-title">Tải EduLink App</h4>
            <p class="footer-app-desc">
              Nhận thông báo lịch học, điểm danh khuôn mặt và kết nối trực tuyến nhanh chóng.
            </p>
            <div class="app-download-buttons mt-3">
              <a href="javascript:void(0)" class="btn-app-store" @click="handleAppNotice('iOS')">
                <i class="fa-brands fa-apple app-icon"></i>
                <div class="app-text">
                  <span class="app-sub">Tải trên</span>
                  <span class="app-main">App Store (iOS)</span>
                </div>
              </a>
              <a href="javascript:void(0)" class="btn-app-store mt-2" @click="handleAppNotice('Android')">
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
            © 2025 EduLink Vietnam. Bảo lưu mọi quyền. Công nghệ Face ID độc quyền bảo vệ an toàn lớp học.
          </div>
          <div class="footer-contact-links">
            <span><i class="fa-regular fa-envelope me-1"></i> contact@edulink.vn</span>
            <span class="contact-sep">•</span>
            <span><i class="fa-solid fa-phone me-1"></i> 1900 8868</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: "LessonReview",
  data() {
    return {
      ratings: {
        expertise: 5,
        pedagogy: 5,
        punctuality: 5,
        technical: 5
      },
      hoveredRatings: {
        expertise: 0,
        pedagogy: 0,
        punctuality: 0,
        technical: 0
      },
      availableTags: [
        { icon: "👍", text: "Giảng dễ hiểu" },
        { icon: "💡", text: "Minh họa GeoGebra trực quan" },
        { icon: "💖", text: "Nhiệt tình & Kiên nhẫn" },
        { icon: "⏱", text: "Đúng giờ tuyệt đối" },
        { icon: "📝", text: "Bài tập thực tế sát đề thi" },
        { icon: "📢", text: "Cần nói chậm hơn" },
        { icon: "📚", text: "Bài tập về nhà hơi nhiều" }
      ],
      selectedTags: ["Giảng dễ hiểu", "Minh họa GeoGebra trực quan", "Đúng giờ tuyệt đối"],
      comment: "",
      uploadedFiles: [],
      isAnonymous: false
    };
  },
  methods: {
    setRating(field, val) {
      this.ratings[field] = val;
    },
    hoverRating(field, val) {
      this.hoveredRatings[field] = val;
    },
    resetHover(field) {
      this.hoveredRatings[field] = 0;
    },
    isTagSelected(tagText) {
      return this.selectedTags.includes(tagText);
    },
    toggleTag(tagText) {
      const idx = this.selectedTags.indexOf(tagText);
      if (idx > -1) {
        this.selectedTags.splice(idx, 1);
      } else {
        this.selectedTags.push(tagText);
      }
    },
    triggerUpload() {
      this.$refs.fileInput.click();
    },
    handleFileChange(e) {
      const files = Array.from(e.target.files);
      if (this.uploadedFiles.length + files.length > 3) {
        alert("Bạn chỉ được đính kèm tối đa 3 ảnh!");
        return;
      }
      this.uploadedFiles.push(...files);
    },
    removeFile(index) {
      this.uploadedFiles.splice(index, 1);
    },
    handleRemindLater() {
      alert("Hệ thống sẽ nhắc nhở bạn đánh giá lại buổi học này sau 24 giờ qua thông báo!");
      this.$router.push("/hoc-vien");
    },
    submitReview() {
      if (this.comment.trim().length > 0 && this.comment.trim().length < 20) {
        alert("Nhận xét chi tiết cần tối thiểu 20 ký tự để cung cấp thông tin hữu ích cho gia sư!");
        return;
      }

      alert(`🎉 Cảm ơn bạn đã gửi đánh giá!\n\nBạn đã nhận thành công +50 Xu EduPoints vào tài khoản học tập.\nÝ kiến của bạn đã được ghi nhận và xác thực qua Face ID an toàn!`);
      this.$router.push("/hoc-vien");
    },
    viewAllReviews() {
      alert("Đang hiển thị toàn bộ 128 đánh giá thực tế đã qua kiểm duyệt Face ID của TS. Nguyễn Minh Triết!");
    },
    showPolicyModal() {
      alert("Quy trình kiểm duyệt EduLink: 100% học viên đánh giá phải có log điểm danh Face ID hợp lệ và tham gia ít nhất 80% thời lượng buổi học.");
    },
    handleAppNotice(platform) {
      alert(`Ứng dụng EduLink trên ${platform} đang trong giai đoạn hoàn thiện. Vui lòng sử dụng trên trình duyệt web!`);
    }
  }
};
</script>

<style scoped>
.lesson-review-page {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #1e293b;
  background-color: #f8fafc;
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
  gap: 20px;
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

.btn-start-trial {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 13.5px;
  font-weight: 600;
  padding: 8px 18px;
  border-radius: 20px;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
  text-decoration: none;
  color: #ffffff;
}

.btn-start-trial:hover {
  background-color: #004fb0;
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

/* 2. MAIN CONTENT */
.review-main-content {
  padding: 28px 20px 60px;
}

.review-container {
  max-width: 1240px;
  margin: 0 auto;
}

/* Breadcrumbs */
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

/* Header row & Reward badge */
.review-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.review-pill-tag {
  display: inline-flex;
  align-items: center;
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 10px;
  border: 1px solid #bfdbfe;
}

.review-page-title {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 8px;
  letter-spacing: -0.4px;
}

.review-page-subtitle {
  font-size: 14px;
  color: #64748b;
  max-width: 680px;
  margin: 0;
  line-height: 1.55;
}

.reward-edupoints-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
  border: 1px solid #a7f3d0;
  border-radius: 16px;
  padding: 12px 20px;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
}

.reward-icon-circle {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background-color: #10b981;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
}

.reward-sub {
  font-size: 11.5px;
  font-weight: 600;
  color: #047857;
}

.reward-val {
  font-size: 18px;
  font-weight: 800;
  color: #059669;
}

/* 3. LAYOUT GRID */
.review-layout-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 24px;
  align-items: start;
}

.content-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}

/* CARD 1: LESSON DETAILS */
.lesson-top-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.badge-mode-online {
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #bfdbfe;
}

.badge-completed {
  color: #059669;
  font-size: 12px;
  font-weight: 700;
}

.lesson-title {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
}

.teacher-detail-row {
  display: flex;
  align-items: center;
  gap: 14px;
}

.teacher-avatar-wrap {
  position: relative;
  width: 54px;
  height: 54px;
}

.teacher-avatar-img {
  width: 54px;
  height: 54px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.verified-faceid-check {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background-color: #10b981;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  border: 2px solid #ffffff;
}

.teacher-name-line {
  display: flex;
  align-items: center;
  gap: 10px;
}

.teacher-name {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
}

.badge-verified-pill {
  background-color: #ecfdf5;
  color: #059669;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 12px;
  border: 1px solid #a7f3d0;
  display: inline-flex;
  align-items: center;
}

.green-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #10b981;
}

.teacher-affiliation {
  font-size: 12.5px;
  color: #64748b;
  margin-top: 2px;
}

.lesson-specs-bar {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12.5px;
  color: #475569;
  flex-wrap: wrap;
  gap: 10px;
}

/* CARD 2: RATING FORM */
.card-section-title {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
}

.card-section-desc {
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 0;
}

/* 4 Criteria list */
.criteria-list-wrap {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.criterion-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  transition: all 0.15s ease;
}

.criterion-item:hover {
  background-color: #f1f5f9;
}

.criterion-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.crit-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.icon-blue { background-color: #eff6ff; color: #0060d2; }
.icon-green { background-color: #ecfdf5; color: #059669; }
.icon-orange { background-color: #fff7ed; color: #ea580c; }
.icon-purple { background-color: #f3e8ff; color: #7e22ce; }

.crit-title {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.crit-desc {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.criterion-stars-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}

.star-rating-stars {
  display: flex;
  gap: 4px;
  font-size: 18px;
  cursor: pointer;
}

.crit-score-num {
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
  min-width: 28px;
  text-align: right;
}

/* Quick Tags */
.tags-label {
  font-size: 12.5px;
  font-weight: 700;
  color: #334155;
}

.quick-tags-flex {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.btn-quick-tag {
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: #334155;
  font-size: 12px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-quick-tag:hover {
  background-color: #e2e8f0;
}

.btn-quick-tag.active {
  background-color: #eff6ff;
  border-color: #0060d2;
  color: #0060d2;
  box-shadow: 0 2px 6px rgba(0, 96, 210, 0.15);
}

/* Comment Textarea */
.char-count {
  font-size: 11.5px;
  color: #64748b;
}

.review-textarea {
  font-size: 13px;
  border-radius: 12px;
  border-color: #cbd5e1;
  padding: 12px 14px;
}

.review-textarea:focus {
  border-color: #0060d2;
  box-shadow: 0 0 0 3px rgba(0, 96, 210, 0.15);
}

.attach-images-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 8px;
}

.btn-attach {
  font-size: 12px;
  font-weight: 600;
  border-radius: 8px;
}

.upload-note {
  font-size: 11.5px;
  color: #94a3b8;
}

.uploaded-previews {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.preview-badge {
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #0060d2;
  font-size: 11.5px;
  padding: 3px 8px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
}

.remove-file-btn {
  margin-left: 6px;
  cursor: pointer;
  font-weight: bold;
}

/* Security notice */
.security-options-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 16px;
}

.confidentiality-notice {
  display: flex;
  align-items: center;
  font-size: 11.5px;
  color: #475569;
  padding-top: 10px;
  margin-top: 10px;
  border-top: 1px solid #e2e8f0;
}

/* Footer buttons */
.review-form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.btn-remind-later {
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  padding: 0;
}

.btn-remind-later:hover {
  text-decoration: underline;
}

.btn-submit-review {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 13.5px;
  font-weight: 700;
  padding: 10px 22px;
  border-radius: 10px;
  box-shadow: 0 4px 14px rgba(0, 96, 210, 0.25);
  transition: all 0.15s ease;
}

.btn-submit-review:hover {
  background-color: #004fb0;
}

/* RIGHT SIDEBAR */
.reputation-top-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.badge-top-percent {
  background-color: #ecfdf5;
  color: #059669;
  font-size: 11.5px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
  border: 1px solid #a7f3d0;
}

.rating-overview-flex {
  display: flex;
  align-items: center;
  gap: 20px;
}

.big-rating-box {
  text-align: center;
  flex-shrink: 0;
}

.big-rating-val {
  font-size: 38px;
  font-weight: 900;
  color: #0f172a;
  line-height: 1;
}

.big-rating-stars {
  font-size: 13px;
  margin: 4px 0 2px;
}

.rating-total-reviews {
  font-size: 11px;
  color: #94a3b8;
}

.stars-breakdown-bars {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.bar-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: #64748b;
}

.star-label {
  width: 32px;
}

.custom-breakdown-bar {
  flex-grow: 1;
  height: 6px;
  border-radius: 4px;
  background-color: #f1f5f9;
}

.pct-label {
  width: 26px;
  text-align: right;
  font-weight: 600;
}

.trust-pill-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.trust-badge-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 12px;
  text-align: center;
}

.trust-main {
  font-size: 12px;
  font-weight: 700;
  color: #0f172a;
}

.trust-sub {
  font-size: 10.5px;
  color: #64748b;
}

/* Sidebar student reviews */
.view-all-reviews-link {
  font-size: 12px;
  font-weight: 600;
  color: #0060d2;
  text-decoration: none;
}

.view-all-reviews-link:hover {
  text-decoration: underline;
}

.sidebar-review-item {
  font-size: 12px;
}

.review-author-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.author-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
}

.author-name {
  font-size: 12.5px;
  font-weight: 700;
  color: #0f172a;
}

.author-sub {
  font-size: 11px;
  color: #94a3b8;
}

.author-rating-stars {
  font-size: 10px;
}

.review-body-text {
  font-size: 12px;
  color: #334155;
  line-height: 1.5;
  font-style: italic;
  margin: 0;
}

.tutor-reply-card {
  background-color: #eff6ff;
  border-radius: 10px;
  padding: 8px 12px;
  border: 1px solid #bfdbfe;
}

.tutor-reply-header {
  font-size: 11px;
  color: #0060d2;
}

.tutor-reply-text {
  font-size: 11.5px;
  color: #1e40af;
  margin-top: 2px;
}

.review-hashtags-list {
  display: flex;
  gap: 6px;
}

.review-hashtag {
  font-size: 11px;
  font-weight: 600;
  color: #0060d2;
  background-color: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
}

/* Transparency card */
.transparency-commitment-card {
  background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
  border-color: #bfdbfe;
}

.transparency-title {
  font-size: 13.5px;
  font-weight: 700;
  color: #0060d2;
}

.transparency-desc {
  font-size: 11.5px;
  color: #475569;
  line-height: 1.55;
  margin: 6px 0 10px;
}

.transparency-link {
  font-size: 12px;
  font-weight: 600;
  color: #0060d2;
  text-decoration: none;
}

.transparency-link:hover {
  text-decoration: underline;
}

/* 4. FOOTER */
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

/* RESPONSIVE */
@media (max-width: 992px) {
  .review-layout-grid {
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
  .criterion-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  .criterion-stars-wrap {
    width: 100%;
    justify-content: space-between;
  }
  .footer-grid {
    grid-template-columns: 1fr;
  }
}
</style>
