<template>
  <div class="edu-page">
    <main class="main-content">
      <div class="content-container">
        <!-- Tiêu đề trang -->
        <div class="page-title-area">
          <div class="title-left">
            <h1 class="page-title">Đăng Ký Lớp Học</h1>
            <p class="page-subtitle">Chọn môn học, xem lịch học hàng tuần và đăng ký lớp phù hợp mà không lo trùng giờ</p>
          </div>
          <div class="title-right d-flex gap-2">
            <button
              :class="['btn', activeViewTab === 'browse' ? 'btn-primary' : 'btn-outline-primary']"
              @click="activeViewTab = 'browse'"
            >
              <i class="fa-solid fa-list-check me-2"></i>Chọn & Đăng ký lớp
            </button>
            <button
              :class="['btn', activeViewTab === 'enrolled' ? 'btn-primary' : 'btn-outline-primary']"
              @click="activeViewTab = 'enrolled'"
            >
              <i class="fa-solid fa-graduation-cap me-2"></i>Lớp đã đăng ký ({{ myRegistrations.length }})
            </button>
          </div>
        </div>

        <!-- TAB 1: KHÁM PHÁ & ĐĂNG KÝ LỚP HỌC -->
        <div v-if="activeViewTab === 'browse'">
          <!-- BỘ LỌC MÔN HỌC DẠNG PILLS TRỰC QUAN -->
          <div class="filter-subject-section">
            <div class="section-label">
              <i class="fa-solid fa-book-open me-2 text-primary"></i>Chọn môn học cần đăng ký:
            </div>
            <div class="subject-pills-list">
              <button
                :class="['subject-pill-btn', { active: selectedMonHocId === '' }]"
                @click="selectMonHoc('')"
              >
                <span>Tất cả môn học</span>
                <span class="count-badge">{{ dsLopHoc.length }}</span>
              </button>
              <button
                v-for="mh in dsMonHoc"
                :key="mh.id"
                :class="['subject-pill-btn', { active: selectedMonHocId === mh.id }]"
                @click="selectMonHoc(mh.id)"
              >
                <span>{{ mh.ten_mon_hoc }}</span>
                <span class="count-badge">{{ countClassesBySubject(mh.id) }}</span>
              </button>
            </div>
          </div>

          <!-- THANH TÌM KIẾM & BỘ LỌC PHỤ -->
          <div class="secondary-filter-bar">
            <div class="filter-search-box">
              <i class="fa-solid fa-magnifying-glass search-icon"></i>
              <input
                type="text"
                class="search-input"
                placeholder="Tìm theo tên môn, giáo viên..."
                v-model="keyword"
              />
              <button v-if="keyword" class="clear-search-btn" @click="keyword = ''">&times;</button>
            </div>

            <div class="filter-dropdowns">
              <div class="filter-item">
                <label>Hình thức:</label>
                <select class="form-select-sm" v-model="filterHinhThuc">
                  <option value="">Tất cả hình thức</option>
                  <option value="online">Lớp Online (Google Meet)</option>
                  <option value="offline">Lớp Trực tiếp (Tại trung tâm)</option>
                </select>
              </div>

              <div class="filter-item">
                <label>Loại lớp:</label>
                <select class="form-select-sm" v-model="filterLoaiLop">
                  <option value="">Tất cả loại lớp</option>
                  <option value="dai_tra">Lớp đại trà</option>
                  <option value="kem">Lớp kèm</option>
                </select>
              </div>
            </div>
          </div>

          <!-- THÔNG TIN TỔNG QUAN -->
          <div class="browse-summary-bar">
            <div class="summary-left">
              <span>Đang hiển thị <strong>{{ filteredClasses.length }}</strong> lớp học</span>
              <span v-if="selectedMonHocName" class="badge-filter-applied">
                Môn: {{ selectedMonHocName }}
                <i class="fa-solid fa-xmark ms-1 cursor-pointer" @click="selectMonHoc('')"></i>
              </span>
            </div>
            <div class="conflict-legend">
              <span class="legend-dot status-available"></span> Có thể đăng ký
              <span class="legend-dot status-registered ms-3"></span> Đã đăng ký
              <span class="legend-dot status-same-subject ms-3"></span> Đã có lớp môn này
              <span class="legend-dot status-conflict ms-3"></span> Trùng giờ học
            </div>
          </div>

          <!-- LOADING / EMPTY -->
          <div v-if="loading" class="loading-box">
            <i class="fa-solid fa-spinner fa-spin fs-2 text-primary mb-3"></i>
            <p class="mb-0 fw-medium">Đang tải danh sách lớp học...</p>
          </div>

          <div v-else-if="filteredClasses.length === 0" class="empty-box">
            <div class="empty-icon-wrap">
              <i class="fa-solid fa-folder-open"></i>
            </div>
            <h4>Không tìm thấy lớp học nào</h4>
            <p class="text-muted">Không có lớp học nào phù hợp với môn học hoặc từ khóa bạn đang tìm.</p>
            <button class="btn btn-outline-primary" @click="resetFilters">Xem tất cả lớp học</button>
          </div>

          <!-- GRID DANH SÁCH LỚP HỌC -->
          <div v-else class="classes-grid">
            <div
              v-for="lop in filteredClasses"
              :key="lop.id"
              class="class-card"
              :class="{
                'card-registered': isRegistered(lop.id),
                'card-same-subject': !isRegistered(lop.id) && hasRegisteredSubjectOtherClass(lop),
                'card-conflict': !isRegistered(lop.id) && !hasRegisteredSubjectOtherClass(lop) && getConflict(lop),
              }"
            >
              <!-- Dải màu trạng thái trên đỉnh card -->
              <div
                class="card-top-strip"
                :class="{
                  'strip-registered': isRegistered(lop.id),
                  'strip-same-subject': !isRegistered(lop.id) && hasRegisteredSubjectOtherClass(lop),
                  'strip-conflict': !isRegistered(lop.id) && !hasRegisteredSubjectOtherClass(lop) && getConflict(lop),
                  'strip-available': !isRegistered(lop.id) && !hasRegisteredSubjectOtherClass(lop) && !getConflict(lop),
                }"
              ></div>

              <div class="card-inner">
                <!-- Header card: Môn học & Badges -->
                <div class="card-header-row">
                  <span class="subject-tag">{{ lop.mon_hoc?.ten_mon_hoc }}</span>
                  <div class="badge-group">
                    <span v-if="isRegistered(lop.id)" class="status-badge badge-registered">
                      <i class="fa-solid fa-check-circle me-1"></i>Đã đăng ký
                    </span>
                    <span v-else-if="hasRegisteredSubjectOtherClass(lop)" class="status-badge badge-same-subject">
                      <i class="fa-solid fa-lock me-1"></i>Đã có lớp môn này
                    </span>
                    <span v-else-if="getConflict(lop)" class="status-badge badge-conflict">
                      <i class="fa-solid fa-triangle-exclamation me-1"></i>Trùng giờ
                    </span>
                    <span v-else class="status-badge badge-available">
                      <i class="fa-solid fa-circle-dot me-1"></i>Còn chỗ
                    </span>
                  </div>
                </div>

                <!-- Giảng viên -->
                <div class="teacher-box">
                  <img
                    :src="lop.giao_vien?.hinh_anh || lop.giaoVien?.hinh_anh || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=240&q=80'"
                    alt="GV"
                    class="teacher-img"
                  />
                  <div class="teacher-info">
                    <h6 class="teacher-name mb-0">{{ lop.giao_vien?.ho_ten || lop.giaoVien?.ho_ten || 'Đang cập nhật GV' }}</h6>
                    <small class="text-muted">{{ lop.giao_vien?.chuc_danh || lop.giaoVien?.chuc_danh || 'Giảng viên chuyên môn' }}</small>
                  </div>
                </div>

                <!-- KHỐI LỊCH HỌC MỖI TUẦN (HIỆN LÙN LỊCH HỌC) -->
                <div class="weekly-schedule-highlight">
                  <div class="schedule-head">
                    <i class="fa-solid fa-calendar-week text-primary me-2"></i>
                    <strong>Lịch học hàng tuần:</strong>
                  </div>
                  <div class="schedule-content">
                    <div class="schedule-main-time">
                      {{ formatWeeklySchedule(lop) }}
                    </div>
                    <div class="schedule-sub-info">
                      <span class="schedule-date-range">
                        <i class="fa-regular fa-clock me-1"></i>
                        {{ formatTime(lop.thoi_gian_bat_dau) }} - {{ formatTime(lop.thoi_gian_ket_thuc) }}
                      </span>
                      <span class="schedule-duration">
                        ({{ calculateDuration(lop.thoi_gian_bat_dau, lop.thoi_gian_ket_thuc) }})
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Chi tiết hình thức & địa điểm -->
                <div class="class-meta-list">
                  <div class="meta-row">
                    <span class="meta-label">
                      <i :class="lop.hinh_thuc === 'online' ? 'fa-solid fa-video text-primary' : 'fa-solid fa-location-dot text-success'" class="me-2"></i>
                      Hình thức:
                    </span>
                    <span class="meta-value fw-semibold">
                      {{ lop.hinh_thuc === 'online' ? 'Online (Google Meet)' : (lop.phong_hoc?.so_phong || 'Tại trung tâm') }}
                    </span>
                  </div>
                  <div class="meta-row">
                    <span class="meta-label">
                      <i class="fa-solid fa-users text-secondary me-2"></i>
                      Sĩ số lớp:
                    </span>
                    <span class="meta-value">
                      {{ lop.so_hoc_vien_hien_tai || 0 }} / {{ lop.si_so_toi_da }} học viên
                    </span>
                  </div>
                  <div class="meta-row">
                    <span class="meta-label">
                      <i class="fa-solid fa-tag text-warning me-2"></i>
                      Học phí:
                    </span>
                    <span class="meta-value text-primary fw-bold">
                      {{ formatMoney(lop.hoc_phi) }} đ
                    </span>
                  </div>
                </div>

                <!-- CẢNH BÁO 1: ĐÃ ĐĂNG KÝ LỚP KHÁC CỦA CÙNG MÔN HỌC (1 MÔN CHỈ 1 LỚP) -->
                <div v-if="!isRegistered(lop.id) && hasRegisteredSubjectOtherClass(lop)" class="same-subject-alert-box">
                  <div class="same-subject-title">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> MỖI MÔN CHỈ ĐƯỢC ĐĂNG KÝ 1 LỚP:
                  </div>
                  <div class="same-subject-desc">
                    Bạn đã ghi danh lớp của <strong>{{ hasRegisteredSubjectOtherClass(lop).giaoVien }}</strong> ({{ hasRegisteredSubjectOtherClass(lop).thoiGian }}). Hãy hủy lớp cũ nếu muốn chuyển sang lớp này.
                  </div>
                </div>

                <!-- CẢNH BÁO 2: TRÙNG GIỜ (NẾU CÓ) -->
                <div v-else-if="!isRegistered(lop.id) && getConflict(lop)" class="conflict-alert-box">
                  <div class="conflict-title">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> TRÙNG GIỜ VỚI LỚP:
                  </div>
                  <div class="conflict-desc">
                    <strong>{{ getConflict(lop).tenMonHoc }}</strong>
                    <div>({{ getConflict(lop).dayName }}, {{ getConflict(lop).timeRange }})</div>
                  </div>
                </div>

                <!-- NÚT HÀNH ĐỘNG -->
                <div class="card-footer-action">
                  <!-- 1. ĐÃ ĐĂNG KÝ CHÍNH LỚP NÀY -->
                  <div v-if="isRegistered(lop.id)" class="registered-action-wrap">
                    <button class="btn btn-registered w-100" disabled>
                      <i class="fa-solid fa-check me-2"></i>Bạn đã đăng ký lớp này
                    </button>
                    <button
                      class="btn btn-outline-danger btn-sm mt-2 w-100"
                      @click="handleHuyDangKy(getRegistration(lop.id))"
                      :disabled="cancellingId === getRegistration(lop.id)?.id"
                    >
                      <i v-if="cancellingId === getRegistration(lop.id)?.id" class="fa-solid fa-spinner fa-spin me-1"></i>
                      <i v-else class="fa-regular fa-trash-can me-1"></i> Hủy đăng ký
                    </button>
                  </div>

                  <!-- 2. ĐÃ ĐĂNG KÝ LỚP KHÁC CÙNG MÔN: KHÔNG CHO ĐĂNG KÝ THÊM -->
                  <div v-else-if="hasRegisteredSubjectOtherClass(lop)">
                    <button
                      class="btn btn-same-subject w-100"
                      @click="showSameSubjectAlert(lop, hasRegisteredSubjectOtherClass(lop))"
                    >
                      <i class="fa-solid fa-lock me-2"></i>Đã có 1 lớp môn này
                    </button>
                  </div>

                  <!-- 3. TRÙNG GIỜ: KHÔNG CHO ĐĂNG KÝ & BẬT POPUP BÁO TRÙNG -->
                  <button
                    v-else-if="getConflict(lop)"
                    class="btn btn-conflict w-100"
                    @click="showConflictAlert(lop, getConflict(lop))"
                  >
                    <i class="fa-solid fa-ban me-2"></i>Trùng giờ học (Không thể đăng ký)
                  </button>

                  <!-- 4. ĐĂNG KÝ ĐƯỢC -->
                  <button
                    v-else
                    class="btn btn-primary-register w-100"
                    @click="handleDangKy(lop)"
                    :disabled="registeringId === lop.id"
                  >
                    <i v-if="registeringId === lop.id" class="fa-solid fa-spinner fa-spin me-2"></i>
                    <i v-else class="fa-solid fa-user-plus me-2"></i> Đăng ký lớp ngay
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB 2: DANH SÁCH LỚP HỌC ĐÃ ĐĂNG KÝ -->
        <div v-else class="enrolled-tab-section">
          <div class="enrolled-header-box">
            <div>
              <h4 class="fw-bold mb-1">Các Lớp Học Bạn Đã Ghi Danh</h4>
              <p class="text-muted mb-0">Các lớp học này đã được lưu vào thời khóa biểu hàng tuần của bạn</p>
            </div>
            <router-link to="/hoc-vien/lich-hoc" class="btn btn-primary">
              <i class="fa-regular fa-calendar-days me-2"></i>Xem thời khóa biểu tuần
            </router-link>
          </div>

          <div v-if="myRegistrations.length === 0" class="empty-box">
            <div class="empty-icon-wrap">
              <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h4>Chưa đăng ký lớp nào</h4>
            <p class="text-muted">Bạn chưa ghi danh vào lớp học nào. Hãy chọn môn học và đăng ký ngay!</p>
            <button class="btn btn-primary" @click="activeViewTab = 'browse'">Khám phá lớp học ngay</button>
          </div>

          <div v-else class="enrolled-list">
            <div
              v-for="dk in myRegistrations"
              :key="dk.id"
              class="enrolled-card"
            >
              <div class="enrolled-left">
                <span class="enrolled-subject">{{ dk.lop_hoc?.mon_hoc?.ten_mon_hoc }}</span>
                <h5 class="enrolled-title">
                  <i class="fa-solid fa-user-tie text-secondary me-2"></i>
                  {{ dk.lop_hoc?.giao_vien?.ho_ten || dk.lop_hoc?.giaoVien?.ho_ten || 'Giảng viên' }}
                </h5>
                <div class="enrolled-time-badge">
                  <i class="fa-solid fa-calendar-week text-primary me-2"></i>
                  <strong>{{ formatWeeklySchedule(dk.lop_hoc) }}</strong>
                </div>
                <div class="enrolled-meta mt-2">
                  <span class="me-3">
                    <i :class="dk.lop_hoc?.hinh_thuc === 'online' ? 'fa-solid fa-video' : 'fa-solid fa-location-dot'" class="me-1"></i>
                    {{ dk.lop_hoc?.hinh_thuc === 'online' ? 'Online qua Google Meet' : (dk.lop_hoc?.phong_hoc?.so_phong + ' - ' + (dk.lop_hoc?.phong_hoc?.dia_chi || 'EduLink')) }}
                  </span>
                  <span>
                    <i class="fa-solid fa-money-bill me-1"></i>
                    {{ formatMoney(dk.lop_hoc?.hoc_phi) }} đ
                  </span>
                </div>
              </div>

              <div class="enrolled-right">
                <span class="badge-status-confirmed mb-3">
                  <i class="fa-solid fa-circle-check me-1"></i>Đã xác nhận
                </span>
                <div class="d-flex flex-column gap-2 w-100">
                  <a
                    v-if="dk.lop_hoc?.link_online"
                    :href="dk.lop_hoc.link_online"
                    target="_blank"
                    class="btn btn-primary btn-sm"
                  >
                    <i class="fa-solid fa-video me-1"></i> Vào phòng Meet
                  </a>
                  <button
                    class="btn btn-outline-danger btn-sm"
                    @click="handleHuyDangKy(dk)"
                    :disabled="cancellingId === dk.id"
                  >
                    <i v-if="cancellingId === dk.id" class="fa-solid fa-spinner fa-spin me-1"></i>
                    <i v-else class="fa-regular fa-trash-can me-1"></i> Hủy đăng ký
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL CẢNH BÁO 1: MỖI MÔN CHỈ ĐĂNG KÝ 1 LỚP -->
        <div v-if="showSameSubjectModal" class="modal-overlay" @click.self="showSameSubjectModal = false">
          <div class="modal-conflict-card">
            <div class="modal-same-subject-icon">
              <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <h4 class="conflict-modal-title">Mỗi Môn Học Chỉ Được Đăng Ký 1 Lớp!</h4>
            <p class="conflict-modal-subtitle">
              Hệ thống EduLink quy định mỗi sinh viên chỉ được ghi danh <strong>1 lớp duy nhất cho mỗi môn học</strong>.
            </p>

            <div class="conflict-comparison-box">
              <div class="comparison-item item-existing">
                <div class="item-tag">LỚP BẠN ĐANG HỌC HIỆN TẠI</div>
                <div class="item-subject">{{ sameSubjectDetails.registeredClass?.tenMonHoc }}</div>
                <div class="item-time">
                  <i class="fa-solid fa-user-tie me-1 text-success"></i>
                  {{ sameSubjectDetails.registeredClass?.giaoVien }} - {{ sameSubjectDetails.registeredClass?.thoiGian }}
                </div>
              </div>

              <div class="comparison-divider text-muted">
                <i class="fa-solid fa-arrow-down"></i>
                <span>Nếu bạn muốn đổi sang lớp mới này:</span>
              </div>

              <div class="comparison-item item-target">
                <div class="item-tag">LỚP MỚI BẠN MUỐN CHUYỂN SANG</div>
                <div class="item-subject">{{ sameSubjectDetails.targetClass?.mon_hoc?.ten_mon_hoc }}</div>
                <div class="item-time">
                  <i class="fa-solid fa-user-tie me-1 text-primary"></i>
                  {{ sameSubjectDetails.targetClass?.giao_vien?.ho_ten || sameSubjectDetails.targetClass?.giaoVien?.ho_ten }} - {{ formatWeeklySchedule(sameSubjectDetails.targetClass) }}
                </div>
              </div>
            </div>

            <div class="conflict-modal-tips">
              <i class="fa-solid fa-circle-info text-primary me-2"></i>
              <span>Hướng dẫn: Bạn chỉ cần bấm <strong>'Hủy đăng ký'</strong> ở lớp hiện tại, sau đó quay lại đăng ký lớp mới này là xong!</span>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-secondary w-50 py-2" @click="showSameSubjectModal = false">Đóng</button>
              <button class="btn btn-outline-primary w-50 py-2" @click="showSameSubjectModal = false; activeViewTab = 'enrolled'">
                Xem lớp đã đăng ký
              </button>
            </div>
          </div>
        </div>

        <!-- MODAL CẢNH BÁO 2: TRÙNG GIỜ HỌC -->
        <div v-if="showConflictModal" class="modal-overlay" @click.self="showConflictModal = false">
          <div class="modal-conflict-card">
            <div class="modal-conflict-icon">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 class="conflict-modal-title">Không Thể Đăng Ký Lớp Học!</h4>
            <p class="conflict-modal-subtitle">
              Hệ thống phát hiện lớp học bạn vừa chọn bị <strong>TRÙNG GIỜ</strong> với một lớp bạn đã ghi danh trước đó.
            </p>

            <div class="conflict-comparison-box">
              <div class="comparison-item item-target">
                <div class="item-tag">LỚP BẠN MUỐN ĐĂNG KÝ</div>
                <div class="item-subject">{{ conflictDetails.targetClass?.mon_hoc?.ten_mon_hoc }}</div>
                <div class="item-time">
                  <i class="fa-solid fa-clock me-1 text-danger"></i>
                  {{ formatWeeklySchedule(conflictDetails.targetClass) }}
                </div>
              </div>

              <div class="comparison-divider">
                <i class="fa-solid fa-bolt text-danger"></i>
                <span>TRÙNG LỊCH</span>
              </div>

              <div class="comparison-item item-existing">
                <div class="item-tag">LỚP BẠN ĐÃ ĐĂNG KÝ TRƯỚC ĐÓ</div>
                <div class="item-subject">{{ conflictDetails.conflictInfo?.tenMonHoc }}</div>
                <div class="item-time">
                  <i class="fa-solid fa-clock me-1 text-primary"></i>
                  {{ conflictDetails.conflictInfo?.dayName }}, {{ conflictDetails.conflictInfo?.timeRange }}
                </div>
              </div>
            </div>

            <div class="conflict-modal-tips">
              <i class="fa-solid fa-circle-info text-primary me-2"></i>
              <span>Gợi ý: Bạn có thể chọn ca học khác của môn này hoặc hủy lớp đang học nếu muốn đổi lịch.</span>
            </div>

            <div class="conflict-modal-actions">
              <button class="btn btn-secondary w-100 py-2" @click="showConflictModal = false">
                Đã hiểu, tôi sẽ chọn lớp khác
              </button>
            </div>
          </div>
        </div>

        <!-- MODAL THÔNG BÁO ĐĂNG KÝ THÀNH CÔNG (KÈM NÚT XEM LỊCH HỌC NGAY) -->
        <div v-if="showSuccessModal" class="modal-overlay" @click.self="showSuccessModal = false">
          <div class="modal-conflict-card">
            <div class="modal-success-icon-wrap mb-3">
              <i class="fa-solid fa-circle-check text-success" style="font-size: 54px;"></i>
            </div>
            <h4 class="conflict-modal-title text-success">Đăng Ký Thành Công!</h4>
            <p class="conflict-modal-subtitle">
              Bạn đã ghi danh thành công vào lớp <strong>{{ registeredSuccessClass?.mon_hoc?.ten_mon_hoc }}</strong>.
            </p>

            <div class="success-schedule-card p-3 rounded-3 text-start mb-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
              <div class="fw-bold text-success mb-1">
                <i class="fa-solid fa-calendar-check me-2"></i>Suất học đã được thêm vào Lịch học của bạn:
              </div>
              <div class="fs-6 fw-bolder text-dark">{{ formatWeeklySchedule(registeredSuccessClass) }}</div>
              <div class="text-muted small mt-1">
                <i class="fa-solid fa-location-dot me-1"></i>
                {{ registeredSuccessClass?.hinh_thuc === 'online' ? 'Online (Google Meet)' : (registeredSuccessClass?.phong_hoc?.so_phong || 'Cơ sở chính') }}
              </div>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-secondary w-50 py-2" @click="showSuccessModal = false">
                Tiếp tục chọn lớp
              </button>
              <router-link to="/hoc-vien/lich-hoc" class="btn btn-primary w-50 py-2 d-flex align-items-center justify-content-center">
                <i class="fa-regular fa-calendar-days me-2"></i>Xem Lịch học ngay
              </router-link>
            </div>
          </div>
        </div>

        <!-- TOAST THÔNG BÁO -->
        <div v-if="toastMessage" class="toast-floating" :class="'toast-' + toastType">
          <i :class="toastType === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-exclamation'" class="me-2 fs-5"></i>
          <span>{{ toastMessage }}</span>
          <button class="toast-close-btn" @click="toastMessage = ''">&times;</button>
        </div>

      </div>
    </main>
  </div>
</template>

<script>
import { lopHocService } from '../../services/lopHocService'
import { lichHocService } from '../../services/lichHocService'

export default {
  name: 'DangKyLopHoc',
  data() {
    return {
      loading: false,
      activeViewTab: 'browse', // 'browse' hoặc 'enrolled'
      dsMonHoc: [],
      dsLopHoc: [],
      myRegistrations: [],
      selectedMonHocId: '',
      keyword: '',
      filterHinhThuc: '',
      filterLoaiLop: '',
      registeringId: null,
      cancellingId: null,
      showConflictModal: false,
      conflictDetails: {
        targetClass: null,
        conflictInfo: null,
      },
      showSameSubjectModal: false,
      sameSubjectDetails: {
        targetClass: null,
        registeredClass: null,
      },
      showSuccessModal: false,
      registeredSuccessClass: null,
      toastMessage: '',
      toastType: 'success',
      toastTimer: null,
    }
  },
  computed: {
    selectedMonHocName() {
      if (!this.selectedMonHocId) return ''
      const found = this.dsMonHoc.find(m => m.id === this.selectedMonHocId)
      return found ? found.ten_mon_hoc : ''
    },
    filteredClasses() {
      return this.dsLopHoc.filter(lop => {
        // Lọc môn học
        if (this.selectedMonHocId && lop.id_mon_hoc !== this.selectedMonHocId) {
          return false
        }
        // Lọc hình thức
        if (this.filterHinhThuc && lop.hinh_thuc !== this.filterHinhThuc) {
          return false
        }
        // Lọc loại lớp
        if (this.filterLoaiLop && lop.loai_lop !== this.filterLoaiLop) {
          return false
        }
        // Lọc từ khóa
        if (this.keyword.trim()) {
          const kw = this.keyword.toLowerCase().trim()
          const tenMon = (lop.mon_hoc?.ten_mon_hoc || '').toLowerCase()
          const tenGv = (lop.giao_vien?.ho_ten || lop.giaoVien?.ho_ten || '').toLowerCase()
          if (!tenMon.includes(kw) && !tenGv.includes(kw)) {
            return false
          }
        }
        return true
      })
    },
  },
  mounted() {
    this.loadAllData()
  },
  methods: {
    async loadAllData() {
      this.loading = true
      try {
        await Promise.all([
          this.loadMonHoc(),
          this.loadClasses(),
          this.loadMyRegistrations(),
        ])
      } catch (e) {
        console.error('Lỗi nạp dữ liệu:', e)
      } finally {
        this.loading = false
      }
    },

    async loadMonHoc() {
      try {
        const res = await lopHocService.getMonHoc()
        if (res && res.status) {
          this.dsMonHoc = res.data || []
        }
      } catch (e) {
        console.error('Lỗi tải môn học:', e)
      }
    },

    async loadClasses() {
      try {
        const res = await lopHocService.getPublicLop({ per_page: 100 })
        if (res && res.status) {
          this.dsLopHoc = res.data?.data || res.data || []
        }
      } catch (e) {
        console.error('Lỗi tải danh sách lớp:', e)
      }
    },

    async loadMyRegistrations() {
      try {
        const res = await lichHocService.getLopHocCuaToi()
        if (res && res.status) {
          this.myRegistrations = (res.data || []).filter(dk => dk.trang_thai !== 'da_huy')
        }
      } catch (e) {
        console.error('Lỗi tải lớp đã đăng ký:', e)
      }
    },

    selectMonHoc(id) {
      this.selectedMonHocId = id
    },

    countClassesBySubject(monHocId) {
      return this.dsLopHoc.filter(l => l.id_mon_hoc === monHocId).length
    },

    resetFilters() {
      this.selectedMonHocId = ''
      this.keyword = ''
      this.filterHinhThuc = ''
      this.filterLoaiLop = ''
    },

    // Kiểm tra học viên đã đăng ký chính lớp này chưa
    isRegistered(lopId) {
      return this.myRegistrations.some(dk => dk.id_lop_hoc === lopId && dk.trang_thai !== 'da_huy')
    },

    getRegistration(lopId) {
      return this.myRegistrations.find(dk => dk.id_lop_hoc === lopId && dk.trang_thai !== 'da_huy')
    },

    // ========================================================
    // RÀNG BUỘC: 1 MÔN CHỈ ĐĂNG KÝ ĐƯỢC 1 LỚP DUY NHẤT
    // ========================================================
    hasRegisteredSubjectOtherClass(lop) {
      if (this.isRegistered(lop.id)) return null
      const found = this.myRegistrations.find(dk => {
        return dk.trang_thai !== 'da_huy'
          && dk.lop_hoc
          && dk.lop_hoc.id_mon_hoc === lop.id_mon_hoc
          && dk.lop_hoc.id !== lop.id
      })
      if (!found) return null
      return {
        lopId: found.lop_hoc.id,
        tenMonHoc: found.lop_hoc.mon_hoc?.ten_mon_hoc || lop.mon_hoc?.ten_mon_hoc,
        giaoVien: found.lop_hoc.giao_vien?.ho_ten || found.lop_hoc.giaoVien?.ho_ten || 'Giảng viên',
        thoiGian: this.formatWeeklySchedule(found.lop_hoc),
      }
    },

    showSameSubjectAlert(targetClass, registeredClass) {
      this.sameSubjectDetails = {
        targetClass,
        registeredClass,
      }
      this.showSameSubjectModal = true
    },

    // ========================================================
    // LOGIC KIỂM TRA TRÙNG GIỜ HỌC
    // ========================================================
    getConflict(lop) {
      if (this.isRegistered(lop.id)) return null
      if (this.hasRegisteredSubjectOtherClass(lop)) return null
      if (!lop.thoi_gian_bat_dau || !lop.thoi_gian_ket_thuc) return null

      const pA = this.parseDateParts(lop.thoi_gian_bat_dau)
      const pAEnd = this.parseDateParts(lop.thoi_gian_ket_thuc)
      if (!pA || !pAEnd) return null

      const startMinA = pA.hour * 60 + pA.minute
      const endMinA = pAEnd.hour * 60 + pAEnd.minute
      const dayOfWeekA = this.getDayOfWeek(lop.thoi_gian_bat_dau)

      for (const dk of this.myRegistrations) {
        if (dk.trang_thai === 'da_huy' || !dk.lop_hoc) continue
        const ex = dk.lop_hoc
        if (!ex.thoi_gian_bat_dau || !ex.thoi_gian_ket_thuc) continue

        const pB = this.parseDateParts(ex.thoi_gian_bat_dau)
        const pBEnd = this.parseDateParts(ex.thoi_gian_ket_thuc)
        if (!pB || !pBEnd) continue

        const startMinB = pB.hour * 60 + pB.minute
        const endMinB = pBEnd.hour * 60 + pBEnd.minute
        const dayOfWeekB = this.getDayOfWeek(ex.thoi_gian_bat_dau)

        let isConflict = false
        let reason = ''

        // TH1: Cùng ngày cụ thể và khoảng giờ đè lên nhau
        if (pA.dateKey === pB.dateKey) {
          if (startMinA < endMinB && endMinA > startMinB) {
            isConflict = true
            reason = `Cùng ngày ${pB.day}/${pB.month}`
          }
        }

        // TH2: Cùng Thứ trong tuần và khung giờ đè lên nhau
        if (dayOfWeekA === dayOfWeekB) {
          if (startMinA < endMinB && endMinA > startMinB) {
            isConflict = true
            reason = `Vào ${this.getDayName(dayOfWeekB)} hàng tuần`
          }
        }

        if (isConflict) {
          return {
            lopId: ex.id,
            tenMonHoc: ex.mon_hoc?.ten_mon_hoc || 'Lớp đã đăng ký',
            dayName: this.getDayName(dayOfWeekB),
            timeRange: `${pB.timeStr} - ${pBEnd.timeStr}`,
            reason: reason,
          }
        }
      }

      return null
    },

    showConflictAlert(targetClass, conflictInfo) {
      this.conflictDetails = {
        targetClass,
        conflictInfo,
      }
      this.showConflictModal = true
    },

    // XỬ LÝ ĐĂNG KÝ LỚP
    async handleDangKy(lop) {
      // 1. Kiểm tra nếu đã có lớp khác cùng môn
      const sameSub = this.hasRegisteredSubjectOtherClass(lop)
      if (sameSub) {
        this.showSameSubjectAlert(lop, sameSub)
        return
      }

      // 2. Kiểm tra trùng giờ
      const conflict = this.getConflict(lop)
      if (conflict) {
        this.showConflictAlert(lop, conflict)
        return
      }

      this.registeringId = lop.id
      try {
        const res = await lichHocService.dangKyLop(lop.id)
        if (res && res.status) {
          this.registeredSuccessClass = lop
          this.showSuccessModal = true
          await Promise.all([
            this.loadMyRegistrations(),
            this.loadClasses(),
          ])
        } else {
          // Bắt trường hợp Backend báo trùng giờ hoặc trùng môn
          if (res?.da_dang_ky_mon) {
            this.showSameSubjectAlert(lop, {
              tenMonHoc: lop.mon_hoc?.ten_mon_hoc,
              giaoVien: res.lop_da_dang_ky?.giao_vien || 'Giảng viên',
            })
          } else if (res?.trung_gio) {
            this.showConflictAlert(lop, {
              tenMonHoc: res.lop_trung?.ten_mon || 'Lớp đã đăng ký',
              dayName: res.lop_trung?.thu || 'Trong tuần',
              timeRange: res.lop_trung?.khung_gio || '',
            })
          } else {
            this.showToast(res?.message || 'Đăng ký không thành công.', 'danger')
          }
        }
      } catch (e) {
        const data = e.response?.data
        if (data && data.da_dang_ky_mon) {
          this.showSameSubjectAlert(lop, {
            tenMonHoc: lop.mon_hoc?.ten_mon_hoc,
            giaoVien: data.lop_da_dang_ky?.giao_vien || 'Giảng viên',
          })
        } else if (data && data.trung_gio) {
          this.showConflictAlert(lop, {
            tenMonHoc: data.lop_trung?.ten_mon || 'Lớp đã đăng ký',
            dayName: data.lop_trung?.thu || 'Trong tuần',
            timeRange: data.lop_trung?.khung_gio || '',
          })
        } else {
          this.showToast(data?.message || e.message || 'Lỗi kết nối máy chủ.', 'danger')
        }
      } finally {
        this.registeringId = null
      }
    },

    // XỬ LÝ HỦY ĐĂNG KÝ
    async handleHuyDangKy(reg) {
      if (!reg) return
      const tenMon = reg.lop_hoc?.mon_hoc?.ten_mon_hoc || 'lớp học này'
      if (!confirm(`Bạn có chắc chắn muốn hủy đăng ký lớp '${tenMon}' không?`)) {
        return
      }

      this.cancellingId = reg.id
      try {
        const res = await lichHocService.huyDangKy(reg.id)
        if (res && res.status) {
          this.showToast(`Đã hủy đăng ký lớp '${tenMon}' thành công.`, 'success')
          await Promise.all([
            this.loadMyRegistrations(),
            this.loadClasses(),
          ])
        } else {
          this.showToast(res?.message || 'Hủy không thành công.', 'danger')
        }
      } catch (e) {
        this.showToast(e.message || 'Lỗi khi hủy đăng ký.', 'danger')
      } finally {
        this.cancellingId = null
      }
    },

    showToast(msg, type = 'success') {
      this.toastMessage = msg
      this.toastType = type
      clearTimeout(this.toastTimer)
      this.toastTimer = setTimeout(() => {
        this.toastMessage = ''
      }, 4500)
    },

    // Helpers định dạng thời gian
    parseDateParts(iso) {
      if (!iso) return null
      const m = String(iso).match(/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})/)
      if (m) {
        return {
          year: parseInt(m[1]),
          month: parseInt(m[2]),
          day: parseInt(m[3]),
          hour: parseInt(m[4]),
          minute: parseInt(m[5]),
          dateKey: `${m[1]}-${m[2]}-${m[3]}`,
          timeStr: `${m[4]}:${m[5]}`,
        }
      }
      const d = new Date(iso)
      const y = d.getFullYear()
      const mo = String(d.getMonth() + 1).padStart(2, '0')
      const day = String(d.getDate()).padStart(2, '0')
      const h = String(d.getHours()).padStart(2, '0')
      const mi = String(d.getMinutes()).padStart(2, '0')
      return {
        year: y,
        month: parseInt(mo),
        day: parseInt(day),
        hour: parseInt(h),
        minute: parseInt(mi),
        dateKey: `${y}-${mo}-${day}`,
        timeStr: `${h}:${mi}`,
      }
    },

    getDayOfWeek(iso) {
      const p = this.parseDateParts(iso)
      if (!p) return 0
      const d = new Date(p.year, p.month - 1, p.day)
      return d.getDay() // 0 = CN, 1 = T2...
    },

    getDayName(dayOfWeek) {
      const names = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7']
      return names[dayOfWeek] || 'Trong tuần'
    },

    formatWeeklySchedule(lop) {
      if (!lop || !lop.thoi_gian_bat_dau || !lop.thoi_gian_ket_thuc) return 'Đang cập nhật lịch'
      const start = this.parseDateParts(lop.thoi_gian_bat_dau)
      const end = this.parseDateParts(lop.thoi_gian_ket_thuc)
      const dayOfWeek = this.getDayOfWeek(lop.thoi_gian_bat_dau)
      const dayName = this.getDayName(dayOfWeek)
      return `${dayName} hàng tuần (${start.timeStr} - ${end.timeStr})`
    },

    calculateDuration(startIso, endIso) {
      const pA = this.parseDateParts(startIso)
      const pB = this.parseDateParts(endIso)
      if (!pA || !pB) return ''
      const minA = pA.hour * 60 + pA.minute
      const minB = pB.hour * 60 + pB.minute
      const diff = Math.max(0, minB - minA)
      const h = Math.floor(diff / 60)
      const m = diff % 60
      if (h > 0 && m > 0) return `${h}h${m}p / buổi`
      if (h > 0) return `${h} giờ / buổi`
      return `${m} phút / buổi`
    },

    formatTime(iso) {
      const p = this.parseDateParts(iso)
      return p ? p.timeStr : ''
    },

    formatMoney(v) {
      return new Intl.NumberFormat('vi-VN').format(v || 0)
    },
  },
}
</script>

<style scoped>
.edu-page {
  min-height: 100vh;
  background: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 30px 24px 60px;
}

.content-container {
  max-width: 1320px;
  margin: 0 auto;
}

/* Page Title Area */
.page-title-area {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 24px;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 4px;
}

.page-subtitle {
  color: #64748b;
  font-size: 14.5px;
  margin-bottom: 0;
}

/* BỘ LỌC MÔN HỌC DẠNG PILLS */
.filter-subject-section {
  background: #ffffff;
  padding: 18px 20px;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  margin-bottom: 16px;
}

.section-label {
  font-size: 13.5px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
}

.subject-pills-list {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  overflow-x: auto;
  padding-bottom: 4px;
}

.subject-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  padding: 8px 16px;
  border-radius: 50px;
  font-size: 13.5px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.subject-pill-btn:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.subject-pill-btn.active {
  background: #0060d2;
  color: #ffffff;
  border-color: #0060d2;
  box-shadow: 0 3px 8px rgba(0, 96, 210, 0.25);
}

.count-badge {
  background: rgba(0, 0, 0, 0.08);
  font-size: 11px;
  padding: 2px 7px;
  border-radius: 20px;
}

.subject-pill-btn.active .count-badge {
  background: rgba(255, 255, 255, 0.25);
  color: #ffffff;
}

/* THANH TÌM KIẾM & BỘ LỌC PHỤ */
.secondary-filter-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.filter-search-box {
  position: relative;
  flex: 1;
  min-width: 260px;
  max-width: 460px;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 14px;
}

.search-input {
  width: 100%;
  height: 42px;
  padding: 8px 36px 8px 38px;
  border: 1px solid #d8dee4;
  border-radius: 10px;
  background: #fff;
  font-size: 13.5px;
  outline: none;
  transition: border-color 0.15s ease;
}

.search-input:focus {
  border-color: #0060d2;
}

.clear-search-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  font-size: 18px;
  color: #94a3b8;
  cursor: pointer;
}

.filter-dropdowns {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
}

.filter-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-item label {
  font-size: 12.5px;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0;
}

.form-select-sm {
  height: 40px;
  padding: 6px 12px;
  border: 1px solid #d8dee4;
  border-radius: 8px;
  background: #fff;
  font-size: 13px;
  color: #334155;
  outline: none;
  cursor: pointer;
}

/* THÔNG TIN TỔNG QUAN */
.browse-summary-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
  padding: 0 4px;
  font-size: 13px;
  color: #64748b;
  flex-wrap: wrap;
  gap: 12px;
}

.badge-filter-applied {
  background: #eff6ff;
  color: #0284c7;
  padding: 3px 10px;
  border-radius: 20px;
  font-weight: 600;
  margin-left: 10px;
}

.conflict-legend {
  display: flex;
  align-items: center;
  font-size: 12.5px;
  color: #64748b;
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 5px;
}

.legend-dot.status-available { background: #0060d2; }
.legend-dot.status-registered { background: #16a34a; }
.legend-dot.status-same-subject { background: #7c3aed; }
.legend-dot.status-conflict { background: #dc2626; }

/* GRID DANH SÁCH LỚP HỌC */
.classes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.class-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.2s ease;
}

.class-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.class-card.card-same-subject {
  border-color: #ddd6fe;
  background: #faf5ff;
}

.card-top-strip {
  height: 5px;
  width: 100%;
}

.strip-available { background: #0060d2; }
.strip-registered { background: #16a34a; }
.strip-same-subject { background: #7c3aed; }
.strip-conflict { background: #dc2626; }

.card-inner {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.card-header-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 14px;
}

.subject-tag {
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
}

.status-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 20px;
  white-space: nowrap;
}

.badge-available {
  background: #eff6ff;
  color: #0284c7;
}

.badge-registered {
  background: #dcfce7;
  color: #15803d;
}

.badge-same-subject {
  background: #ede9fe;
  color: #6d28d9;
}

.badge-conflict {
  background: #fee2e2;
  color: #b91c1c;
}

/* Giảng viên */
.teacher-box {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f8fafc;
  padding: 10px 14px;
  border-radius: 12px;
  margin-bottom: 14px;
}

.card-same-subject .teacher-box {
  background: #ffffff;
}

.teacher-img {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
  border: 1.5px solid #e2e8f0;
}

.teacher-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

/* KHỐI LỊCH HỌC MỖI TUẦN */
.weekly-schedule-highlight {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 12px;
  padding: 12px 14px;
  margin-bottom: 14px;
}

.schedule-head {
  font-size: 12.5px;
  color: #1e40af;
  margin-bottom: 4px;
}

.schedule-main-time {
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
}

.schedule-sub-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #475569;
  margin-top: 2px;
}

/* Chi tiết hình thức */
.class-meta-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-size: 13px;
  margin-bottom: 14px;
  padding-bottom: 14px;
  border-bottom: 1px dashed #e2e8f0;
}

.meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.meta-label {
  color: #64748b;
  display: flex;
  align-items: center;
}

.meta-value {
  color: #0f172a;
}

/* HỘP CẢNH BÁO 1 MÔN 1 LỚP */
.same-subject-alert-box {
  background: #f5f3ff;
  border: 1px solid #ddd6fe;
  border-radius: 10px;
  padding: 10px 12px;
  margin-bottom: 14px;
}

.same-subject-title {
  font-size: 11.5px;
  font-weight: 800;
  color: #6d28d9;
  margin-bottom: 3px;
}

.same-subject-desc {
  font-size: 12px;
  color: #5b21b6;
  line-height: 1.35;
}

/* HỘP CẢNH BÁO TRÙNG GIỜ */
.conflict-alert-box {
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 10px;
  padding: 10px 12px;
  margin-bottom: 14px;
  animation: shake 0.3s ease-in-out;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}

.conflict-title {
  font-size: 11.5px;
  font-weight: 800;
  color: #b91c1c;
  margin-bottom: 3px;
}

.conflict-desc {
  font-size: 12px;
  color: #991b1b;
  line-height: 1.3;
}

/* Footer Nút bấm */
.card-footer-action {
  margin-top: auto;
}

.btn-primary-register {
  background: #0060d2;
  color: #ffffff;
  border: 0;
  padding: 10px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-primary-register:hover {
  background: #004fb0;
}

.btn-registered {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
  font-weight: 700;
  font-size: 13.5px;
  padding: 9px;
  border-radius: 10px;
  cursor: default;
}

.btn-same-subject {
  background: #ede9fe;
  color: #6d28d9;
  border: 1px solid #c4b5fd;
  font-weight: 700;
  font-size: 13px;
  padding: 9px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-same-subject:hover {
  background: #ddd6fe;
}

.btn-conflict {
  background: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fca5a5;
  font-weight: 700;
  font-size: 13px;
  padding: 9px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-conflict:hover {
  background: #fecaca;
}

/* TAB 2: LỚP ĐÃ ĐĂNG KÝ */
.enrolled-tab-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.enrolled-header-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  padding: 18px 24px;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  flex-wrap: wrap;
  gap: 16px;
}

.enrolled-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.enrolled-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  padding: 20px 24px;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  border-left: 5px solid #16a34a;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
  gap: 20px;
  flex-wrap: wrap;
}

.enrolled-subject {
  font-size: 12px;
  font-weight: 700;
  color: #15803d;
  background: #dcfce7;
  padding: 3px 10px;
  border-radius: 20px;
  display: inline-block;
  margin-bottom: 6px;
}

.enrolled-title {
  font-size: 16px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 4px;
}

.enrolled-time-badge {
  font-size: 13.5px;
  color: #0284c7;
  background: #eff6ff;
  padding: 6px 12px;
  border-radius: 8px;
  display: inline-block;
}

.enrolled-meta {
  font-size: 13px;
  color: #64748b;
}

.enrolled-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  min-width: 170px;
}

.badge-status-confirmed {
  background: #dcfce7;
  color: #15803d;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
}

/* ========================================================
   MODAL DIALOGS
   ======================================================== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-conflict-card {
  background: #ffffff;
  border-radius: 20px;
  width: 100%;
  max-width: 520px;
  padding: 30px;
  text-align: center;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  animation: pop-modal 0.25s ease-out;
}

@keyframes pop-modal {
  from { opacity: 0; transform: scale(0.92); }
  to { opacity: 1; transform: scale(1); }
}

.modal-conflict-icon {
  width: 68px;
  height: 68px;
  background: #fee2e2;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: #dc2626;
  margin-bottom: 16px;
}

.modal-same-subject-icon {
  width: 68px;
  height: 68px;
  background: #ede9fe;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: #7c3aed;
  margin-bottom: 16px;
}

.conflict-modal-title {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 8px;
}

.conflict-modal-subtitle {
  font-size: 13.5px;
  color: #64748b;
  margin-bottom: 20px;
  line-height: 1.4;
}

.conflict-comparison-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px;
  margin-bottom: 18px;
  text-align: left;
}

.comparison-item {
  padding: 10px 12px;
  border-radius: 10px;
}

.item-target {
  background: #fff5f5;
  border: 1px solid #fed7d7;
}

.item-existing {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
}

.item-tag {
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.5px;
  color: #64748b;
  margin-bottom: 2px;
}

.item-target .item-tag { color: #c53030; }
.item-existing .item-tag { color: #22543d; }

.item-subject {
  font-size: 14.5px;
  font-weight: 800;
  color: #0f172a;
}

.item-time {
  font-size: 12.5px;
  font-weight: 600;
  margin-top: 2px;
}

.comparison-divider {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 8px 0;
  font-size: 11.5px;
  font-weight: 800;
  color: #dc2626;
}

.conflict-modal-tips {
  background: #eff6ff;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 12.5px;
  color: #1e40af;
  text-align: left;
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;
}

/* Toast Floating */
.toast-floating {
  position: fixed;
  bottom: 24px;
  right: 24px;
  padding: 14px 20px;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  z-index: 9999;
  font-size: 14px;
  font-weight: 600;
  animation: slide-up 0.25s ease-out;
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.toast-success {
  background: #15803d;
  color: #ffffff;
}

.toast-danger {
  background: #dc2626;
  color: #ffffff;
}

.toast-close-btn {
  background: transparent;
  border: 0;
  color: #fff;
  font-size: 20px;
  margin-left: 14px;
  cursor: pointer;
  line-height: 1;
}

/* Loading & Empty State */
.loading-box, .empty-box {
  background: #fff;
  padding: 60px 20px;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  text-align: center;
  color: #64748b;
}

.empty-icon-wrap {
  width: 64px;
  height: 64px;
  background: #f1f5f9;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  color: #94a3b8;
  margin-bottom: 14px;
}

.empty-box h4 {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 6px;
}

@media (max-width: 768px) {
  .classes-grid {
    grid-template-columns: 1fr;
  }
  .secondary-filter-bar {
    flex-direction: column;
    align-items: stretch;
  }
  .filter-search-box {
    max-width: 100%;
  }
  .enrolled-card {
    flex-direction: column;
    align-items: flex-start;
  }
  .enrolled-right {
    width: 100%;
    align-items: flex-start;
  }
}
</style>
