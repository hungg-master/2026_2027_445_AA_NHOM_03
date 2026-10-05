<template>
  <main class="lesson-review-page"><div class="container py-4">
    <h1 class="h3">Đánh giá buổi học</h1><p>Đánh giá được lưu cho buổi đã hoàn thành mà bạn đã tham dự. Máy chủ kiểm tra điều kiện trước khi lưu.</p>
    <p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p><p v-if="message" class="alert alert-success" role="status">{{ message }}</p>
    <div class="row g-3"><section v-if="role === 'hoc_vien'" class="col-md-6"><form class="content-card card p-4 d-grid gap-3" @submit.prevent="submit">
      <label>Buổi học<select v-model="sessionId" class="form-select" required><option value="">Chọn buổi đã hoàn thành</option><option v-for="row in sessions" :key="row.id_buoi_hoc" :value="row.id_buoi_hoc">{{ row.lop_hoc?.mon_hoc?.ten_mon_hoc }} · {{ new Date(row.thoi_gian_bat_dau).toLocaleString('vi-VN') }}</option></select></label>
      <p v-if="!sessions.length">Chưa có buổi đã hoàn thành trong lịch.</p><label>Điểm đánh giá<select v-model.number="rating" class="form-select"><option v-for="n in 5" :key="n" :value="n">{{ n }} sao</option></select></label>
      <label>Nhận xét<textarea v-model.trim="comment" class="form-control" rows="5" maxlength="2000"></textarea></label><button class="btn btn-primary" :disabled="busy || !sessionId">{{ busy ? 'Đang gửi…' : 'Lưu đánh giá' }}</button>
    </form></section><aside class="col"><div class="content-card card p-4"><h2 class="h5">{{ role === 'giao_vien' ? 'Đánh giá nhận được' : 'Đánh giá của bạn' }}</h2><p v-if="!reviews.length">Chưa có đánh giá.</p><article v-for="row in reviews" :key="row.id" class="border-bottom pb-3 mb-3"><strong>{{ row.rating }}/5 · Buổi #{{ row.id_buoi_hoc }}</strong><p>{{ row.comment || 'Không có nhận xét' }}</p><small>{{ row.created_at }}</small></article></div></aside></div>
  </div></main>
</template>
<script>
import support from '../../services/supportService';
import { lichHocService } from '../../services/lichHocService';
export default {
  name: 'LessonReview',
  data() { return { role: localStorage.getItem('role'), sessions: [], reviews: [], sessionId: this.$route.query.session || '', rating: 5, comment: '', error: '', message: '', busy: false }; },
  mounted() { this.load(); },
  methods: {
    async load() { try { this.reviews = (await support.reviews(this.role)).data || []; if (this.role === 'hoc_vien') { const result = await lichHocService.getLichHoc(); this.sessions = (result.data || []).filter(row => row.trang_thai === 'completed'); } } catch (error) { this.error = error.message; } },
    async submit() { this.busy = true; this.error = ''; this.message = ''; try { const result = await support.review({ id_buoi_hoc: Number(this.sessionId), rating: this.rating, comment: this.comment }); this.message = result.message || 'Đã lưu đánh giá.'; await this.load(); } catch (error) { this.error = error.message; } finally { this.busy = false; } }
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
