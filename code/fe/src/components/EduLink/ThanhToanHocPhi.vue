<template>
  <div class="payment-page">
    <div class="payment-container">
      <!-- 1. BREADCRUMB -->
      <nav class="breadcrumb-nav">
        <span class="crumb-item">Cổng học viên</span>
        <i class="fa-solid fa-chevron-right crumb-sep"></i>
        <span class="crumb-item">Tài chính & Học phí</span>
        <i class="fa-solid fa-chevron-right crumb-sep"></i>
        <span class="crumb-item active">Thanh toán trực tuyến</span>
      </nav>

      <!-- 2. HEADER: TIÊU ĐỀ & THÔNG TIN HỌC VIÊN -->
      <div class="payment-header-area">
        <div class="header-left-info">
          <h1 class="page-title">Thanh toán Học phí</h1>
          <p class="page-subtitle">
            Quản lý và hoàn tất nghĩa vụ học phí học kỳ II (2024 - 2025) an toàn, nhanh chóng.
          </p>
        </div>

        <!-- Thẻ thông tin học viên góc phải -->
        <div class="student-id-card">
          <div class="student-avatar-wrap">NL</div>
          <div class="student-text-wrap">
            <div class="student-name">Nguyễn Linh Lan</div>
            <div class="student-code">Mã HV: EDU - 202488</div>
          </div>
        </div>
      </div>

      <!-- 3. BỐ CỤC CHÍNH 2 CỘT -->
      <div class="payment-layout">
        <!-- CỘT TRÁI: KHOẢN THU & PHƯƠNG THỨC THANH TOÁN -->
        <div class="left-column">
          <!-- CARD 1: KHOẢN THU KỲ HỌC HIỆN TẠI -->
          <div class="content-card">
            <div class="card-header-row">
              <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-book-open text-primary fs-5"></i>
                <h2 class="card-title mb-0">Khoản thu kỳ học hiện tại</h2>
              </div>
              <span class="term-badge">Học kỳ II • 2024-2025</span>
            </div>

            <!-- Danh sách môn học -->
            <div class="courses-list">
              <!-- Môn 1 -->
              <div class="course-item">
                <div class="course-left">
                  <div class="course-icon-box">
                    <i class="fa-solid fa-calculator text-primary"></i>
                  </div>
                  <div class="course-info">
                    <div class="course-name">Toán học Nâng cao 301</div>
                    <div class="course-sub">Mã MH: MATH-301 • 3 Tín chỉ • Lớp 12A1</div>
                  </div>
                </div>
                <div class="course-right">
                  <div class="course-price">650.000 đ</div>
                  <div class="badge-mandatory">Bắt buộc</div>
                </div>
              </div>

              <!-- Môn 2 -->
              <div class="course-item">
                <div class="course-left">
                  <div class="course-icon-box">
                    <i class="fa-solid fa-code text-primary"></i>
                  </div>
                  <div class="course-info">
                    <div class="course-name">Lập trình Ứng dụng Web</div>
                    <div class="course-sub">Mã MH: CS-204 • 3 Tín chỉ • Thực hành LAB 03</div>
                  </div>
                </div>
                <div class="course-right">
                  <div class="course-price">550.000 đ</div>
                  <div class="badge-mandatory">Bắt buộc</div>
                </div>
              </div>
            </div>

            <!-- Footer của thẻ khoản thu: Hạn nộp & Miễn phí phí GD -->
            <div class="card-footer-bar">
              <div class="deadline-text">
                Hạn nộp học phí: <strong>15/04/2025</strong>
              </div>
              <div class="fee-free-text">
                <i class="fa-regular fa-circle-check text-success me-1"></i>
                <span>Miễn phí phí giao dịch cổng trường</span>
              </div>
            </div>
          </div>

          <!-- CARD 2: PHƯƠNG THỨC THANH TOÁN -->
          <div class="content-card mt-4">
            <h2 class="card-title">Phương thức thanh toán</h2>
            <p class="card-subtitle">Chọn kênh thuận tiện nhất để tiến hành nộp học phí</p>

            <!-- 3 Kênh thanh toán lựa chọn -->
            <div class="payment-methods-grid">
              <!-- Kênh 1: VietQR (Active) -->
              <div
                :class="['method-card', { 'active': selectedMethod === 'vietqr' }]"
                @click="selectedMethod = 'vietqr'"
              >
                <div class="method-badge">Khuyên dùng</div>
                <div class="method-icon-wrap">
                  <i class="fa-solid fa-qrcode text-primary"></i>
                </div>
                <div class="method-radio-wrap">
                  <span class="custom-radio" :class="{ 'checked': selectedMethod === 'vietqr' }"></span>
                </div>
                <div class="method-title">VietQR Nhanh</div>
                <div class="method-desc">Quét qua App ngân hàng</div>
              </div>

              <!-- Kênh 2: Thẻ Quốc tế / ATM -->
              <div
                :class="['method-card', { 'active': selectedMethod === 'card' }]"
                @click="selectedMethod = 'card'"
              >
                <div class="method-icon-wrap">
                  <i class="fa-regular fa-credit-card text-secondary"></i>
                </div>
                <div class="method-radio-wrap">
                  <span class="custom-radio" :class="{ 'checked': selectedMethod === 'card' }"></span>
                </div>
                <div class="method-title">Thẻ Quốc tế / ATM</div>
                <div class="method-desc">Visa, Mastercard, Napas</div>
              </div>

              <!-- Kênh 3: Ví điện tử -->
              <div
                :class="['method-card', { 'active': selectedMethod === 'ewallet' }]"
                @click="selectedMethod = 'ewallet'"
              >
                <div class="method-icon-wrap">
                  <i class="fa-solid fa-wallet text-secondary"></i>
                </div>
                <div class="method-radio-wrap">
                  <span class="custom-radio" :class="{ 'checked': selectedMethod === 'ewallet' }"></span>
                </div>
                <div class="method-title">Ví điện tử</div>
                <div class="method-desc">MoMo, ZaloPay, ShopeePay</div>
              </div>
            </div>

            <!-- KHUNG CHI TIẾT VIETQR NAPAS 247 (Hiển thị khi chọn VietQR) -->
            <div v-if="selectedMethod === 'vietqr'" class="qr-details-box">
              <div class="qr-code-wrapper">
                <!-- Mã QR VietQR Napas -->
                <div class="qr-box">
                  <svg class="qr-svg" viewBox="0 0 160 160">
                    <!-- Khung viền góc QR -->
                    <rect x="10" y="10" width="40" height="40" rx="6" fill="none" stroke="#0f172a" stroke-width="6"/>
                    <rect x="22" y="22" width="16" height="16" rx="2" fill="#0060d2"/>

                    <rect x="110" y="10" width="40" height="40" rx="6" fill="none" stroke="#0f172a" stroke-width="6"/>
                    <rect x="122" y="22" width="16" height="16" rx="2" fill="#0060d2"/>

                    <rect x="10" y="110" width="40" height="40" rx="6" fill="none" stroke="#0f172a" stroke-width="6"/>
                    <rect x="22" y="122" width="16" height="16" rx="2" fill="#0060d2"/>

                    <!-- Hoa văn QR ngẫu nhiên tinh tế -->
                    <rect x="62" y="20" width="12" height="12" fill="#0f172a"/>
                    <rect x="82" y="20" width="16" height="8" fill="#0060d2"/>
                    <rect x="62" y="42" width="20" height="10" fill="#0f172a"/>
                    <rect x="90" y="42" width="8" height="18" fill="#0060d2"/>

                    <rect x="20" y="66" width="14" height="12" fill="#0f172a"/>
                    <rect x="42" y="66" width="18" height="8" fill="#0060d2"/>
                    <rect x="70" y="66" width="20" height="20" fill="#0f172a" rx="3"/>
                    <rect x="100" y="70" width="16" height="14" fill="#0060d2"/>
                    <rect x="126" y="66" width="14" height="18" fill="#0f172a"/>

                    <rect x="20" y="90" width="10" height="10" fill="#0f172a"/>
                    <rect x="40" y="86" width="18" height="12" fill="#0f172a"/>
                    <rect x="100" y="94" width="14" height="20" fill="#0060d2"/>
                    <rect x="124" y="94" width="16" height="12" fill="#0f172a"/>

                    <rect x="60" y="100" width="12" height="24" fill="#0f172a"/>
                    <rect x="80" y="110" width="12" height="14" fill="#0060d2"/>
                    <rect x="70" y="132" width="24" height="16" fill="#0f172a"/>
                    <rect x="104" y="124" width="16" height="12" fill="#0060d2"/>
                    <rect x="128" y="120" width="12" height="22" fill="#0f172a"/>
                  </svg>
                </div>
                <div class="qr-brand-text">VietQR Napas 247</div>
              </div>

              <!-- Chi tiết thông tin chuyển khoản -->
              <div class="qr-info-table">
                <div class="info-row">
                  <div class="info-label">Ngân hàng thụ hưởng</div>
                  <div class="info-value fw-bold">Ngân hàng TMCP Quân Đội (MBBank)</div>
                </div>

                <div class="info-row">
                  <div class="info-label">Số tài khoản</div>
                  <div class="info-value">
                    <span class="account-num fw-bold">8888 2024 9999</span>
                    <button class="copy-btn-inline" @click="copyText('888820249999', 'Số tài khoản')">
                      <i class="fa-regular fa-copy"></i>
                    </button>
                  </div>
                </div>

                <div class="info-row">
                  <div class="info-label">Tên chủ tài khoản</div>
                  <div class="info-value fw-bold text-uppercase">TRUONG DAI HOC CONG NGHE EDU</div>
                </div>

                <div class="info-row">
                  <div class="info-label">Số tiền</div>
                  <div class="info-value">
                    <span class="price-highlight">1.125.000 đ</span>
                    <button class="copy-btn-inline" @click="copyText('1125000', 'Số tiền')">
                      <i class="fa-regular fa-copy"></i>
                    </button>
                  </div>
                </div>

                <!-- Box nội dung chuyển khoản bắt buộc -->
                <div class="memo-transfer-box">
                  <div class="memo-label">Nội dung chuyển khoản (bắt buộc)</div>
                  <div class="memo-content-row">
                    <span class="memo-code">HP2 EDU202488 NGUYENLINHLAN</span>
                    <button class="btn btn-copy-memo" @click="copyText('HP2 EDU202488 NGUYENLINHLAN', 'Nội dung chuyển khoản')">
                      <i class="fa-regular fa-copy me-1"></i> Sao chép
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ghi chú lưu ý dưới QR -->
            <div class="qr-note-box">
              <i class="fa-solid fa-circle-info note-icon text-success"></i>
              <span>
                Ứng dụng ngân hàng sẽ tự động điền đầy đủ số tiền và nội dung chuyển khoản sau khi quét mã QR. Vui lòng không thay đổi mã nội dung để học phí được gạch nợ tức thì.
              </span>
            </div>
          </div>
        </div>

        <!-- CỘT PHẢI: TÓM TẮT CHI PHÍ & LỊCH SỬ -->
        <div class="right-column">
          <!-- CARD 1: TÓM TẮT CHI PHÍ -->
          <div class="content-card">
            <h2 class="card-title mb-3">Tóm tắt chi phí</h2>

            <div class="summary-lines">
              <div class="summary-row">
                <span class="summary-label">Toán học Nâng cao 301</span>
                <span class="summary-val">650.000 đ</span>
              </div>

              <div class="summary-row">
                <span class="summary-label">Lập trình Ứng dụng Web</span>
                <span class="summary-val">550.000 đ</span>
              </div>

              <div class="summary-row subtotal-row">
                <span class="summary-label">Tạm tính</span>
                <span class="summary-val">1.200.000 đ</span>
              </div>

              <div class="summary-row discount-row text-success">
                <span class="summary-label">
                  <i class="fa-solid fa-tag me-1"></i> Học bổng khuyến khích (5%)
                </span>
                <span class="summary-val fw-bold">-75.000 đ</span>
              </div>
            </div>

            <hr class="summary-divider" />

            <!-- Tổng thanh toán -->
            <div class="total-row">
              <div class="total-left">
                <div class="total-label">Tổng thanh toán</div>
                <div class="total-sub">Đã bao gồm thuế và tài liệu số</div>
              </div>
              <div class="total-right text-end">
                <div class="total-amount">1.125.000 đ</div>
                <div class="total-usd">~ $45.20 USD</div>
              </div>
            </div>

            <!-- Nút xác nhận chuyển khoản -->
            <button class="btn btn-confirm-pay w-100" @click="handleConfirmPayment">
              <i class="fa-regular fa-circle-check me-2"></i> Xác nhận đã chuyển khoản
            </button>

            <!-- Ghi chú gửi hóa đơn điện tử -->
            <div class="invoice-tip-box">
              <i class="fa-regular fa-envelope tip-mail-icon text-primary"></i>
              <span>
                Hệ thống sẽ gửi hóa đơn điện tử hợp lệ (e-Invoice) qua email
                <strong>linhlan.nguyen@edu.vn</strong> ngay khi giao dịch hoàn tất.
              </span>
            </div>
          </div>

          <!-- CARD 2: LỊCH SỬ THANH TOÁN GẦN ĐÂY -->
          <div class="content-card mt-4">
            <div class="card-header-row mb-3">
              <h3 class="card-title fs-6 mb-0">Lịch sử thanh toán gần đây</h3>
              <a href="javascript:void(0)" class="view-all-link">Xem tất cả</a>
            </div>

            <div class="history-list">
              <!-- Giao dịch 1 -->
              <div class="history-item">
                <div class="history-left">
                  <div class="history-check-circle">
                    <i class="fa-solid fa-check"></i>
                  </div>
                  <div class="history-info">
                    <div class="history-name">Học kỳ I (2024 - 2025)</div>
                    <div class="history-date">12/09/2024 • VietQR</div>
                  </div>
                </div>
                <div class="history-right text-end">
                  <div class="history-amount">1.850.000 đ</div>
                  <span class="badge-success-pill">Thành công</span>
                </div>
              </div>

              <!-- Giao dịch 2 -->
              <div class="history-item">
                <div class="history-left">
                  <div class="history-check-circle">
                    <i class="fa-solid fa-check"></i>
                  </div>
                  <div class="history-info">
                    <div class="history-name">Lệ phí thi chuẩn đầu ra B2</div>
                    <div class="history-date">04/06/2024 • Visa Debit</div>
                  </div>
                </div>
                <div class="history-right text-end">
                  <div class="history-amount">450.000 đ</div>
                  <span class="badge-success-pill">Thành công</span>
                </div>
              </div>
            </div>
          </div>

          <!-- CARD 3: HỖ TRỢ PHÒNG TÀI VỤ -->
          <div class="support-banner mt-3">
            <div class="support-left">
              <i class="fa-solid fa-headset support-icon text-primary"></i>
              <div>
                <div class="support-title">Cần trợ giúp nộp học phí?</div>
                <div class="support-phone">Phòng Tài vụ: (028) 3824 9999</div>
              </div>
            </div>
            <button class="btn btn-chat-support" @click="handleChatSupport">
              Chat tư vấn
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "EduLinkThanhToanHocPhi",
  data() {
    return {
      selectedMethod: "vietqr"
    };
  },
  methods: {
    copyText(text, label) {
      navigator.clipboard.writeText(text).then(() => {
        alert(`Đã sao chép ${label}: ${text}`);
      }).catch(() => {
        alert(`Sao chép: ${text}`);
      });
    },
    handleConfirmPayment() {
      alert("Hệ thống đang kiểm tra giao dịch chuyển khoản cho học viên Nguyễn Linh Lan (EDU-202488). Hóa đơn e-Invoice sẽ được gửi vào email sau khi đối soát thành công!");
    },
    handleChatSupport() {
      alert("Đang kết nối tới chuyên viên tư vấn Phòng Tài vụ EduLink...");
    }
  }
};
</script>

<style scoped>
.payment-page {
  min-height: 100vh;
  background-color: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  padding: 30px 20px 60px;
  display: flex;
  justify-content: center;
}

.payment-container {
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
  margin-bottom: 18px;
}

.crumb-sep {
  font-size: 10px;
  color: #94a3b8;
}

.crumb-item.active {
  color: #0060d2;
  font-weight: 600;
}

/* 2. Header Area */
.payment-header-area {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  gap: 20px;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.5px;
  margin-bottom: 6px;
}

.page-subtitle {
  font-size: 14.5px;
  color: #64748b;
  margin-bottom: 0;
}

.student-id-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 10px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
  flex-shrink: 0;
}

.student-avatar-wrap {
  width: 38px;
  height: 38px;
  background-color: #eff6ff;
  color: #0060d2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.student-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.2;
}

.student-code {
  font-size: 12px;
  color: #64748b;
}

/* 3. Layout 2 cột */
.payment-layout {
  display: flex;
  gap: 28px;
  align-items: flex-start;
}

.left-column {
  flex: 1 1 58%;
}

.right-column {
  flex: 1 1 42%;
}

/* Card Content chung */
.content-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.card-title {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.3px;
}

.card-subtitle {
  font-size: 13px;
  color: #64748b;
  margin-top: 2px;
  margin-bottom: 16px;
}

.card-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.term-badge {
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 50px;
}

/* Danh sách môn học */
.courses-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-bottom: 20px;
}

.course-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background-color: #f8fafc;
  border-radius: 12px;
  border: 1px solid #f1f5f9;
}

.course-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.course-icon-box {
  width: 38px;
  height: 38px;
  background-color: #eff6ff;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}

.course-name {
  font-size: 14.5px;
  font-weight: 700;
  color: #0f172a;
}

.course-sub {
  font-size: 12.5px;
  color: #64748b;
  margin-top: 2px;
}

.course-price {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  text-align: right;
}

.badge-mandatory {
  font-size: 11.5px;
  color: #16a34a;
  font-weight: 600;
  text-align: right;
}

/* Card footer bar */
.card-footer-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 16px;
  border-top: 1px solid #f1f5f9;
  font-size: 13px;
}

.deadline-text {
  color: #475569;
}

.fee-free-text {
  color: #16a34a;
  font-weight: 500;
}

/* Payment Methods Grid */
.payment-methods-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 20px;
}

.method-card {
  position: relative;
  background-color: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.method-card:hover {
  background-color: #f1f5f9;
}

.method-card.active {
  background-color: #f8fbff;
  border-color: #0060d2;
}

.method-badge {
  position: absolute;
  top: -9px;
  right: 12px;
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 50px;
}

.method-icon-wrap {
  font-size: 20px;
  margin-bottom: 8px;
}

.method-radio-wrap {
  position: absolute;
  top: 16px;
  right: 14px;
}

.custom-radio {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid #cbd5e1;
  display: inline-block;
  position: relative;
}

.custom-radio.checked {
  border-color: #0060d2;
}

.custom-radio.checked::after {
  content: "";
  position: absolute;
  inset: 3px;
  border-radius: 50%;
  background-color: #0060d2;
}

.method-title {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.method-desc {
  font-size: 11.5px;
  color: #64748b;
  margin-top: 2px;
}

/* Khung chi tiết VietQR */
.qr-details-box {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 24px;
  display: flex;
  gap: 24px;
  align-items: center;
}

.qr-code-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex-shrink: 0;
}

.qr-box {
  width: 140px;
  height: 140px;
  background-color: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}

.qr-svg {
  width: 100%;
  height: 100%;
}

.qr-brand-text {
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  margin-top: 8px;
}

/* Bảng thông tin chuyển khoản */
.qr-info-table {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.info-row {
  display: flex;
  flex-direction: column;
  font-size: 13px;
}

.info-label {
  color: #64748b;
  font-size: 12px;
  margin-bottom: 2px;
}

.info-value {
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 8px;
}

.copy-btn-inline {
  background: transparent;
  border: none;
  color: #0060d2;
  cursor: pointer;
  padding: 2px 4px;
  font-size: 13px;
}

.price-highlight {
  color: #0060d2;
  font-weight: 800;
  font-size: 15.5px;
}

/* Box Memo Code */
.memo-transfer-box {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px 14px;
  margin-top: 4px;
}

.memo-label {
  font-size: 11.5px;
  color: #64748b;
  margin-bottom: 4px;
}

.memo-content-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.memo-code {
  font-weight: 800;
  font-size: 13px;
  color: #0f172a;
  letter-spacing: 0.5px;
}

.btn-copy-memo {
  background-color: #eff6ff;
  border: 1px solid #dbeafe;
  color: #0060d2;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-copy-memo:hover {
  background-color: #dbeafe;
}

.qr-note-box {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.45;
  margin-top: 14px;
}

.note-icon {
  margin-top: 2px;
  font-size: 13px;
}

/* Right Column: Tóm tắt chi phí */
.summary-lines {
  display: flex;
  flex-direction: column;
  gap: 10px;
  font-size: 13.5px;
}

.summary-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.summary-label {
  color: #475569;
}

.summary-val {
  color: #0f172a;
  font-weight: 600;
}

.subtotal-row {
  font-weight: 700;
  padding-top: 4px;
}

.summary-divider {
  margin: 18px 0;
  border-color: #e2e8f0;
}

.total-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 20px;
}

.total-label {
  font-size: 15.5px;
  font-weight: 800;
  color: #0f172a;
}

.total-sub {
  font-size: 11.5px;
  color: #64748b;
  margin-top: 2px;
}

.total-amount {
  font-size: 26px;
  font-weight: 800;
  color: #0060d2;
  line-height: 1.1;
}

.total-usd {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.btn-confirm-pay {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  height: 48px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  box-shadow: 0 4px 14px rgba(0, 96, 210, 0.25);
  transition: all 0.2s ease;
}

.btn-confirm-pay:hover {
  background-color: #004fb0;
  color: #ffffff;
  transform: translateY(-1px);
}

.invoice-tip-box {
  background-color: #f0f7ff;
  border: 1px solid #e0f2fe;
  border-radius: 10px;
  padding: 12px 14px;
  margin-top: 16px;
  display: flex;
  gap: 10px;
  align-items: flex-start;
  font-size: 12.5px;
  color: #334155;
  line-height: 1.45;
}

.tip-mail-icon {
  margin-top: 2px;
  font-size: 14px;
}

/* Lịch sử thanh toán */
.view-all-link {
  font-size: 12.5px;
  color: #0060d2;
  text-decoration: none;
  font-weight: 600;
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.history-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.history-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.history-check-circle {
  width: 32px;
  height: 32px;
  background-color: #dcfce7;
  color: #16a34a;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
}

.history-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.history-date {
  font-size: 12px;
  color: #64748b;
  margin-top: 1px;
}

.history-amount {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.badge-success-pill {
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 50px;
  display: inline-block;
}

/* Hỗ trợ Banner */
.support-banner {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.support-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.support-icon {
  font-size: 20px;
}

.support-title {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.support-phone {
  font-size: 12px;
  color: #64748b;
  margin-top: 1px;
}

.btn-chat-support {
  background-color: #ffffff;
  border: 1px solid #cbd5e1;
  color: #1e293b;
  font-size: 12.5px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-chat-support:hover {
  background-color: #f1f5f9;
}

/* Responsive */
@media (max-width: 991px) {
  .payment-layout {
    flex-direction: column;
  }
  .left-column,
  .right-column {
    flex: 1 1 100%;
    width: 100%;
  }
  .payment-methods-grid {
    grid-template-columns: 1fr;
  }
  .qr-details-box {
    flex-direction: column;
    text-align: center;
  }
}
</style>
