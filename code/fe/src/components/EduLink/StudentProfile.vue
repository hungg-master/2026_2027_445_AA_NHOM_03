<template><ClientProfile /></template>
<script>
import ClientProfile from './ClientProfile.vue';
export default { name: 'StudentProfile', components: { ClientProfile } };
</script>
<style scoped>
.student-profile-page {
  min-height: 100vh;
  background-color: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  padding: 24px 20px 60px;
  display: flex;
  justify-content: center;
}

.profile-container {
  width: 100%;
  max-width: 1240px;
}

/* 1. Breadcrumb & Role Switcher */
.breadcrumb-and-switcher {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  gap: 16px;
  flex-wrap: wrap;
}

.breadcrumb-nav {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 0;
  flex-wrap: wrap;
}

.role-switcher-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  padding: 4px 6px;
  border-radius: 30px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.role-switcher-label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  padding-left: 8px;
}

.role-switcher-pills {
  display: flex;
  gap: 4px;
}

.role-pill {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 20px;
  color: #64748b;
  text-decoration: none;
  transition: all 0.2s ease;
}

.role-pill:hover {
  color: #0060d2;
  background: #f1f5f9;
}

.role-pill.active {
  background: #0060d2;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 96, 210, 0.25);
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

/* 2. Top Profile Header Card */
.profile-header-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 28px 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
  margin-bottom: 26px;
}

.profile-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid #f1f5f9;
}

.profile-info-left {
  display: flex;
  align-items: center;
  gap: 22px;
}

.student-avatar-box {
  position: relative;
  width: 96px;
  height: 96px;
  flex-shrink: 0;
}

.student-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 18px;
  object-fit: cover;
}

.faceid-verified-icon {
  position: absolute;
  bottom: -4px;
  right: -4px;
  background-color: #ffffff;
  color: #16a34a;
  font-size: 18px;
  border-radius: 50%;
  line-height: 1;
}

.student-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}

.student-name {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0;
  letter-spacing: -0.4px;
}

.badge-faceid-verified {
  background-color: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #16a34a;
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 12px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
}

.green-dot {
  width: 7px;
  height: 7px;
  background-color: #16a34a;
  border-radius: 50%;
  display: inline-block;
}

.student-sub-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13.5px;
  color: #475569;
  flex-wrap: wrap;
}

.badge-student-id {
  background-color: #eff6ff;
  color: #0060d2;
  font-weight: 700;
  font-size: 12px;
  padding: 2px 10px;
  border-radius: 6px;
}

.meta-dot {
  color: #cbd5e1;
}

.profile-top-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-action-outline {
  height: 40px;
  padding: 0 16px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 8px;
  background-color: #ffffff;
  border: 1px solid #d8dee4;
  color: #334155;
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-action-outline:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
  color: #0f172a;
}

.btn-download-report {
  height: 40px;
  padding: 0 18px;
  font-size: 13px;
  font-weight: 700;
  border-radius: 8px;
  background-color: #0060d2;
  border: none;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.2);
}

.btn-download-report:hover {
  background-color: #004fb0;
}

/* Stats Highlights Bar */
.profile-stats-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  padding-top: 22px;
}

.stat-highlight-item {
  display: flex;
  align-items: center;
  gap: 14px;
}

.stat-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.icon-blue { background-color: #eff6ff; color: #0060d2; }
.icon-indigo { background-color: #e0e7ff; color: #4338ca; }
.icon-green { background-color: #f0fdf4; color: #16a34a; }
.icon-orange { background-color: #fff7ed; color: #ea580c; }

.stat-top-line {
  line-height: 1.1;
}

.stat-num {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
}

.stat-unit {
  font-size: 12px;
  color: #64748b;
  margin-left: 2px;
}

.stat-label {
  font-size: 12.5px;
  color: #475569;
  font-weight: 500;
  margin-top: 2px;
}

.stat-sub {
  font-size: 11.5px;
  margin-top: 2px;
}

/* 3. Bố cục 2 cột chính */
.profile-layout {
  display: flex;
  gap: 28px;
  align-items: flex-start;
}

.profile-left-column {
  flex: 1 1 64%;
}

.profile-right-column {
  flex: 1 1 36%;
}

/* Content Card chung */
.content-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.card-header-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.card-title {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.3px;
}

.card-sub-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.card-title-icon {
  font-size: 18px;
}

.badge-exam-focus {
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 50px;
}

/* Khung mục tiêu */
.goal-highlight-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 16px 20px;
  margin-top: 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.goal-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.goal-flag-icon {
  font-size: 20px;
}

.goal-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 600;
}

.goal-desc {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
  margin-top: 2px;
}

.goal-percent-box {
  text-align: right;
  flex-shrink: 0;
}

.goal-number {
  font-size: 24px;
  font-weight: 800;
  color: #0060d2;
  line-height: 1;
}

.goal-sub {
  font-size: 11px;
  color: #64748b;
  margin-top: 2px;
}

/* Progress bar */
.overall-progress-wrap {
  margin-top: 16px;
}

.progress-labels-row {
  display: flex;
  justify-content: space-between;
  font-size: 12.5px;
  margin-bottom: 6px;
}

.progress-text-label {
  color: #475569;
  font-weight: 600;
}

.progress-val-label {
  color: #0f172a;
  font-weight: 700;
}

.custom-progress-bar {
  height: 8px;
  border-radius: 50px;
  background-color: #f1f5f9;
}

/* 3 Môn học Progress Grid */
.subjects-progress-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.subject-progress-card {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 14px 16px;
}

.subj-header {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 6px;
}

.subj-name {
  font-weight: 700;
  color: #0f172a;
}

.sub-progress {
  height: 6px;
  border-radius: 50px;
  background-color: #e2e8f0;
  margin-bottom: 6px;
}

.subj-note {
  font-size: 11.5px;
  color: #64748b;
}

.text-warning-dark {
  color: #b45309;
}

/* Enrolled classes */
.view-history-link {
  font-size: 12.5px;
  color: #0060d2;
  text-decoration: none;
  font-weight: 600;
}

.enrolled-classes-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.class-enroll-item {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 16px 20px;
}

.class-enroll-item.highlight-border-blue {
  border-left: 4px solid #0060d2;
  background-color: #fbfdff;
}

.class-item-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.class-pills {
  display: flex;
  gap: 8px;
}

.pill-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 50px;
}

.pill-blue { background-color: #eff6ff; color: #0060d2; }
.pill-purple { background-color: #f3e8ff; color: #9333ea; }
.pill-gray { background-color: #f1f5f9; color: #475569; }

.pill-green-outline {
  border: 1px solid #86efac;
  color: #16a34a;
  background-color: #f0fdf4;
}

.pill-gray-outline {
  border: 1px solid #cbd5e1;
  color: #64748b;
  background-color: #ffffff;
}

.class-upcoming-badge {
  font-size: 12px;
  font-weight: 600;
  color: #ea580c;
}

.class-item-main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.class-item-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.class-item-meta {
  font-size: 12.5px;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 8px;
}

.class-item-buttons {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.btn-class-details {
  font-size: 12px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 8px;
}

.btn-enter-faceid {
  font-size: 12px;
  font-weight: 700;
  padding: 6px 16px;
  border-radius: 8px;
  background-color: #0060d2;
  border: none;
  text-decoration: none;
  color: #ffffff;
}

.btn-enter-faceid:hover {
  background-color: #004fb0;
}

/* Khung giờ rảnh đã đăng ký */
.btn-update-slots {
  font-size: 12px;
  font-weight: 600;
  border-radius: 8px;
}

.week-free-slots-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 8px;
}

.day-slot-card {
  border-radius: 10px;
  padding: 12px 6px;
  text-align: center;
  border: 1px solid #e2e8f0;
}

.day-label {
  font-size: 12px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.slot-time-text {
  font-size: 11px;
}

.slot-booked {
  background-color: #eff6ff;
  border-color: #bfdbfe;
  color: #0060d2;
  font-weight: 600;
}

.slot-busy {
  background-color: #f8fafc;
  color: #94a3b8;
}

.slot-available {
  background-color: #f0fdf4;
  border-color: #bbf7d0;
}

.slots-legend {
  display: flex;
  gap: 16px;
  font-size: 12px;
  color: #64748b;
}

.legend-pill {
  display: flex;
  align-items: center;
  gap: 6px;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.dot-booked { background-color: #0060d2; }
.dot-available { background-color: #16a34a; }

/* Nhận xét giáo viên */
.teacher-feedback-card {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 20px;
}

.feedback-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.teacher-reviewer-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.teacher-reviewer-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
}

.teacher-reviewer-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.teacher-reviewer-role {
  font-size: 11.5px;
  color: #64748b;
}

.feedback-stars {
  font-size: 11px;
}

.stars-num {
  font-size: 12px;
  font-weight: 700;
  color: #0f172a;
}

.feedback-quote {
  font-size: 13.5px;
  color: #334155;
  line-height: 1.6;
  margin-bottom: 10px;
  font-style: italic;
}

.feedback-date {
  font-size: 11.5px;
}

/* RIGHT COLUMN */
/* Face ID status box */
.faceid-status-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 20px;
  text-align: center;
}

.faceid-icon-circle {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}

.faceid-status-title {
  font-size: 15px;
  font-weight: 800;
  color: #16a34a;
  margin-bottom: 2px;
}

.faceid-match-rate {
  font-size: 12px;
  color: #475569;
  margin-bottom: 10px;
}

.faceid-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.45;
  margin-bottom: 14px;
}

.btn-rescan-face {
  font-size: 12.5px;
  font-weight: 600;
  border-radius: 8px;
  padding: 7px 14px;
}

.device-specs-list {
  font-size: 12.5px;
}

.spec-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.spec-label {
  color: #64748b;
}

/* Tuition box */
.tuition-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 16px 18px;
}

.tuition-top-line {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.tuition-term-name {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.tuition-package-sub {
  font-size: 11.5px;
  color: #64748b;
}

.badge-paid {
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 50px;
}

.tuition-price-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.tuition-amount {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

.tuition-currency {
  font-size: 12px;
  color: #64748b;
}

.tuition-vietqr-status {
  font-size: 12px;
  font-weight: 600;
}

.invoice-meta-info {
  font-size: 12px;
}

.invoice-meta-row {
  display: flex;
  justify-content: space-between;
}

.btn-tuition-history {
  font-size: 12.5px;
  font-weight: 600;
  border-radius: 8px;
  padding: 7px 14px;
}

/* Parent info */
.parent-profile-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.parent-avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #eff6ff;
  color: #0060d2;
  font-size: 14px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
}

.parent-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.parent-role {
  font-size: 12px;
  color: #64748b;
}

.parent-contacts-list {
  font-size: 12.5px;
}

.contact-line {
  display: flex;
  align-items: center;
}

.parent-linked-notification {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 10px;
  padding: 10px 12px;
  display: flex;
  align-items: flex-start;
  font-size: 11.5px;
  color: #475569;
  line-height: 1.45;
}

/* Responsive */
@media (max-width: 991px) {
  .profile-layout {
    flex-direction: column;
  }
  .profile-left-column,
  .profile-right-column {
    flex: 1 1 100%;
    width: 100%;
  }
  .profile-top-row {
    flex-direction: column;
    align-items: flex-start;
  }
  .profile-stats-bar {
    grid-template-columns: repeat(2, 1fr);
  }
  .subjects-progress-grid {
    grid-template-columns: 1fr;
  }
  .week-free-slots-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* ===== FACE ID UPLOAD SECTION ===== */
.faceid-current-photo-wrap {
  position: relative;
  display: flex;
  justify-content: center;
  margin-bottom: 4px;
}

.faceid-current-img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e2e8f0;
}

.faceid-verified-badge {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  background: #dcfce7;
  color: #16a34a;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 50px;
  white-space: nowrap;
}

.faceid-pending-badge {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  background: #fff7ed;
  color: #ea580c;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 50px;
  white-space: nowrap;
}

.faceid-upload-area {
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  padding: 20px 12px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
  background: #f8fafc;
}

.faceid-upload-area:hover {
  border-color: #0060d2;
  background: #eff6ff;
}

.faceid-upload-icon {
  font-size: 28px;
  color: #94a3b8;
  margin-bottom: 8px;
  display: block;
}

.faceid-upload-text {
  font-size: 13px;
  color: #475569;
  margin-bottom: 2px;
}

.faceid-upload-hint {
  font-size: 11.5px;
  color: #94a3b8;
  margin-bottom: 0;
}

/* Thanh laser quet chuyen dong doc cho khuon mat */
.scan-line-circle {
  position: absolute;
  width: 180px;
  height: 3px;
  background: linear-gradient(to right, transparent, #ea580c, transparent);
  box-shadow: 0 0 12px #ea580c;
  z-index: 12;
  animation: scan-vertical 2s ease-in-out infinite;
}

.status-text-anim {
  font-weight: bold;
  color: #ea580c;
  transition: all 0.3s ease;
}

@keyframes scan-vertical {
  0% {
    top: 10%;
    opacity: 0;
  }
  50% {
    top: 50%;
    opacity: 1;
  }
  90% {
    top: 90%;
    opacity: 0;
  }
  100% {
    top: 10%;
    opacity: 0;
  }
}

.badge-faceid-unverified {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  color: #854d0e;
  background: #fef9c3;
  padding: 4px 10px;
  border-radius: 20px;
  font-weight: 600;
}

.badge-faceid-verified {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  color: #15803d;
  background: #dcfce7;
  padding: 4px 10px;
  border-radius: 20px;
  font-weight: 600;
}
</style>
