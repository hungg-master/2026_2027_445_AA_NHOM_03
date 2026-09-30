<template>
  <div class="auth-page">
    <div class="auth-card">
      <!-- Cột trái: Hero Brand Banner -->
      <div class="brand-side">
        <div class="brand-bg-overlay"></div>

        <div class="brand-content">
          <!-- Logo EduLink -->
          <div class="brand-logo">
            <svg class="edu-icon" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.94 8.13 22 12 22C15.87 22 19 19.94 19 17.18V13.18L12 17L5 13.18Z"/>
            </svg>
            <span class="brand-title">EduLink</span>
          </div>

          <!-- Hero Headline -->
          <h2 class="hero-headline">
            {{ isLoginMode ? 'Chào mừng trở lại!' : 'Tham gia cộng đồng học tập' }}
          </h2>
          <p class="hero-description">
            {{ isLoginMode
              ? 'Đăng nhập để tiếp tục hành trình học tập và kết nối với giáo viên của bạn.'
              : 'Kết nối với các giáo viên ưu tú, quản lý lịch học dễ dàng và thúc đẩy hành trình giáo dục của bạn.' }}
          </p>
        </div>

        <!-- Floating decoration -->
        <div class="floating-elements">
          <div class="floating-orb orb-1"></div>
          <div class="floating-orb orb-2"></div>
          <div class="floating-book book-1">
            <i class="fa-solid fa-book-open"></i>
          </div>
          <div class="floating-book book-2">
            <i class="fa-solid fa-book-open-reader"></i>
          </div>
        </div>
      </div>

      <!-- Cột phải: Form -->
      <div class="form-side">
        <!-- Tab chuyển Đăng ký / Đăng nhập -->
        <div class="auth-tabs">
          <button
            :class="['tab-btn', { active: !isLoginMode }]"
            @click="switchMode(false)"
          >
            Đăng ký
          </button>
          <button
            :class="['tab-btn', { active: isLoginMode }]"
            @click="switchMode(true)"
          >
            Đăng nhập
          </button>
        </div>

        <!-- ========== FORM ĐĂNG KÝ ========== -->
        <form v-if="!isLoginMode" @submit.prevent="handleRegister">
          <!-- Chọn loại tài khoản -->
          <div class="mb-3">
            <label class="form-label">Bạn là</label>
            <div class="role-selector">
              <button
                type="button"
                :class="['role-btn', { active: form.role === 'hoc_vien' }]"
                @click="form.role = 'hoc_vien'"
              >
                <i class="fa-solid fa-user-graduate me-2"></i>Học viên
              </button>
              <button
                type="button"
                :class="['role-btn', { active: form.role === 'giao_vien' }]"
                @click="form.role = 'giao_vien'"
              >
                <i class="fa-solid fa-chalkboard-user me-2"></i>Giáo viên
              </button>
            </div>
          </div>

          <!-- Họ tên -->
          <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input
              v-model.trim="form.ho_ten"
              type="text"
              class="form-control custom-input"
              placeholder="Nguyễn Văn A"
              required
            />
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input
              v-model.trim="form.email"
              type="email"
              class="form-control custom-input"
              placeholder="example@email.com"
              required
            />
          </div>

          <!-- Số điện thoại -->
          <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input
              v-model.trim="form.so_dien_thoai"
              type="tel"
              class="form-control custom-input"
              placeholder="0901234567"
            />
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <div class="input-password-wrapper">
              <input
                v-model="form.mat_khau"
                :type="showPassword ? 'text' : 'password'"
                class="form-control custom-input"
                placeholder="Ít nhất 6 ký tự"
                required
              />
              <button type="button" class="toggle-pw-btn" @click="showPassword = !showPassword">
                <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-4">
            <label class="form-label">Xác nhận mật khẩu</label>
            <div class="input-password-wrapper">
              <input
                v-model="form.mat_khau_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                class="form-control custom-input"
                placeholder="Nhập lại mật khẩu"
                required
              />
              <button type="button" class="toggle-pw-btn" @click="showConfirmPassword = !showConfirmPassword">
                <i :class="showConfirmPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Alert -->
          <div v-if="errorMessage" class="alert alert-danger py-2 px-3 mb-3" role="alert" style="font-size: 13.5px;">
            {{ errorMessage }}
          </div>
          <div v-if="successMessage" class="alert alert-success py-2 px-3 mb-3" role="alert" style="font-size: 13.5px;">
            {{ successMessage }}
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary submit-btn w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
            Tạo tài khoản
          </button>

          <div class="auth-footer text-center mt-3">
            <span>Đã có tài khoản? </span>
            <a href="javascript:void(0)" class="login-link" @click="switchMode(true)">Đăng nhập</a>
          </div>
        </form>

        <!-- ========== FORM ĐĂNG NHẬP ========== -->
        <form v-else @submit.prevent="handleLogin">
          <!-- Chọn loại tài khoản -->
          <div class="mb-3">
            <label class="form-label">Đăng nhập với tư cách</label>
            <div class="role-selector">
              <button
                type="button"
                :class="['role-btn', { active: loginForm.role === 'hoc_vien' }]"
                @click="loginForm.role = 'hoc_vien'"
              >
                <i class="fa-solid fa-user-graduate me-2"></i>Học viên
              </button>
              <button
                type="button"
                :class="['role-btn', { active: loginForm.role === 'giao_vien' }]"
                @click="loginForm.role = 'giao_vien'"
              >
                <i class="fa-solid fa-chalkboard-user me-2"></i>Giáo viên
              </button>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input
              v-model.trim="loginForm.email"
              type="email"
              class="form-control custom-input"
              placeholder="example@email.com"
              required
            />
          </div>

          <!-- Password -->
          <div class="mb-2">
            <label class="form-label">Mật khẩu</label>
            <div class="input-password-wrapper">
              <input
                v-model="loginForm.mat_khau"
                :type="showLoginPassword ? 'text' : 'password'"
                class="form-control custom-input"
                placeholder="Mật khẩu của bạn"
                required
              />
              <button type="button" class="toggle-pw-btn" @click="showLoginPassword = !showLoginPassword">
                <i :class="showLoginPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-end mb-4">
            <a href="javascript:void(0)" class="forgot-link">Quên mật khẩu?</a>
          </div>

          <!-- Alert -->
          <div v-if="errorMessage" class="alert alert-danger py-2 px-3 mb-3" role="alert" style="font-size: 13.5px;">
            {{ errorMessage }}
          </div>
          <div v-if="successMessage" class="alert alert-success py-2 px-3 mb-3" role="alert" style="font-size: 13.5px;">
            {{ successMessage }}
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary submit-btn w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
            Đăng nhập
          </button>

          <div class="auth-footer text-center mt-3">
            <span>Chưa có tài khoản? </span>
            <a href="javascript:void(0)" class="login-link" @click="switchMode(false)">Đăng ký ngay</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

import { API_BASE } from '../../services/api';

export default {
  name: "DangKyView",
  data() {
    return {
      isLoginMode: false,
      loading: false,
      errorMessage: "",
      successMessage: "",
      showPassword: false,
      showConfirmPassword: false,
      showLoginPassword: false,

      // Form đăng ký
      form: {
        role: 'hoc_vien',
        ho_ten: "",
        email: "",
        so_dien_thoai: "",
        mat_khau: "",
        mat_khau_confirmation: ""
      },

      // Form đăng nhập
      loginForm: {
        role: 'hoc_vien',
        email: "",
        mat_khau: ""
      }
    };
  },
  methods: {
    switchMode(isLogin) {
      this.isLoginMode = isLogin;
      this.errorMessage = "";
      this.successMessage = "";
    },

    async handleRegister() {
      this.errorMessage = "";
      this.successMessage = "";

      if (this.form.mat_khau !== this.form.mat_khau_confirmation) {
        this.errorMessage = "Mật khẩu và xác nhận mật khẩu không khớp!";
        return;
      }
      if (this.form.mat_khau.length < 6) {
        this.errorMessage = "Mật khẩu phải chứa ít nhất 6 ký tự.";
        return;
      }

      this.loading = true;
      try {
        const endpoint = this.form.role === 'giao_vien'
          ? `${API_BASE}/giao-vien/register`
          : `${API_BASE}/hoc-vien/register`;

        const payload = {
          ho_ten: this.form.ho_ten,
          email: this.form.email,
          so_dien_thoai: this.form.so_dien_thoai,
          password: this.form.mat_khau,
          re_password: this.form.mat_khau_confirmation
        };

        const res = await axios.post(endpoint, payload);

        if (res.data.status) {
          this.successMessage = res.data.message || "Đăng ký thành công! Vui lòng đăng nhập.";
          // Reset form
          this.form = { role: this.form.role, ho_ten: "", email: "", so_dien_thoai: "", mat_khau: "", mat_khau_confirmation: "" };
          setTimeout(() => this.switchMode(true), 1500);
        } else {
          this.errorMessage = res.data.message || "Đăng ký thất bại, vui lòng thử lại.";
        }
      } catch (err) {
        if (err.response && err.response.data) {
          const data = err.response.data;
          if (data.errors) {
            const firstErr = Object.values(data.errors)[0];
            this.errorMessage = Array.isArray(firstErr) ? firstErr[0] : firstErr;
          } else {
            this.errorMessage = data.message || "Có lỗi xảy ra, vui lòng thử lại.";
          }
        } else {
          this.errorMessage = "Không thể kết nối đến máy chủ.";
        }
      } finally {
        this.loading = false;
      }
    },

    async handleLogin() {
      this.errorMessage = "";
      this.successMessage = "";
      this.loading = true;

      try {
        const endpoint = this.loginForm.role === 'giao_vien'
          ? `${API_BASE}/giao-vien/login`
          : `${API_BASE}/hoc-vien/login`;

        const res = await axios.post(endpoint, {
          email: this.loginForm.email,
          password: this.loginForm.mat_khau
        });

        if (res.data.status) {
          // Backend trả về: { status, message, token, user }
          const token = res.data.token;
          const user = res.data.user;
          const role = this.loginForm.role;

          // Lưu token và role vào localStorage
          localStorage.setItem('token', token);
          localStorage.setItem('role', role);
          localStorage.setItem('user', JSON.stringify(user));

          this.successMessage = "Đăng nhập thành công! Đang chuyển hướng...";

          // Chuyển hướng theo role hoặc về trang đã yêu cầu trước đó
          setTimeout(() => {
            const redirectTo = this.$route.query.redirect;
            if (redirectTo) {
              this.$router.push(redirectTo);
            } else if (role === 'giao_vien') {
              this.$router.push('/ho-so-giang-vien');
            } else {
              this.$router.push('/hoc-vien');
            }
          }, 800);
        } else {
          this.errorMessage = res.data.message || "Đăng nhập thất bại.";
        }
      } catch (err) {
        if (err.response && err.response.data) {
          this.errorMessage = err.response.data.message || "Email hoặc mật khẩu không đúng.";
        } else {
          this.errorMessage = "Không thể kết nối đến máy chủ.";
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  background-color: #f3f6fc;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px 15px;
  font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.auth-card {
  width: 100%;
  max-width: 1040px;
  background: #ffffff;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 15px 45px rgba(18, 38, 63, 0.08);
  display: flex;
  flex-direction: row;
  min-height: 680px;
}

/* CỘT TRÁI */
.brand-side {
  width: 44%;
  position: relative;
  background: url("https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80") center center / cover no-repeat;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px;
  overflow: hidden;
}

.brand-bg-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(145deg,
    rgba(255,255,255,0.94) 0%,
    rgba(240,246,255,0.88) 35%,
    rgba(224,238,255,0.72) 70%,
    rgba(215,232,255,0.85) 100%);
  backdrop-filter: blur(2px);
  z-index: 1;
}

.brand-content {
  position: relative;
  z-index: 2;
}

.brand-logo {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 36px;
}

.edu-icon {
  width: 32px;
  height: 32px;
  color: #0060d2;
}

.brand-title {
  font-size: 26px;
  font-weight: 800;
  color: #0056b3;
  letter-spacing: -0.5px;
}

.hero-headline {
  font-size: 26px;
  font-weight: 700;
  color: #1a202c;
  line-height: 1.35;
  margin-bottom: 14px;
}

.hero-description {
  font-size: 14px;
  color: #4a5568;
  line-height: 1.65;
}

.floating-elements {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 1;
}

.floating-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(20px);
}

.orb-1 {
  width: 140px; height: 140px;
  top: 15%; left: 20%;
  background: radial-gradient(circle, rgba(96,165,250,0.45) 0%, rgba(255,255,255,0) 70%);
}

.orb-2 {
  width: 180px; height: 180px;
  bottom: 15%; right: 5%;
  background: radial-gradient(circle, rgba(147,197,253,0.5) 0%, rgba(255,255,255,0) 70%);
}

.floating-book {
  position: absolute;
  color: rgba(99,142,236,0.4);
  font-size: 42px;
  filter: drop-shadow(0 8px 16px rgba(0,80,200,0.15));
}

.book-1 { top: 55%; right: 15%; transform: rotate(-12deg); }
.book-2 { bottom: 20%; left: 30%; transform: rotate(8deg); font-size: 32px; color: rgba(125,172,255,0.35); }

/* CỘT PHẢI */
.form-side {
  width: 56%;
  padding: 44px 48px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #ffffff;
}

/* Tabs chuyển Login / Register */
.auth-tabs {
  display: flex;
  background: #f1f5f9;
  border-radius: 12px;
  padding: 4px;
  margin-bottom: 28px;
  gap: 4px;
}

.tab-btn {
  flex: 1;
  border: none;
  background: transparent;
  border-radius: 9px;
  padding: 10px 0;
  font-size: 14.5px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.25s ease;
}

.tab-btn.active {
  background: #ffffff;
  color: #0060d2;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* Role Selector */
.role-selector {
  display: flex;
  gap: 10px;
}

.role-btn {
  flex: 1;
  border: 1.5px solid #e2e8f0;
  background: #ffffff;
  border-radius: 10px;
  padding: 10px 8px;
  font-size: 13.5px;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
}

.role-btn.active {
  background: #eff6ff;
  border-color: #0060d2;
  color: #0060d2;
  font-weight: 600;
}

.role-btn:hover:not(.active) {
  border-color: #94a3b8;
  background: #f8fafc;
}

/* Labels & Inputs */
.form-label {
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.custom-input {
  height: 42px;
  font-size: 14px;
  color: #1e293b;
  border: 1px solid #d8dee4;
  border-radius: 8px;
  padding: 8px 14px;
  transition: all 0.2s ease;
  background-color: #ffffff;
}

.custom-input::placeholder {
  color: #94a3b8;
  font-size: 13.5px;
}

.custom-input:focus {
  border-color: #0060d2;
  box-shadow: 0 0 0 3px rgba(0, 96, 210, 0.12);
  outline: none;
}

/* Password input với toggle */
.input-password-wrapper {
  position: relative;
}

.input-password-wrapper .custom-input {
  width: 100%;
  padding-right: 42px;
}

.toggle-pw-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
  font-size: 14px;
  padding: 0;
  display: flex;
  align-items: center;
}

.toggle-pw-btn:hover {
  color: #475569;
}

/* Quên mật khẩu */
.forgot-link {
  font-size: 13px;
  color: #0060d2;
  text-decoration: none;
  font-weight: 500;
}

.forgot-link:hover {
  text-decoration: underline;
}

/* Submit Button */
.submit-btn {
  background-color: #0060d2;
  border: none;
  height: 46px;
  border-radius: 8px;
  font-size: 14.5px;
  font-weight: 600;
  color: #ffffff;
  transition: all 0.25s ease;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.2);
}

.submit-btn:hover:not(:disabled) {
  background-color: #004fb0;
  box-shadow: 0 6px 16px rgba(0, 96, 210, 0.32);
  transform: translateY(-1px);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Footer Log In */
.auth-footer {
  font-size: 13.5px;
  color: #64748b;
}

.login-link {
  color: #0060d2;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
}

.login-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 991px) {
  .auth-card {
    flex-direction: column;
    max-width: 520px;
  }
  .brand-side {
    width: 100%;
    min-height: 220px;
    padding: 30px;
  }
  .form-side {
    width: 100%;
    padding: 32px 24px;
  }
}
</style>
