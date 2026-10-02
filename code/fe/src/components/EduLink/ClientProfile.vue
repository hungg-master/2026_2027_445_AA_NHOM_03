<template>
  <div class="client-profile-page">
    <div class="profile-container">
      <!-- 1. Breadcrumb điều hướng -->
      <nav class="breadcrumb-nav mb-4">
        <i class="fa-solid fa-house me-1 text-muted"></i>
        <router-link to="/" class="crumb-link">Trang chủ</router-link>
        <span class="crumb-sep">›</span>
        <span class="crumb-active">Thông tin tài khoản</span>
      </nav>

      <!-- 2. Thẻ tổng quan người dùng (Profile Overview Card) -->
      <div class="profile-overview-card mb-4">
        <div class="overview-content">
          <!-- Avatar to với nút đổi ảnh nhanh -->
          <div class="overview-avatar-wrapper">
            <img :src="profile.hinh_anh || defaultAvatar" alt="Avatar" class="overview-avatar-img" />
            <button
              class="avatar-change-badge"
              title="Đổi ảnh đại diện"
              @click="activeTab = 'avatar'"
            >
              <i class="fa-solid fa-camera"></i>
            </button>
            <span
              v-if="da_xac_minh_face_id"
              class="avatar-verified-shield"
              title="Đã xác thực Face ID sinh trắc học"
            >
              <i class="bx bxs-check-shield"></i>
            </span>
          </div>

          <!-- Thông tin tóm tắt -->
          <div class="overview-details">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
              <h1 class="overview-name mb-0">{{ profile.ho_ten || 'Người dùng EduLink' }}</h1>
              <span class="badge-role">
                <i :class="userRole === 'giao_vien' ? 'fa-solid fa-chalkboard-user me-1' : 'fa-solid fa-user-graduate me-1'"></i>
                {{ userRole === 'giao_vien' ? 'Giảng viên' : 'Học viên' }}
              </span>
            </div>

            <div class="overview-meta d-flex flex-wrap align-items-center gap-3 text-muted">
              <span><i class="fa-regular fa-envelope me-1"></i> {{ profile.email || 'Chưa cập nhật' }}</span>
              <span><i class="fa-solid fa-phone me-1"></i> {{ profile.so_dien_thoai || 'Chưa cập nhật' }}</span>
              <span><i class="fa-regular fa-calendar me-1"></i> Ngày sinh: {{ formatDateDisplay(profile.ngay_sinh) }}</span>
            </div>

            <!-- Trạng thái Face ID -->
            <div class="overview-face-status mt-2">
              <span v-if="da_xac_minh_face_id" class="badge-face-verified">
                <i class="bx bxs-check-shield text-success me-1 fs-5"></i>
                <strong>Đã xác thực Face ID sinh trắc học</strong>
              </span>
              <span v-else class="badge-face-unverified" @click="activeTab = 'faceid'">
                <i class="bx bx-error-circle text-warning me-1 fs-5"></i>
                <strong>Chưa xác thực Face ID</strong> — <span class="action-link">Kích hoạt ngay</span>
              </span>
            </div>
          </div>
        </div>

        <!-- 4 Tabs điều hướng chức năng -->
        <div class="profile-tabs-header mt-4">
          <button
            :class="['profile-tab-btn', { active: activeTab === 'info' }]"
            @click="activeTab = 'info'"
          >
            <i class="fa-regular fa-id-card me-2"></i> Thông tin cá nhân
          </button>
          <button
            :class="['profile-tab-btn', { active: activeTab === 'avatar' }]"
            @click="activeTab = 'avatar'"
          >
            <i class="fa-regular fa-image me-2"></i> Thay đổi Avatar
          </button>
          <button
            :class="['profile-tab-btn', { active: activeTab === 'faceid' }]"
            @click="activeTab = 'faceid'"
          >
            <i class="fa-solid fa-fingerprint me-2"></i> Xác thực Face ID
          </button>
          <button
            :class="['profile-tab-btn', { active: activeTab === 'password' }]"
            @click="activeTab = 'password'"
          >
            <i class="fa-solid fa-shield-halved me-2"></i> Đổi mật khẩu
          </button>
        </div>
      </div>

      <!-- Toast thông báo kết quả -->
      <transition name="toast-fade">
        <div v-if="toast.show" :class="['profile-toast', 'toast-' + toast.type]">
          <i :class="toast.icon" class="me-2 fs-5"></i>
          <span>{{ toast.message }}</span>
        </div>
      </transition>

      <!-- 3. NỘI DUNG TỪNG TAB -->
      <div class="profile-tab-content">
        <!-- ================= TAB 1: CẬP NHẬT THÔNG TIN ================= -->
        <div v-if="activeTab === 'info'" class="card-section">
          <div class="section-header">
            <h2 class="section-title"><i class="fa-regular fa-user text-primary me-2"></i> Cập nhật thông tin cá nhân</h2>
            <p class="section-subtitle">Quản lý và cập nhật thông tin hồ sơ của bạn trên hệ thống EduLink.</p>
          </div>

          <form @submit.prevent="submitUpdateProfile" class="profile-form">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                <input
                  type="text"
                  class="form-control"
                  v-model.trim="profileForm.ho_ten"
                  placeholder="Nhập họ và tên đầy đủ"
                  required
                />
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Địa chỉ Email</label>
                <input
                  type="email"
                  class="form-control bg-light"
                  :value="profile.email"
                  disabled
                  title="Email tài khoản không thể chỉnh sửa"
                />
                <small class="text-muted"><i class="fa-solid fa-lock me-1"></i> Email dùng để đăng nhập và bảo mật</small>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                <input
                  type="tel"
                  class="form-control"
                  v-model.trim="profileForm.so_dien_thoai"
                  placeholder="Nhập số điện thoại liên hệ"
                  required
                />
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Ngày sinh</label>
                <input
                  type="date"
                  class="form-control"
                  v-model="profileForm.ngay_sinh"
                />
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Giới tính</label>
                <select class="form-select" v-model="profileForm.gioi_tinh">
                  <option :value="1">Nam</option>
                  <option :value="0">Nữ</option>
                  <option :value="2">Khác</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Địa chỉ liên hệ</label>
                <input
                  type="text"
                  class="form-control"
                  v-model.trim="profileForm.dia_chi"
                  placeholder="Ví dụ: Quận 1, TP. Hồ Chí Minh"
                />
              </div>
            </div>

            <div class="form-action-row mt-4 pt-3 border-top d-flex justify-content-end">
              <button
                type="submit"
                class="btn btn-primary btn-save px-4 py-2 fw-semibold"
                :disabled="loading.info"
              >
                <i v-if="loading.info" class="fa-solid fa-spinner fa-spin me-2"></i>
                <i v-else class="fa-solid fa-floppy-disk me-2"></i>
                {{ loading.info ? 'Đang lưu...' : 'Lưu thông tin cá nhân' }}
              </button>
            </div>
          </form>
        </div>

        <!-- ================= TAB 2: THAY ĐỔI AVATAR ================= -->
        <div v-if="activeTab === 'avatar'" class="card-section">
          <div class="section-header">
            <h2 class="section-title"><i class="fa-regular fa-image text-primary me-2"></i> Thay đổi ảnh đại diện</h2>
            <p class="section-subtitle">Tải lên hình ảnh chân dung hoặc nhập đường dẫn ảnh để cập nhật ảnh đại diện của bạn.</p>
          </div>

          <div class="row align-items-center g-4">
            <!-- Xem trước ảnh -->
            <div class="col-md-4 text-center">
              <div class="avatar-preview-box mx-auto">
                <img :src="avatarPreview || profile.hinh_anh || defaultAvatar" alt="Preview Avatar" class="avatar-preview-img" />
              </div>
              <p class="small text-muted mt-2">Ảnh xem trước hiển thị trên hệ thống</p>
            </div>

            <!-- Các tùy chọn cập nhật ảnh -->
            <div class="col-md-8">
              <!-- Cách 1: Tải ảnh từ máy tính -->
              <div class="avatar-upload-option mb-3 p-3 rounded-3 border bg-white">
                <label class="form-label fw-bold mb-1">
                  <i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Tải ảnh lên từ máy tính
                </label>
                <p class="small text-muted mb-2">Hỗ trợ các định dạng JPG, PNG, WEBP (tối đa 5MB).</p>
                <input
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png,image/webp"
                  @change="handleAvatarFileChange"
                />
              </div>

              <!-- Cách 2: Nhập đường link ảnh trực tiếp -->
              <div class="avatar-url-option p-3 rounded-3 border bg-white">
                <label class="form-label fw-bold mb-1">
                  <i class="fa-solid fa-link text-primary me-1"></i> Hoặc dán đường dẫn ảnh (URL)
                </label>
                <div class="input-group">
                  <input
                    type="url"
                    class="form-control"
                    v-model.trim="avatarUrlInput"
                    placeholder="https://example.com/avatar.jpg"
                  />
                  <button class="btn btn-outline-secondary" type="button" @click="applyAvatarUrl">
                    Xem thử
                  </button>
                </div>
              </div>

              <!-- Nút Lưu Avatar -->
              <div class="mt-4 d-flex gap-2 justify-content-end">
                <button
                  type="button"
                  class="btn btn-light border px-3"
                  @click="resetAvatarPreview"
                >
                  Hủy thay đổi
                </button>
                <button
                  type="button"
                  class="btn btn-primary px-4 fw-semibold"
                  :disabled="loading.avatar || !avatarPreview"
                  @click="submitUpdateAvatar"
                >
                  <i v-if="loading.avatar" class="fa-solid fa-spinner fa-spin me-2"></i>
                  <i v-else class="fa-solid fa-check me-2"></i>
                  {{ loading.avatar ? 'Đang lưu ảnh...' : 'Lưu ảnh đại diện' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= TAB 3: XÁC THỰC FACE ID ================= -->
        <div v-if="activeTab === 'faceid'" class="card-section">
          <div class="section-header">
            <h2 class="section-title"><i class="fa-solid fa-fingerprint text-primary me-2"></i> Xác thực Face ID sinh trắc học</h2>
            <p class="section-subtitle">
              Đăng ký khuôn mặt giúp bạn điểm danh lớp học tự động, xác minh bảo mật và đăng nhập nhanh không cần mật khẩu.
            </p>
          </div>

          <!-- Trạng thái xác thực hiện tại -->
          <div class="face-status-alert mb-4 p-3 rounded-3" :class="da_xac_minh_face_id ? 'bg-success-subtle text-success border-success' : 'bg-warning-subtle text-warning border-warning'">
            <div class="d-flex align-items-center gap-3">
              <i :class="da_xac_minh_face_id ? 'bx bxs-check-shield fs-1' : 'bx bx-error-circle fs-1'"></i>
              <div>
                <h5 class="fw-bold mb-1">
                  {{ da_xac_minh_face_id ? 'Tài khoản của bạn đã được kích hoạt Face ID' : 'Chưa đăng ký Face ID cho tài khoản này' }}
                </h5>
                <p class="mb-0 small">
                  {{ da_xac_minh_face_id ? 'Dữ liệu vector khuôn mặt của bạn đã được mã hóa an toàn trên hệ thống. Bạn có thể quét lại để cập nhật góc nhìn mới bất cứ lúc nào.' : 'Vui lòng sử dụng camera bên dưới để quét khuôn mặt trực tiếp hoặc tải ảnh chân dung rõ mặt để đăng ký.' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Giao diện Camera quét Face ID trực tiếp -->
          <div class="face-scanner-container text-center py-3">
            <div class="scanner-wrapper mx-auto">
              <!-- Video Live Stream -->
              <video
                ref="videoElement"
                autoplay
                playsinline
                muted
                class="scanner-video"
                :class="{ 'scanning-active': isScanning }"
              ></video>
              <!-- Canvas vẽ khung nhận diện -->
              <canvas ref="overlayCanvas" class="scanner-canvas"></canvas>

              <!-- Vòng radar quét xoay quanh -->
              <div v-if="isScanning" class="radar-scan-circle"></div>

              <!-- Lớp mờ khi chưa mở camera -->
              <div v-if="!isScanning" class="camera-standby-overlay">
                <i class="fa-solid fa-camera fs-1 mb-2 text-muted"></i>
                <p class="small text-muted mb-0">Camera đang tắt</p>
              </div>
            </div>

            <!-- Trạng thái quét động -->
            <div class="scan-status-info mt-3">
              <div v-if="isModelLoading" class="text-primary fw-medium">
                <i class="fa-solid fa-spinner fa-spin me-2"></i> Đang tải mô hình trí tuệ nhân tạo (AI Face Detection)...
              </div>
              <div v-else-if="scanStatus" class="fw-bold text-dark fs-6">
                {{ scanStatus }}
              </div>
              <div v-else class="text-muted small">
                Nhìn thẳng vào khung hình tròn, giữ yên khuôn mặt trong vài giây khi mở camera.
              </div>

              <!-- Thanh tiến trình quét (0 - 100%) -->
              <div v-if="isScanning && scanProgress > 0" class="progress scan-progress-bar mx-auto mt-2" style="max-width: 280px; height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" :style="{ width: scanProgress + '%' }"></div>
              </div>
            </div>

            <!-- Các nút thao tác Camera -->
            <div class="scanner-actions mt-3 d-flex justify-content-center gap-2">
              <button
                v-if="!isScanning"
                type="button"
                class="btn btn-primary px-4 py-2 fw-semibold"
                @click="startFaceScan"
              >
                <i class="fa-solid fa-camera me-2"></i> {{ da_xac_minh_face_id ? 'Quét lại Face ID' : 'Bắt đầu quét Face ID' }}
              </button>
              <button
                v-else
                type="button"
                class="btn btn-outline-danger px-4 py-2 fw-semibold"
                @click="stopFaceScan"
              >
                <i class="fa-solid fa-stop me-2"></i> Dừng Camera
              </button>
            </div>

            <!-- Tùy chọn dự phòng: Tải ảnh Face ID -->
            <div class="mt-4 pt-3 border-top" style="max-width: 480px; margin: 0 auto;">
              <p class="small text-muted mb-2">
                <i class="fa-solid fa-circle-info me-1"></i> Thiết bị không có webcam? Bạn có thể tải lên ảnh chân dung rõ mặt để lưu hồ sơ:
              </p>
              <div class="input-group input-group-sm">
                <input
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png,image/webp"
                  @change="handleFacePhotoUpload"
                  :disabled="loading.facePhoto"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- ================= TAB 4: ĐỔI MẬT KHẨU ================= -->
        <div v-if="activeTab === 'password'" class="card-section">
          <div class="section-header">
            <h2 class="section-title"><i class="fa-solid fa-lock text-primary me-2"></i> Đổi mật khẩu tài khoản</h2>
            <p class="section-subtitle">Để bảo vệ tài khoản, hãy sử dụng mật khẩu mạnh gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
          </div>

          <form @submit.prevent="submitChangePassword" class="password-form" style="max-width: 580px;">
            <!-- Mật khẩu hiện tại -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
              <div class="input-group">
                <input
                  :type="showPass.current ? 'text' : 'password'"
                  class="form-control"
                  v-model.trim="passwordForm.current_password"
                  placeholder="Nhập mật khẩu hiện tại của bạn"
                  required
                />
                <button
                  class="btn btn-outline-secondary"
                  type="button"
                  @click="showPass.current = !showPass.current"
                >
                  <i :class="showPass.current ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                </button>
              </div>
            </div>

            <!-- Mật khẩu mới -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
              <div class="input-group">
                <input
                  :type="showPass.new ? 'text' : 'password'"
                  class="form-control"
                  v-model="passwordForm.password"
                  placeholder="Tối thiểu 6 ký tự"
                  required
                  minlength="6"
                  @input="checkPasswordStrength"
                />
                <button
                  class="btn btn-outline-secondary"
                  type="button"
                  @click="showPass.new = !showPass.new"
                >
                  <i :class="showPass.new ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                </button>
              </div>

              <!-- Thanh đo độ mạnh mật khẩu -->
              <div v-if="passwordForm.password" class="password-strength-box mt-2">
                <div class="progress" style="height: 5px;">
                  <div
                    class="progress-bar"
                    :class="passwordStrengthColor"
                    :style="{ width: passwordStrengthPercent + '%' }"
                  ></div>
                </div>
                <small class="strength-label mt-1 d-block" :class="passwordStrengthTextColor">
                  Độ bảo mật: <strong>{{ passwordStrengthText }}</strong>
                </small>
              </div>
            </div>

            <!-- Xác nhận mật khẩu mới -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
              <div class="input-group">
                <input
                  :type="showPass.confirm ? 'text' : 'password'"
                  class="form-control"
                  v-model="passwordForm.password_confirmation"
                  placeholder="Nhập lại mật khẩu mới"
                  required
                />
                <button
                  class="btn btn-outline-secondary"
                  type="button"
                  @click="showPass.confirm = !showPass.confirm"
                >
                  <i :class="showPass.confirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                </button>
              </div>
              <small
                v-if="passwordForm.password_confirmation && passwordForm.password !== passwordForm.password_confirmation"
                class="text-danger mt-1 d-block"
              >
                <i class="fa-solid fa-circle-exclamation me-1"></i> Mật khẩu xác nhận không khớp!
              </small>
            </div>

            <!-- Nút Lưu mật khẩu -->
            <div class="mt-4 pt-3 border-top">
              <button
                type="submit"
                class="btn btn-primary px-4 py-2 fw-semibold"
                :disabled="loading.password || (passwordForm.password !== passwordForm.password_confirmation)"
              >
                <i v-if="loading.password" class="fa-solid fa-spinner fa-spin me-2"></i>
                <i v-else class="fa-solid fa-key me-2"></i>
                {{ loading.password ? 'Đang cập nhật...' : 'Cập nhật mật khẩu' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as faceapi from "face-api.js";
import profileService from "../../services/profileService";

export default {
  name: "ClientProfile",
  data() {
    return {
      activeTab: "info", // 'info' | 'avatar' | 'faceid' | 'password'
      userRole: "hoc_vien",
      defaultAvatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80",
      profile: {
        id: null,
        ho_ten: "",
        email: "",
        so_dien_thoai: "",
        ngay_sinh: "",
        gioi_tinh: 1,
        dia_chi: "",
        hinh_anh: "",
        du_lieu_khuon_mat: null,
        face_id_photo_path: null
      },
      profileForm: {
        ho_ten: "",
        so_dien_thoai: "",
        ngay_sinh: "",
        gioi_tinh: 1,
        dia_chi: ""
      },
      // Avatar
      avatarPreview: "",
      avatarUrlInput: "",
      // Password
      passwordForm: {
        current_password: "",
        password: "",
        password_confirmation: ""
      },
      showPass: {
        current: false,
        new: false,
        confirm: false
      },
      passwordStrengthPercent: 0,
      passwordStrengthText: "Yếu",
      passwordStrengthColor: "bg-danger",
      passwordStrengthTextColor: "text-danger",
      // Face ID Scanner
      isScanning: false,
      isModelLoading: false,
      isModelLoaded: false,
      scanStatus: "",
      scanProgress: 0,
      videoStream: null,
      detectInterval: null,
      scanCount: 0,
      requiredScanFrames: 12,
      // Loadings & Toasts
      loading: {
        info: false,
        avatar: false,
        password: false,
        facePhoto: false
      },
      toast: {
        show: false,
        message: "",
        type: "success", // 'success' | 'error' | 'info'
        icon: "fa-solid fa-circle-check",
        timer: null
      }
    };
  },
  computed: {
    da_xac_minh_face_id() {
      return !!(this.profile.du_lieu_khuon_mat || this.profile.face_id_photo_path);
    }
  },
  mounted() {
    this.userRole = localStorage.getItem("role") || "hoc_vien";
    this.loadProfile();

    // Hỗ trợ nhảy trực tiếp tới tab qua query param hoặc hash (e.g. /client/profile#faceid)
    if (this.$route.hash) {
      const hash = this.$route.hash.replace("#", "");
      if (["info", "avatar", "faceid", "password"].includes(hash)) {
        this.activeTab = hash;
      }
    }
  },
  beforeUnmount() {
    this.stopFaceScan();
  },
  methods: {
    // 1. TẢI HỒ SƠ TÀI KHOẢN
    async loadProfile() {
      // Đọc trước từ LocalStorage
      const localUserStr = localStorage.getItem("user") || localStorage.getItem("edulink_user");
      if (localUserStr) {
        try {
          const u = JSON.parse(localUserStr);
          this.applyUserData(u);
        } catch (e) {}
      }

      // Gọi API lấy dữ liệu mới nhất từ CSDL
      try {
        const res = await profileService.getProfile(this.userRole);
        if (res && (res.status || res.data)) {
          const userData = res.data || res;
          this.applyUserData(userData);
          // Cập nhật lại localStorage để đồng bộ các nơi khác
          localStorage.setItem("user", JSON.stringify(userData));
        }
      } catch (err) {
        console.warn("Không thể tải hồ sơ từ server:", err);
      }
    },

    applyUserData(u) {
      if (!u) return;
      this.profile = {
        ...this.profile,
        id: u.id,
        ho_ten: u.ho_ten || u.name || "",
        email: u.email || "",
        so_dien_thoai: u.so_dien_thoai || u.phone || "",
        ngay_sinh: u.ngay_sinh || "",
        gioi_tinh: u.gioi_tinh !== undefined ? u.gioi_tinh : 1,
        dia_chi: u.dia_chi || "",
        hinh_anh: u.hinh_anh || u.avatar || "",
        du_lieu_khuon_mat: u.du_lieu_khuon_mat || null,
        face_id_photo_path: u.face_id_photo_path || null
      };

      this.profileForm = {
        ho_ten: this.profile.ho_ten,
        so_dien_thoai: this.profile.so_dien_thoai,
        ngay_sinh: this.profile.ngay_sinh ? this.profile.ngay_sinh.substring(0, 10) : "",
        gioi_tinh: this.profile.gioi_tinh,
        dia_chi: this.profile.dia_chi
      };

      if (!this.avatarPreview) {
        this.avatarPreview = this.profile.hinh_anh || this.defaultAvatar;
      }
    },

    // 2. CẬP NHẬT THÔNG TIN CÁ NHÂN
    async submitUpdateProfile() {
      this.loading.info = true;
      try {
        const payload = {
          ho_ten: this.profileForm.ho_ten,
          so_dien_thoai: this.profileForm.so_dien_thoai,
          ngay_sinh: this.profileForm.ngay_sinh || null,
          gioi_tinh: this.profileForm.gioi_tinh,
          dia_chi: this.profileForm.dia_chi || null,
          hinh_anh: this.profile.hinh_anh
        };

        const res = await profileService.updateProfile(this.userRole, payload);
        if (res && res.status) {
          this.profile = { ...this.profile, ...payload };
          this.syncLocalUser();
          this.showToast("success", "Cập nhật thông tin tài khoản thành công!");
        } else {
          this.showToast("error", res?.message || "Cập nhật thất bại. Vui lòng kiểm tra lại.");
        }
      } catch (err) {
        console.error(err);
        this.showToast("error", err?.response?.data?.message || err?.message || "Lỗi lưu thông tin.");
      } finally {
        this.loading.info = false;
      }
    },

    // 3. THAY ĐỔI ẢNH ĐẠI DIỆN
    handleAvatarFileChange(e) {
      const file = e.target.files?.[0];
      if (!file) return;

      if (file.size > 5 * 1024 * 1024) {
        this.showToast("error", "Kích thước ảnh tối đa là 5MB!");
        return;
      }

      const reader = new FileReader();
      reader.onload = (event) => {
        this.avatarPreview = event.target.result;
      };
      reader.readAsDataURL(file);
    },

    applyAvatarUrl() {
      if (!this.avatarUrlInput) {
        this.showToast("error", "Vui lòng nhập đường link ảnh hợp lệ!");
        return;
      }
      this.avatarPreview = this.avatarUrlInput;
    },

    resetAvatarPreview() {
      this.avatarPreview = this.profile.hinh_anh || this.defaultAvatar;
      this.avatarUrlInput = "";
    },

    async submitUpdateAvatar() {
      if (!this.avatarPreview) return;
      this.loading.avatar = true;
      try {
        const payload = {
          ho_ten: this.profileForm.ho_ten,
          so_dien_thoai: this.profileForm.so_dien_thoai,
          hinh_anh: this.avatarPreview
        };

        const res = await profileService.updateProfile(this.userRole, payload);
        if (res && res.status) {
          this.profile.hinh_anh = this.avatarPreview;
          this.syncLocalUser();
          this.showToast("success", "Cập nhật ảnh đại diện thành công!");
        } else {
          this.showToast("error", res?.message || "Không thể cập nhật ảnh đại diện.");
        }
      } catch (err) {
        console.error(err);
        this.showToast("error", err?.response?.data?.message || "Lỗi cập nhật ảnh đại diện.");
      } finally {
        this.loading.avatar = false;
      }
    },

    // 4. XÁC THỰC FACE ID CAMERA
    async startFaceScan() {
      this.isScanning = true;
      this.scanStatus = "Đang kết nối camera...";
      this.scanProgress = 0;
      this.scanCount = 0;

      try {
        // Tải Face-API model nếu chưa tải
        if (!this.isModelLoaded) {
          this.isModelLoading = true;
          this.scanStatus = "Đang tải mô hình trí tuệ nhân tạo...";
          await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri("/model"),
            faceapi.nets.faceLandmark68Net.loadFromUri("/model"),
            faceapi.nets.faceRecognitionNet.loadFromUri("/model")
          ]);
          this.isModelLoaded = true;
          this.isModelLoading = false;
        }

        // Mở luồng Camera
        this.videoStream = await navigator.mediaDevices.getUserMedia({
          video: { width: 320, height: 320, facingMode: "user" }
        });

        const video = this.$refs.videoElement;
        if (video) {
          video.srcObject = this.videoStream;
          video.onloadedmetadata = () => {
            video.play();
            this.runFaceDetectionLoop();
          };
        }
      } catch (err) {
        console.error("Lỗi camera:", err);
        this.isScanning = false;
        this.isModelLoading = false;
        const msg = err.name === "NotAllowedError"
          ? "Trình duyệt đã chặn quyền truy cập Camera. Vui lòng cho phép quyền Camera trên thanh địa chỉ."
          : "Không thể kết nối với Camera: " + (err.message || err.name);
        this.showToast("error", msg);
      }
    },

    runFaceDetectionLoop() {
      const video = this.$refs.videoElement;
      const canvas = this.$refs.overlayCanvas;
      if (!video || !canvas) return;

      const displaySize = { width: 220, height: 220 };
      canvas.width = displaySize.width;
      canvas.height = displaySize.height;
      faceapi.matchDimensions(canvas, displaySize);

      this.scanStatus = "Đang tìm kiếm khuôn mặt...";

      this.detectInterval = setInterval(async () => {
        if (!this.isScanning || !video || video.paused || video.ended) return;

        try {
          const detections = await faceapi
            .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.4 }))
            .withFaceLandmarks()
            .withFaceDescriptors();

          const ctx = canvas.getContext("2d");
          ctx.clearRect(0, 0, canvas.width, canvas.height);

          if (detections.length === 1) {
            this.scanCount++;
            this.scanProgress = Math.min(Math.round((this.scanCount / this.requiredScanFrames) * 100), 100);
            this.scanStatus = `Đang phân tích đặc trưng sinh trắc học... ${this.scanProgress}%`;

            if (this.scanCount >= this.requiredScanFrames) {
              clearInterval(this.detectInterval);
              this.scanStatus = "Đang lưu dữ liệu Face ID vào hệ thống...";
              const descriptorVector = Array.from(detections[0].descriptor);
              await this.saveFaceIdDescriptor(descriptorVector);
            }
          } else if (detections.length === 0) {
            this.scanCount = Math.max(0, this.scanCount - 1);
            this.scanStatus = "Vui lòng nhìn thẳng vào camera...";
          } else {
            this.scanStatus = "Cảnh báo: Phát hiện quá nhiều người trong khung hình!";
          }
        } catch (e) {
          console.error("Lỗi nhận diện khuôn mặt:", e);
        }
      }, 200);
    },

    async saveFaceIdDescriptor(descriptorVector) {
      try {
        const res = await profileService.saveFaceIdVector(this.userRole, this.profile.id, descriptorVector);
        if (res && (res.status || res.success)) {
          this.profile.du_lieu_khuon_mat = JSON.stringify(descriptorVector);
          this.syncLocalUser();
          this.showToast("success", "Đăng ký Face ID thành công! Dữ liệu sinh trắc học đã được kích hoạt.");
          this.stopFaceScan();
        } else {
          this.showToast("error", res?.message || "Lỗi lưu sinh trắc học.");
          this.stopFaceScan();
        }
      } catch (err) {
        console.error(err);
        this.showToast("error", err?.response?.data?.message || "Lỗi khi lưu khuôn mặt.");
        this.stopFaceScan();
      }
    },

    stopFaceScan() {
      if (this.detectInterval) {
        clearInterval(this.detectInterval);
        this.detectInterval = null;
      }
      if (this.videoStream) {
        this.videoStream.getTracks().forEach((track) => track.stop());
        this.videoStream = null;
      }
      this.isScanning = false;
      this.scanStatus = "";
      this.scanProgress = 0;
    },

    async handleFacePhotoUpload(e) {
      const file = e.target.files?.[0];
      if (!file) return;

      this.loading.facePhoto = true;
      try {
        const formData = new FormData();
        formData.append("face_id_photo", file);

        const res = await profileService.uploadFaceIdPhoto(this.userRole, formData);
        if (res && res.status) {
          this.profile.face_id_photo_path = "uploaded";
          this.syncLocalUser();
          this.showToast("success", "Tải lên ảnh Face ID thành công!");
        } else {
          this.showToast("error", res?.message || "Không thể tải ảnh Face ID.");
        }
      } catch (err) {
        console.error(err);
        this.showToast("error", err?.response?.data?.message || "Lỗi khi tải ảnh Face ID.");
      } finally {
        this.loading.facePhoto = false;
      }
    },

    // 5. ĐỔI MẬT KHẨU
    checkPasswordStrength() {
      const p = this.passwordForm.password;
      if (!p) {
        this.passwordStrengthPercent = 0;
        this.passwordStrengthText = "Yếu";
        return;
      }
      let score = 0;
      if (p.length >= 6) score += 25;
      if (p.length >= 10) score += 25;
      if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score += 25;
      if (/[0-9]/.test(p) && /[^A-Za-z0-9]/.test(p)) score += 25;

      this.passwordStrengthPercent = score;
      if (score <= 25) {
        this.passwordStrengthText = "Rất yếu";
        this.passwordStrengthColor = "bg-danger";
        this.passwordStrengthTextColor = "text-danger";
      } else if (score <= 50) {
        this.passwordStrengthText = "Trung bình";
        this.passwordStrengthColor = "bg-warning";
        this.passwordStrengthTextColor = "text-warning";
      } else if (score <= 75) {
        this.passwordStrengthText = "Khá";
        this.passwordStrengthColor = "bg-info";
        this.passwordStrengthTextColor = "text-info";
      } else {
        this.passwordStrengthText = "Mạnh & An toàn";
        this.passwordStrengthColor = "bg-success";
        this.passwordStrengthTextColor = "text-success";
      }
    },

    async submitChangePassword() {
      if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
        this.showToast("error", "Mật khẩu xác nhận không khớp!");
        return;
      }
      this.loading.password = true;
      try {
        const res = await profileService.changePassword(this.userRole, this.passwordForm);
        if (res && res.status) {
          this.showToast("success", "Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới của bạn.");
          this.passwordForm = {
            current_password: "",
            password: "",
            password_confirmation: ""
          };
          this.passwordStrengthPercent = 0;
        } else {
          this.showToast("error", res?.message || "Đổi mật khẩu thất bại.");
        }
      } catch (err) {
        console.error(err);
        this.showToast("error", err?.response?.data?.message || "Mật khẩu hiện tại không chính xác!");
      } finally {
        this.loading.password = false;
      }
    },

    // 6. TIỆN ÍCH CHUNG
    syncLocalUser() {
      const avatarFinal = this.profile.hinh_anh || this.defaultAvatar;
      const u = {
        ...this.profile,
        avatar: avatarFinal,
        hinh_anh: avatarFinal
      };
      localStorage.setItem("user", JSON.stringify(u));
      localStorage.setItem("edulink_user", JSON.stringify(u));
      window.dispatchEvent(new CustomEvent("edulink:user-updated", { detail: u }));
    },

    formatDateDisplay(d) {
      if (!d) return "Chưa cập nhật";
      try {
        const dateObj = new Date(d);
        if (isNaN(dateObj.getTime())) return d;
        return `${dateObj.getDate().toString().padStart(2, "0")}/${(dateObj.getMonth() + 1).toString().padStart(2, "0")}/${dateObj.getFullYear()}`;
      } catch (e) {
        return d;
      }
    },

    showToast(type, message) {
      if (this.toast.timer) clearTimeout(this.toast.timer);
      this.toast.type = type;
      this.toast.message = message;
      this.toast.icon =
        type === "success"
          ? "fa-solid fa-circle-check"
          : type === "error"
          ? "fa-solid fa-triangle-exclamation"
          : "fa-solid fa-circle-info";
      this.toast.show = true;

      this.toast.timer = setTimeout(() => {
        this.toast.show = false;
      }, 3500);
    }
  }
};
</script>

<style scoped>
.client-profile-page {
  padding: 32px 20px 60px;
  background-color: #f8fafc;
  min-height: calc(100vh - 70px);
}

.profile-container {
  max-width: 1040px;
  margin: 0 auto;
}

/* Breadcrumb */
.breadcrumb-nav {
  font-size: 13.5px;
}

.crumb-link {
  color: #64748b;
  text-decoration: none;
  font-weight: 500;
}

.crumb-link:hover {
  color: #0060d2;
}

.crumb-sep {
  margin: 0 8px;
  color: #94a3b8;
}

.crumb-active {
  color: #0f172a;
  font-weight: 700;
}

/* 1. THẺ TỔNG QUAN PROFILE */
.profile-overview-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  padding: 28px 28px 0;
}

.overview-content {
  display: flex;
  align-items: center;
  gap: 24px;
}

.overview-avatar-wrapper {
  position: relative;
  width: 96px;
  height: 96px;
  flex-shrink: 0;
}

.overview-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #0060d2;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.15);
}

.avatar-change-badge {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: #0060d2;
  color: #ffffff;
  border: 2px solid #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.avatar-change-badge:hover {
  background: #004bb0;
  transform: scale(1.08);
}

.avatar-verified-shield {
  position: absolute;
  top: -2px;
  right: -2px;
  color: #10b981;
  font-size: 24px;
  background: #ffffff;
  border-radius: 50%;
  line-height: 1;
}

.overview-details {
  flex: 1;
}

.overview-name {
  font-size: 23px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.5px;
}

.badge-role {
  background: #eff6ff;
  color: #0060d2;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  border: 1px solid #bfdbfe;
}

.overview-meta {
  font-size: 13.5px;
}

.badge-face-verified {
  display: inline-flex;
  align-items: center;
  color: #059669;
  background: #ecfdf5;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 13px;
  border: 1px solid #a7f3d0;
}

.badge-face-unverified {
  display: inline-flex;
  align-items: center;
  color: #b45309;
  background: #fffbeb;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 13px;
  border: 1px solid #fde68a;
  cursor: pointer;
}

.badge-face-unverified .action-link {
  color: #0060d2;
  text-decoration: underline;
  margin-left: 4px;
}

/* Tabs Header */
.profile-tabs-header {
  display: flex;
  gap: 8px;
  border-top: 1px solid #f1f5f9;
  overflow-x: auto;
}

.profile-tab-btn {
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  padding: 14px 18px;
  font-size: 14.5px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.profile-tab-btn:hover {
  color: #0060d2;
}

.profile-tab-btn.active {
  color: #0060d2;
  border-bottom-color: #0060d2;
  font-weight: 700;
}

/* Toast */
.profile-toast {
  position: fixed;
  top: 85px;
  right: 28px;
  z-index: 1050;
  padding: 14px 20px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
  display: flex;
  align-items: center;
  font-size: 14.5px;
  font-weight: 500;
}

.toast-success {
  background-color: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.toast-error {
  background-color: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.toast-info {
  background-color: #eff6ff;
  color: #1e40af;
  border: 1px solid #bfdbfe;
}

.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.25s ease;
}

.toast-fade-enter-from,
.toast-fade-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

/* Khối nội dung Card Section */
.card-section {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  padding: 28px;
}

.section-header {
  margin-bottom: 24px;
}

.section-title {
  font-size: 19px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.3px;
  margin-bottom: 4px;
}

.section-subtitle {
  font-size: 14px;
  color: #64748b;
  margin-bottom: 0;
}

/* Avatar Preview Box */
.avatar-preview-box {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  overflow: hidden;
  border: 4px solid #e2e8f0;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.avatar-preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Face ID Scanner */
.scanner-wrapper {
  position: relative;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  overflow: hidden;
  border: 4px solid #0060d2;
  box-shadow: 0 0 25px rgba(0, 96, 210, 0.25);
  background-color: #0f172a;
}

.scanner-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scaleX(-1);
}

.scanner-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: scaleX(-1);
}

.camera-standby-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #f1f5f9;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.radar-scan-circle {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 2px dashed #00c6ff;
  animation: radarSpin 4s linear infinite;
  pointer-events: none;
}

@keyframes radarSpin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .overview-content {
    flex-direction: column;
    text-align: center;
  }
  .overview-meta {
    justify-content: center;
  }
}
</style>
