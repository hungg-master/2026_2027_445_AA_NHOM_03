<template>
  <footer class="main-footer">
    <div class="footer-container">
      <div class="footer-grid">
        <!-- Col 1: Brand Info -->
        <div class="footer-col footer-brand-col">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon-wrap">
              <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <span class="brand-name">Edu<span>Link</span></span>
          </router-link>
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
            <li><router-link to="/hoc-vien/lop-cua-toi">Khóa học của tôi</router-link></li>
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
            <li><router-link to="/giao-vien">Kiểm định gia sư</router-link></li>
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
</template>

<script>
export default {
  name: "BotKhachHang",
  methods: {
    handleAppStore() {
      alert("Ứng dụng EduLink iOS sẽ sớm có mặt trên App Store!");
    },
    handleGooglePlay() {
      alert("Ứng dụng EduLink Android sẽ sớm có mặt trên Google Play!");
    }
  }
};
</script>

<style scoped>
.main-footer {
  background-color: #ffffff;
  border-top: 1px solid #e2e8f0;
  padding: 50px 20px 24px;
  width: 100%;
  margin-top: auto;
}

.footer-container {
  max-width: 1240px;
  margin: 0 auto;
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
  gap: 40px;
  margin-bottom: 36px;
}

@media (max-width: 991px) {
  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 24px;
  }
}

@media (max-width: 576px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
}

.brand-logo {
  display: inline-flex;
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
  border-top: 1px solid #f1f5f9;
  padding-top: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
  font-size: 12px;
  color: #94a3b8;
}

.footer-contact-links {
  display: flex;
  align-items: center;
  gap: 10px;
}

.contact-sep {
  color: #cbd5e1;
}
</style>
