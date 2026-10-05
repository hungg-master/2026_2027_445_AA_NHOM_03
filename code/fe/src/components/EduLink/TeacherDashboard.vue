<template><ClientProfile /></template>
<script>
import ClientProfile from './ClientProfile.vue';
export default { name: 'TeacherDashboard', components: { ClientProfile } };
</script>
<style scoped>
.teacher-dashboard-page {
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
  flex-wrap: wrap;
}

.profile-info-left {
  display: flex;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}

.teacher-avatar-box {
  position: relative;
  width: 96px;
  height: 96px;
  flex-shrink: 0;
}

.teacher-avatar-img {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #ffffff;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
}

.faceid-verified-icon {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background-color: #10b981;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  border: 2.5px solid #ffffff;
}

.teacher-meta-details {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.teacher-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.teacher-name {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  letter-spacing: -0.3px;
}

.badge-faceid-verified {
  background-color: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
}

.green-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #10b981;
}

.teacher-sub-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13.5px;
  color: #475569;
  flex-wrap: wrap;
}

.badge-teacher-id {
  background-color: #eff6ff;
  color: #0060d2;
  font-weight: 600;
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 6px;
}

.meta-dot {
  color: #94a3b8;
}

.profile-top-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-action-outline {
  background-color: #ffffff;
  border: 1px solid #cbd5e1;
  color: #334155;
  font-size: 13.5px;
  font-weight: 600;
  padding: 9px 16px;
  border-radius: 10px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: all 0.15s ease;
}

.btn-action-outline:hover {
  background-color: #f1f5f9;
  border-color: #94a3b8;
  color: #0f172a;
}

.btn-download-report {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 13.5px;
  font-weight: 600;
  padding: 9px 18px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
  display: inline-flex;
  align-items: center;
}

.btn-download-report:hover {
  background-color: #004fb0;
  border-color: #004fb0;
}

/* Stats bar */
.profile-stats-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 28px;
  padding-top: 24px;
  border-top: 1px solid #f1f5f9;
}

.stat-highlight-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 14px;
  background-color: #f8fafc;
  border-radius: 14px;
  border: 1px solid #f1f5f9;
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

.icon-blue {
  background-color: #eff6ff;
  color: #0060d2;
}

.icon-indigo {
  background-color: #eef2ff;
  color: #4f46e5;
}

.icon-green {
  background-color: #ecfdf5;
  color: #059669;
}

.icon-orange {
  background-color: #fff7ed;
  color: #ea580c;
}

.stat-top-line {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.stat-num {
  font-size: 22px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.1;
}

.stat-unit {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

.stat-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.stat-sub {
  font-size: 11px;
  font-weight: 500;
  margin-top: 2px;
}

/* 3. Main layout 2 columns */
.profile-layout {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 26px;
  align-items: start;
}

.content-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.015);
}

.card-header-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.card-title-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background-color: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
}

.card-title {
  font-size: 16.5px;
  font-weight: 700;
  color: #0f172a;
}

.card-sub-label {
  font-size: 12px;
  color: #64748b;
}

.badge-exam-focus {
  background-color: #f1f5f9;
  color: #475569;
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
}

/* Goal highlight box */
.goal-highlight-box {
  background-color: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 14px;
  padding: 16px 20px;
  margin-top: 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.goal-left {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.goal-flag-icon {
  font-size: 18px;
  margin-top: 2px;
}

.goal-label {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.goal-desc {
  font-size: 12.5px;
  color: #475569;
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

/* Overall progress */
.overall-progress-wrap {
  margin-top: 16px;
}

.progress-labels-row {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  margin-bottom: 6px;
}

.progress-text-label {
  color: #475569;
  font-weight: 500;
}

.progress-val-label {
  color: #0060d2;
  font-weight: 700;
}

.custom-progress-bar {
  height: 8px;
  border-radius: 6px;
  background-color: #e2e8f0;
}

/* 3 Subjects progress grid */
.subjects-progress-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.subject-progress-card {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 12px 14px;
}

.subj-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12.5px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 8px;
}

.sub-progress {
  height: 6px;
  border-radius: 4px;
  background-color: #e2e8f0;
}

.subj-note {
  font-size: 11px;
  color: #64748b;
  margin-top: 6px;
}

.text-warning-dark {
  color: #d97706 !important;
}

/* Classes list */
.view-history-link {
  font-size: 12.5px;
  color: #0060d2;
  text-decoration: none;
  font-weight: 600;
}

.view-history-link:hover {
  text-decoration: underline;
}

.enrolled-classes-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.class-enroll-item {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px 18px;
  background-color: #ffffff;
  transition: all 0.15s ease;
}

.class-enroll-item:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.highlight-border-blue {
  border-color: #bfdbfe;
  background-color: #fcfdff;
}

.class-item-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.class-pills {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pill-badge {
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
}

.pill-blue {
  background-color: #eff6ff;
  color: #0060d2;
}

.pill-purple {
  background-color: #f3e8ff;
  color: #7e22ce;
}

.pill-gray {
  background-color: #f1f5f9;
  color: #475569;
}

.pill-green-outline {
  border: 1px solid #a7f3d0;
  background-color: #f0fdf4;
  color: #15803d;
}

.pill-gray-outline {
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
  color: #64748b;
}

.class-upcoming-badge {
  font-size: 11.5px;
  font-weight: 600;
  color: #d97706;
}

.class-item-main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
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
  flex-wrap: wrap;
}

.class-item-buttons {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-class-details {
  font-size: 12.5px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 8px;
}

.btn-enter-faceid {
  font-size: 12.5px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 8px;
  background-color: #0060d2;
  border-color: #0060d2;
  text-decoration: none;
}

.btn-enter-faceid:hover {
  background-color: #004fb0;
}

/* Week free slots grid */
.btn-update-slots {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 8px;
  text-decoration: none;
}

.week-free-slots-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 8px;
}

.day-slot-card {
  border-radius: 10px;
  padding: 10px 6px;
  text-align: center;
  font-size: 11.5px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 68px;
}

.day-label {
  font-weight: 700;
  color: #334155;
  margin-bottom: 4px;
}

.slot-time-text {
  font-size: 11px;
}

.slot-booked {
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #1e40af;
}

.slot-busy {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #94a3b8;
}

.slot-available {
  background-color: #f0fdf4;
  border: 1px dashed #4ade80;
  color: #166534;
}

.slots-legend {
  display: flex;
  align-items: center;
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

.dot-booked {
  background-color: #0060d2;
}

.dot-available {
  background-color: #10b981;
}

/* Teacher feedback */
.student-feedback-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 18px 20px;
}

.feedback-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
  flex-wrap: wrap;
  gap: 10px;
}

.student-reviewer-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.student-reviewer-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
}

.student-reviewer-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.student-reviewer-role {
  font-size: 12px;
  color: #64748b;
}

.feedback-stars {
  font-size: 12px;
  display: flex;
  align-items: center;
}

.stars-num {
  font-weight: 700;
  color: #0f172a;
}

.feedback-quote {
  font-size: 13px;
  color: #334155;
  font-style: italic;
  line-height: 1.6;
  margin: 0 0 10px;
}

.feedback-date {
  font-size: 11.5px;
}

/* Right column styles */
.faceid-status-box {
  background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
  border: 1px solid #bbf7d0;
  border-radius: 14px;
  padding: 20px 16px;
  text-align: center;
}

.faceid-icon-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #ecfdf5;
  border: 2px solid #86efac;
  color: #059669;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  margin: 0 auto 12px;
}

.faceid-status-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #065f46;
}

.faceid-match-rate {
  font-size: 12px;
  color: #047857;
  margin-top: 2px;
}

.faceid-desc {
  font-size: 11.5px;
  color: #475569;
  margin: 10px 0 14px;
  line-height: 1.5;
}

.btn-rescan-face {
  font-size: 12px;
  font-weight: 600;
  padding: 7px 12px;
  border-radius: 8px;
  text-decoration: none;
}

.device-specs-list {
  font-size: 12px;
}

.spec-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.spec-label {
  color: #64748b;
}

/* Tuition box */
.tuition-box {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px;
}

.tuition-top-line {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.tuition-term-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.tuition-package-sub {
  font-size: 11.5px;
  color: #64748b;
}

.badge-paid {
  background-color: #ecfdf5;
  color: #059669;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
  border: 1px solid #a7f3d0;
}

.tuition-price-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.tuition-amount {
  font-size: 22px;
  font-weight: 800;
  color: #0f172a;
}

.tuition-currency {
  font-size: 12px;
  color: #64748b;
}

.tuition-vietqr-status {
  font-size: 11.5px;
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
  padding: 8px 12px;
  border-radius: 8px;
}

/* Parent/Assistant info */
.parent-info-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px;
}

.parent-profile-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.parent-avatar-circle {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background-color: #eff6ff;
  color: #0060d2;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  border: 1px solid #bfdbfe;
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
  font-size: 12px;
}

.contact-line {
  display: flex;
  align-items: center;
}

.parent-note-box {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 11px;
  color: #475569;
  line-height: 1.5;
  display: flex;
  align-items: flex-start;
}

/* Responsive */
@media (max-width: 992px) {
  .profile-layout {
    grid-template-columns: 1fr;
  }
  .profile-stats-bar {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .profile-header-card {
    padding: 20px;
  }
  .profile-stats-bar {
    grid-template-columns: 1fr;
  }
  .subjects-progress-grid {
    grid-template-columns: 1fr;
  }
  .week-free-slots-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
