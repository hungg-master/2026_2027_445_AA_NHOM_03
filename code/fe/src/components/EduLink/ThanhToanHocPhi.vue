<template>
  <main class="payment-page"><div class="payment-container">
    <div class="breadcrumb-nav"><router-link to="/hoc-vien/lop-cua-toi">Lớp của tôi</router-link> / Học phí</div>
    <h1 class="h3 mb-3">Học phí và lịch sử giao dịch</h1>
    <p class="alert alert-info">Chế độ thử nghiệm: yêu cầu thanh toán được chờ quản trị viên đối soát. Trạng thái học phí chỉ đổi sau xác nhận trên máy chủ.</p>
    <p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p><p v-if="message" class="alert alert-info" role="status">{{ message }}</p>
    <p v-if="loading">Đang tải học phí…</p><p v-else-if="!rows.length">Chưa có khoản học phí.</p>
    <div class="payment-layout"><section class="content-card p-4" style="width:100%;">
      <article v-for="row in rows" :key="row.id" class="border-bottom pb-3 mb-3">
        <h3 class="h5">{{ row.ten_mon_hoc || 'Học phí lớp #' + row.id_lop_hoc }}</h3>
        <p class="fs-5 fw-bold">{{ money(row.amount) }} {{ row.currency }}</p><p>Trạng thái: {{ row.status === 'paid' ? 'Đã đối soát thanh toán' : row.status === 'cancelled' ? 'Đã hủy nghĩa vụ' : 'Chưa thanh toán' }}</p>
        <div v-if="row.status === 'pending'" class="d-flex flex-wrap gap-2"><button class="btn btn-primary" :disabled="busy === row.id" @click="pay(row, 'success')">{{ busy === row.id ? 'Đang gửi…' : 'Gửi yêu cầu thanh toán thử' }}</button><button class="btn btn-outline-secondary" :disabled="busy === row.id" @click="pay(row, 'failed')">Thử giao dịch thất bại</button></div>
        <h4 class="h6 mt-3">Lịch sử</h4><p v-if="!row.transactions?.length">Chưa có giao dịch.</p><p v-for="tx in row.transactions" :key="tx.id">#{{ tx.id }} · {{ tx.created_at }} · {{ transactionStatus(tx.status) }} · {{ money(tx.amount || row.amount) }}</p>
      </article>
      <router-link to="/tu-van">Mở hội thoại tư vấn</router-link>
    </section></div>
  </div></main>
</template>
<script>
import support from '../../services/supportService';
export default {
  name: 'EduLinkThanhToanHocPhi',
  data: () => ({ rows: [], loading: false, busy: null, error: '', message: '', keys: {} }),
  mounted() { this.load(); },
  methods: {
    money(value) { return new Intl.NumberFormat('vi-VN').format(value || 0); },
    transactionStatus(value) { return { awaiting_manual: 'Chờ đối soát thử nghiệm', failed: 'Thất bại', confirmed: 'Đã xác nhận', settled: 'Đã đối soát', success: 'Đã xác nhận' }[value] || value; },
    async load() { this.loading = true; try { this.rows = (await support.tuition()).data || []; } catch (error) { this.error = error.message; } finally { this.loading = false; } },
    async pay(row, outcome) { if (this.busy) return; this.busy = row.id; this.error = ''; this.message = ''; const scope = `${row.id}:${outcome}`; const key = this.keys[scope] || crypto.randomUUID(); this.keys[scope] = key; try { const result = await support.payment(row.id, key, outcome); this.message = result.message || 'Máy chủ đã tiếp nhận yêu cầu thử nghiệm.'; await this.load(); } catch (error) { this.error = error.message; } finally { this.busy = null; } }
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
