<template>
  <div class="auth-page">
    <div class="auth-card">
      <!-- Cột trái: Hero Brand Banner -->
      <div class="brand-side">
        <div class="brand-bg-overlay"></div>

        <!-- Top navigation/faint header -->
        <div class="brand-top-nav">
          <div class="nav-item">
            <span>Đăng ký</span>
            <i class="fa-solid fa-chevron-down ms-1"></i>
          </div>
          <div class="nav-item">
            <span>Lên tiết báo</span>
          </div>
        </div>

        <div class="brand-content">
          <!-- Logo EduLink -->
          <div class="brand-logo">
            <svg class="edu-icon" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.94 8.13 22 12 22C15.87 22 19 19.94 19 17.18V13.18L12 17L5 13.18Z"/>
            </svg>
            <span class="brand-title">EduLink</span>
          </div>

          <!-- Hero Headline -->
          <h2 class="hero-headline">Join our learning community</h2>
          <p class="hero-description">
            Connect with top educators, manage your schedule seamlessly, and accelerate your educational journey today.
          </p>
        </div>

        <!-- Floating 3D graphics & elements decoration -->
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

      <!-- Cột phải: Form Đăng ký -->
      <div class="form-side">
        <div class="form-header">
          <h3 class="form-title">Create an Account</h3>
          <p class="form-subtitle">Sign up to get started as a student.</p>
        </div>

        <form @submit.prevent="handleRegister">
          <!-- Full Name & Username -->
          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label class="form-label">Full Name</label>
              <input
                v-model.trim="form.fullName"
                type="text"
                class="form-control custom-input"
                placeholder="John Doe"
                required
              />
            </div>
            <div class="col-sm-6">
              <label class="form-label">Username</label>
              <input
                v-model.trim="form.username"
                type="text"
                class="form-control custom-input"
                placeholder="johndoe123"
                required
              />
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input
              v-model.trim="form.email"
              type="email"
              class="form-control custom-input"
              placeholder="john@example.com"
              required
            />
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input
              v-model="form.password"
              type="password"
              class="form-control custom-input"
              placeholder="••••••••"
              required
            />
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input
              v-model="form.confirmPassword"
              type="password"
              class="form-control custom-input"
              placeholder="••••••••"
              required
            />
          </div>

          <!-- Terms Checkbox -->
          <div class="form-check mb-4">
            <input
              id="agreeTerms"
              v-model="form.agreeTerms"
              type="checkbox"
              class="form-check-input custom-checkbox"
              required
            />
            <label class="form-check-label terms-label" for="agreeTerms">
              I agree to the <a href="javascript:void(0)" class="terms-link">Terms and Conditions</a> and
              <a href="javascript:void(0)" class="terms-link">Privacy Policy</a>.
            </label>
          </div>

          <!-- Alert message -->
          <div v-if="errorMessage" class="alert alert-danger py-2 px-3 mb-3" role="alert" style="font-size: 13.5px;">
            {{ errorMessage }}
          </div>
          <div v-if="successMessage" class="alert alert-success py-2 px-3 mb-3" role="alert" style="font-size: 13.5px;">
            {{ successMessage }}
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-primary submit-btn w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
            Create Account
          </button>

          <!-- Divider -->
          <div class="auth-divider">
            <span>Or sign up with</span>
          </div>

          <!-- Social Sign Up -->
          <div class="row g-3 mb-4">
            <div class="col-6">
              <button type="button" class="btn social-btn w-100" @click="handleSocialSignUp('Google')">
                <svg class="social-icon" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                  <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.29 21.41 7.36 24 12 24z"/>
                  <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.16 0 9.97 0 12s.46 3.84 1.26 5.42l4.02-3.15z"/>
                  <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.29 2.59 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
                <span>Google</span>
              </button>
            </div>
            <div class="col-6">
              <button type="button" class="btn social-btn w-100" @click="handleSocialSignUp('Facebook')">
                <svg class="social-icon" viewBox="0 0 24 24" fill="#1877F2">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Facebook</span>
              </button>
            </div>
          </div>

          <!-- Footer Link -->
          <div class="auth-footer text-center">
            <span>Already have an account? </span>
            <router-link to="/login" class="login-link">Log In</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DangKyView",
  data() {
    return {
      form: {
        fullName: "",
        username: "",
        email: "",
        password: "",
        confirmPassword: "",
        agreeTerms: false
      },
      loading: false,
      errorMessage: "",
      successMessage: ""
    };
  },
  methods: {
    handleRegister() {
      this.errorMessage = "";
      this.successMessage = "";

      if (!this.form.fullName || !this.form.username || !this.form.email || !this.form.password) {
        this.errorMessage = "Vui lòng điền đầy đủ các thông tin yêu cầu.";
        return;
      }

      if (this.form.password !== this.form.confirmPassword) {
        this.errorMessage = "Mật khẩu và xác nhận mật khẩu không khớp!";
        return;
      }

      if (this.form.password.length < 6) {
        this.errorMessage = "Mật khẩu phải chứa ít nhất 6 ký tự.";
        return;
      }

      if (!this.form.agreeTerms) {
        this.errorMessage = "Bạn cần đồng ý với Điều khoản và Chính sách quyền riêng tư.";
        return;
      }

      this.loading = true;
      setTimeout(() => {
        this.loading = false;
        this.successMessage = `Tài khoản ${this.form.username} đã được khởi tạo thành công!`;
      }, 1000);
    },
    handleSocialSignUp(provider) {
      alert(`Đang kết nối xác thực qua ${provider}...`);
    }
  }
};
</script>

<style scoped>
/* Toàn màn hình nền sáng */
.auth-page {
  min-height: 100vh;
  background-color: #f3f6fc;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px 15px;
  font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Khung card bo tròn chuẩn thiết kế */
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

/* CỘT TRÁI - HERO BRAND BANNER */
.brand-side {
  width: 48%;
  position: relative;
  background: url("https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80") center center / cover no-repeat;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 36px 40px;
  overflow: hidden;
}

/* Lớp phủ sáng mờ dịu nhẹ tạo chiều sâu như ảnh mẫu */
.brand-bg-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    145deg,
    rgba(255, 255, 255, 0.94) 0%,
    rgba(240, 246, 255, 0.88) 35%,
    rgba(224, 238, 255, 0.72) 70%,
    rgba(215, 232, 255, 0.85) 100%
  );
  backdrop-filter: blur(2px);
  z-index: 1;
}

/* Top nav mờ ở góc trên banner */
.brand-top-nav {
  position: relative;
  z-index: 2;
  display: flex;
  justify-content: flex-end;
  gap: 20px;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 24px;
}

.brand-top-nav .nav-item {
  cursor: pointer;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}

.brand-top-nav .nav-item:hover {
  color: #0b5ed7;
}

.brand-content {
  position: relative;
  z-index: 2;
  margin-top: 10px;
}

/* Logo EduLink */
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

/* Tiêu đề & mô tả cột trái */
.hero-headline {
  font-size: 27px;
  font-weight: 700;
  color: #1a202c;
  line-height: 1.35;
  margin-bottom: 16px;
  letter-spacing: -0.3px;
}

.hero-description {
  font-size: 14.5px;
  color: #4a5568;
  line-height: 1.6;
  max-width: 360px;
}

/* Floating 3D graphics & elements decoration */
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
  width: 140px;
  height: 140px;
  top: 35%;
  left: 20%;
  background: radial-gradient(circle, rgba(96, 165, 250, 0.45) 0%, rgba(255, 255, 255, 0) 70%);
}

.orb-2 {
  width: 180px;
  height: 180px;
  bottom: 15%;
  right: 10%;
  background: radial-gradient(circle, rgba(147, 197, 253, 0.5) 0%, rgba(255, 255, 255, 0) 70%);
}

.floating-book {
  position: absolute;
  color: rgba(99, 142, 236, 0.45);
  font-size: 42px;
  filter: drop-shadow(0 8px 16px rgba(0, 80, 200, 0.15));
  transform: rotate(-12deg);
}

.book-1 {
  top: 38%;
  right: 18%;
}

.book-2 {
  bottom: 24%;
  left: 35%;
  transform: rotate(8deg);
  font-size: 34px;
  color: rgba(125, 172, 255, 0.4);
}

/* CỘT PHẢI - FORM ĐĂNG KÝ */
.form-side {
  width: 52%;
  padding: 44px 48px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #ffffff;
}

.form-header {
  margin-bottom: 24px;
}

.form-title {
  font-size: 23px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 6px;
  letter-spacing: -0.3px;
}

.form-subtitle {
  font-size: 14px;
  color: #64748b;
  margin-bottom: 0;
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

/* Terms Checkbox */
.terms-label {
  font-size: 13px;
  color: #475569;
  line-height: 1.4;
  user-select: none;
}

.custom-checkbox {
  width: 17px;
  height: 17px;
  border-radius: 4px;
  border: 1px solid #cbd5e1;
  margin-top: 2px;
  cursor: pointer;
}

.custom-checkbox:checked {
  background-color: #0060d2;
  border-color: #0060d2;
}

.terms-link {
  color: #0060d2;
  text-decoration: none;
  font-weight: 500;
}

.terms-link:hover {
  text-decoration: underline;
}

/* Nút Submit Create Account */
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

.submit-btn:hover {
  background-color: #004fb0;
  box-shadow: 0 6px 16px rgba(0, 96, 210, 0.32);
  transform: translateY(-1px);
}

.submit-btn:active {
  transform: translateY(0);
}

/* Divider "Or sign up with" */
.auth-divider {
  position: relative;
  text-align: center;
  margin: 22px 0;
}

.auth-divider::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background-color: #e2e8f0;
}

.auth-divider span {
  position: relative;
  background-color: #ffffff;
  padding: 0 14px;
  font-size: 12.5px;
  color: #64748b;
}

/* Social Buttons */
.social-btn {
  height: 44px;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 13.5px;
  font-weight: 500;
  color: #1e293b;
  transition: all 0.2s ease;
}

.social-btn:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.social-icon {
  width: 18px;
  height: 18px;
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
  margin-left: 4px;
}

.login-link:hover {
  text-decoration: underline;
}

/* Responsive cho màn hình tablet & mobile */
@media (max-width: 991px) {
  .auth-card {
    flex-direction: column;
    max-width: 520px;
  }
  .brand-side {
    width: 100%;
    min-height: 260px;
    padding: 30px;
  }
  .form-side {
    width: 100%;
    padding: 36px 28px;
  }
}
</style>
