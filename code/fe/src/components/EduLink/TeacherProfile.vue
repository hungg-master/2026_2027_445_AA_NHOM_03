<template><TeacherDirectory /></template>
<script>
import TeacherDirectory from './TeacherDirectory.vue';
export default { name: 'TeacherProfile', components: { TeacherDirectory } };
</script>
<style scoped>
.teacher-profile-page {
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

/* 1. Breadcrumb */
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

/* 2. Top Profile Card */
.profile-header-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 28px 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
  margin-bottom: 28px;
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

.teacher-avatar-box {
  position: relative;
  width: 96px;
  height: 96px;
  flex-shrink: 0;
}

.teacher-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 16px;
  object-fit: cover;
}

.verified-badge-icon {
  position: absolute;
  bottom: -4px;
  right: -4px;
  background-color: #ffffff;
  color: #0060d2;
  font-size: 18px;
  border-radius: 50%;
  line-height: 1;
}

.teacher-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}

.teacher-name {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0;
  letter-spacing: -0.4px;
}

.badge-verified {
  background-color: #eff6ff;
  border: 1px solid #dbeafe;
  color: #0060d2;
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 50px;
}

.teacher-workplace {
  font-size: 13.5px;
  color: #475569;
  margin-bottom: 8px;
}

.teacher-trust-badges {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
}

.trust-sep {
  color: #cbd5e1;
}

.profile-top-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-book-now {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  font-weight: 700;
  font-size: 14px;
  height: 42px;
  padding: 0 20px;
  border-radius: 8px;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.2);
}

.btn-book-now:hover {
  background-color: #004fb0;
  transform: translateY(-1px);
}

.btn-chat-now {
  height: 42px;
  padding: 0 16px;
  font-size: 13.5px;
  font-weight: 600;
  border-radius: 8px;
}

.btn-icon-like {
  width: 42px;
  height: 42px;
  border: 1px solid #e2e8f0;
  background-color: #ffffff;
  color: #64748b;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  transition: all 0.2s;
}

.btn-icon-like:hover {
  background-color: #fef2f2;
  color: #ef4444;
  border-color: #fecaca;
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
  gap: 12px;
}

.stat-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  flex-shrink: 0;
}

.icon-blue { background-color: #eff6ff; color: #0060d2; }
.icon-green { background-color: #f0fdf4; color: #16a34a; }
.icon-orange { background-color: #fff7ed; color: #ea580c; }
.icon-purple { background-color: #faf5ff; color: #9333ea; }

.stat-highlight-num {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
}

.stat-highlight-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

/* 3. Bố cục 2 cột chính */
.profile-layout {
  display: flex;
  gap: 28px;
  align-items: flex-start;
}

.profile-main-column {
  flex: 1 1 64%;
}

.profile-sidebar-column {
  flex: 1 1 36%;
}

/* Section Card chung */
.section-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px 28px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.section-heading {
  font-size: 17.5px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  letter-spacing: -0.3px;
}

.heading-accent {
  width: 4px;
  height: 18px;
  background-color: #0060d2;
  border-radius: 4px;
  display: inline-block;
}

.bio-paragraphs {
  font-size: 14px;
  color: #334155;
  line-height: 1.65;
}

.bio-paragraphs p {
  margin-bottom: 12px;
}

.teaching-features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-top: 20px;
}

.feature-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 16px;
}

.feature-icon {
  font-size: 20px;
  margin-bottom: 10px;
}

.feature-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.feature-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.45;
  margin-bottom: 0;
}

/* Credentials Grid */
.credentials-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.credential-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  padding: 16px;
}

.cred-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.cred-blue { background-color: #eff6ff; color: #0060d2; }
.cred-green { background-color: #f0fdf4; color: #16a34a; }
.cred-orange { background-color: #fff7ed; color: #ea580c; }
.cred-gray { background-color: #f1f5f9; color: #475569; }

.cred-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 2px;
}

.cred-school {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 6px;
}

.cred-status {
  font-size: 11.5px;
  font-weight: 600;
}

.text-warning-dark {
  color: #b45309;
}

/* Class Packages */
.class-types-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.class-package-card {
  position: relative;
  background-color: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 20px 16px;
  display: flex;
  flex-direction: column;
  cursor: pointer;
  transition: all 0.2s ease;
}

.class-package-card:hover {
  border-color: #0060d2;
  box-shadow: 0 6px 18px rgba(0, 96, 210, 0.08);
}

.class-package-card.selected-pkg {
  border-color: #0060d2;
  background-color: #fbfdff;
}

.popular-package {
  border-color: #16a34a;
}

.popular-ribbon {
  position: absolute;
  top: -10px;
  right: 12px;
  background-color: #16a34a;
  color: #ffffff;
  font-size: 10px;
  font-weight: 800;
  padding: 2px 10px;
  border-radius: 50px;
  letter-spacing: 0.5px;
}

.pkg-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 50px;
  display: inline-block;
  margin-bottom: 10px;
  max-width: fit-content;
}

.badge-blue { background-color: #eff6ff; color: #0060d2; }
.badge-green { background-color: #f0fdf4; color: #16a34a; }
.badge-gray { background-color: #f1f5f9; color: #475569; }

.pkg-title {
  font-size: 14.5px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 6px;
}

.pkg-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.45;
  flex-grow: 1;
  margin-bottom: 16px;
}

.pkg-price-wrap {
  margin-bottom: 14px;
}

.pkg-price {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
}

.pkg-unit {
  font-size: 12px;
  color: #64748b;
  margin-left: 4px;
}

/* Schedule Matrix */
.schedule-legend {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 12px;
  color: #64748b;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.dot-blue {
  width: 8px;
  height: 8px;
  background-color: #0060d2;
  border-radius: 50%;
}

.dot-gray {
  width: 8px;
  height: 8px;
  background-color: #cbd5e1;
  border-radius: 50%;
}

.schedule-matrix-wrapper {
  overflow-x: auto;
}

.schedule-matrix-table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
}

.schedule-matrix-table th,
.schedule-matrix-table td {
  border: 1px solid #f1f5f9;
  padding: 10px 6px;
  vertical-align: middle;
}

.schedule-matrix-table th {
  background-color: #f8fafc;
  font-size: 12.5px;
  font-weight: 700;
  color: #475569;
}

.matrix-header-col {
  width: 140px;
  text-align: left;
  padding-left: 12px !important;
}

.shift-name {
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  padding-left: 12px !important;
  background-color: #f8fafc;
}

.slot-btn {
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #0060d2;
  font-size: 12px;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
  max-width: 68px;
}

.slot-btn:hover {
  background-color: #0060d2;
  color: #ffffff;
  border-color: #0060d2;
}

.slot-btn.selected {
  background-color: #0060d2;
  color: #ffffff;
  border-color: #0060d2;
  box-shadow: 0 2px 8px rgba(0, 96, 210, 0.3);
}

.slot-closed {
  font-size: 11.5px;
  color: #94a3b8;
  background-color: #f8fafc;
  padding: 6px 12px;
  border-radius: 6px;
  display: inline-block;
}

.schedule-hint-note {
  display: flex;
  align-items: center;
  font-size: 12.5px;
  color: #64748b;
  margin-top: 14px;
}

/* Reviews */
.overall-rating-badge {
  font-size: 14px;
  color: #0f172a;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.review-box {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 18px 20px;
}

.review-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.reviewer-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.reviewer-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 13.5px;
}

.avatar-blue { background-color: #eff6ff; color: #0060d2; }
.avatar-green { background-color: #f0fdf4; color: #16a34a; }

.reviewer-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.reviewer-course {
  font-size: 11.5px;
  color: #64748b;
}

.review-date {
  font-size: 12px;
  color: #94a3b8;
}

.review-stars {
  font-size: 11px;
  margin-bottom: 8px;
}

.review-text {
  font-size: 13px;
  color: #334155;
  line-height: 1.55;
  margin-bottom: 0;
}

/* Sidebar Booking */
.sticky-sidebar-wrapper {
  position: sticky;
  top: 24px;
}

.booking-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
}

.booking-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.booking-card-title {
  font-size: 16.5px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0;
}

.badge-accepting {
  background-color: #dcfce7;
  color: #16a34a;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 50px;
}

.booking-label {
  font-size: 12.5px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.custom-select,
.booking-input {
  height: 42px;
  font-size: 13.5px;
  border-radius: 8px;
  border: 1px solid #d8dee4;
}

.radio-options-row {
  display: flex;
  gap: 16px;
}

.custom-radio-label {
  font-size: 13px;
  color: #334155;
  display: flex;
  align-items: center;
  cursor: pointer;
}

.selected-slot-box {
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
  color: #0060d2;
  font-weight: 700;
  font-size: 13.5px;
}

.fee-calculation-box {
  background-color: #f8fafc;
  border-radius: 10px;
  padding: 14px 16px;
  margin: 18px 0;
}

.fee-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 6px;
}

.fee-divider {
  margin: 10px 0;
  border-color: #e2e8f0;
}

.fee-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total-label {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.total-price {
  font-size: 20px;
  font-weight: 800;
  color: #0060d2;
}

.btn-confirm-booking {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  height: 46px;
  border-radius: 10px;
  font-size: 14.5px;
  font-weight: 700;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
}

.btn-confirm-booking:hover {
  background-color: #004fb0;
  transform: translateY(-1px);
}

/* Commitment card */
.commitment-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px 22px;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
}

.commitment-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 12px;
}

.commitment-list {
  list-style: none;
  padding-left: 0;
  margin-bottom: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.commitment-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 12.5px;
  color: #334155;
  line-height: 1.45;
}

.list-icon {
  margin-top: 3px;
  font-size: 13px;
}

/* Special Advice card */
.special-advice-card {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px;
}

.advice-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 6px;
}

.advice-desc {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.45;
  margin-bottom: 14px;
}

.contact-pill {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 12px;
  color: #334155;
  display: flex;
  align-items: center;
}

/* Responsive */
@media (max-width: 991px) {
  .profile-layout {
    flex-direction: column;
  }
  .profile-main-column,
  .profile-sidebar-column {
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
  .teaching-features-grid,
  .class-types-grid {
    grid-template-columns: 1fr;
  }
  .credentials-grid {
    grid-template-columns: 1fr;
  }
}
</style>
