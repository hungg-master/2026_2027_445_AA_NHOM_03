<template><AccountClasses /></template>
<script>
import AccountClasses from './AccountClasses.vue';
export default { name: 'ClassManagement', components: { AccountClasses } };
</script>
<style scoped>
/* Reset & Căn chỉnh chung */
.edulink-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

/* ================= 1. HEADER / NAVBAR ================= */
.edulink-header {
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-container {
  max-width: 1260px;
  margin: 0 auto;
  padding: 0 28px;
  height: 68px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand-logo {
  font-size: 23px;
  font-weight: 800;
  color: #0060d2;
  text-decoration: none;
  letter-spacing: -0.5px;
}

.header-nav {
  display: flex;
  align-items: center;
  gap: 36px;
  height: 100%;
}

.header-nav .nav-item {
  text-decoration: none;
  font-size: 14.5px;
  font-weight: 500;
  color: #475569;
  height: 68px;
  display: inline-flex;
  align-items: center;
  border-bottom: 2.5px solid transparent;
  transition: all 0.2s ease;
}

.header-nav .nav-item:hover {
  color: #0060d2;
}

.header-nav .nav-item.active {
  color: #0060d2;
  font-weight: 600;
  border-bottom-color: #0060d2;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.icon-btn {
  background: transparent;
  border: none;
  font-size: 18px;
  color: #475569;
  cursor: pointer;
  padding: 6px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.icon-btn:hover {
  color: #0060d2;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #e2e8f0;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.logout-link {
  font-size: 14px;
  font-weight: 500;
  color: #475569;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.2s;
}

.logout-link:hover {
  color: #ef4444;
}

/* ================= 2. MAIN CONTENT ================= */
.main-content {
  flex: 1;
  padding: 36px 24px 60px;
}

.content-container {
  max-width: 1260px;
  margin: 0 auto;
}

.page-title-area {
  margin-bottom: 30px;
}

.page-title {
  font-size: 29px;
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

/* 2 Cột Layout */
.classes-layout {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}

/* Section Titles */
.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0;
}

/* --- CỘT TRÁI: UPCOMING SESSIONS --- */
.sessions-column {
  flex: 1 1 68%;
}

.sessions-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.view-calendar-link {
  font-size: 13.5px;
  font-weight: 600;
  color: #0060d2;
  text-decoration: none;
  transition: transform 0.2s;
}

.view-calendar-link:hover {
  text-decoration: underline;
}

.session-cards-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Thẻ Session Card */
.session-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
  padding: 22px 26px;
  display: flex;
  align-items: center;
  gap: 22px;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.session-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

/* Border điểm nhấn bên trái */
.session-card.accent-blue {
  border-left: 5px solid #0060d2;
}

.session-card.accent-orange {
  border-left: 5px solid #ea580c;
}

.session-card.accent-light-blue {
  border-left: 5px solid #60a5fa;
}

/* Hộp ngày tháng bên trái thẻ */
.session-date-box {
  background-color: #f1f5f9;
  border-radius: 10px;
  padding: 12px 10px;
  width: 78px;
  text-align: center;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.date-month {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.5px;
}

.date-day {
  font-size: 24px;
  font-weight: 800;
  color: #0060d2;
  line-height: 1.1;
  margin: 2px 0;
}

.date-time {
  font-size: 11px;
  color: #64748b;
  font-weight: 500;
  white-space: nowrap;
}

/* Thông tin chi tiết buổi học */
.session-info {
  flex: 1;
}

.session-tags {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.tag-pill {
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
}

.tag-grey {
  background-color: #f1f5f9;
  color: #475569;
}

.tag-green {
  background-color: #dcfce7;
  color: #16a34a;
}

.tag-purple {
  background-color: #f3e8ff;
  color: #9333ea;
}

.tag-lavender {
  background-color: #ede9fe;
  color: #7c3aed;
}

.tag-neutral {
  color: #64748b;
  font-size: 11.5px;
  font-weight: 500;
  margin-left: 2px;
}

.session-name {
  font-size: 17.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 8px;
  line-height: 1.3;
}

.session-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.meta-item {
  font-size: 13px;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 8px;
}

.meta-icon {
  font-size: 12px;
  width: 14px;
  text-align: center;
}

.text-warning-dark {
  color: #b45309;
}

/* Nút Manage */
.btn-manage {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  padding: 8px 24px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-manage:hover {
  background-color: #004fb0;
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
}

/* --- CỘT PHẢI: CLASS OVERVIEW & QUICK ACTIONS --- */
.overview-column {
  flex: 0 0 32%;
}

/* Stats Cards */
.stats-cards-row {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  flex: 1;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
  padding: 20px;
}

.stat-icon-wrap {
  font-size: 18px;
  margin-bottom: 12px;
}

.stat-label {
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 4px;
  font-weight: 500;
}

.stat-value {
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

/* Quick Actions Card */
.quick-actions-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
  padding: 24px;
}

.quick-actions-title {
  font-size: 17px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 18px;
}

.quick-buttons-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.quick-action-btn {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  height: 48px;
  padding: 0 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 13.5px;
  font-weight: 600;
  color: #1e293b;
  transition: all 0.2s ease;
  width: 100%;
  text-align: left;
}

.quick-action-btn:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
  transform: translateY(-1px);
}

.action-icon {
  font-size: 16px;
}

/* ================= 3. FOOTER ================= */
.edulink-footer {
  background-color: #ffffff;
  border-top: 1px solid #e2e8f0;
  padding: 40px 24px;
  margin-top: auto;
}

.footer-container {
  max-width: 1260px;
  margin: 0 auto;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.footer-brand {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 6px;
}

.footer-copyright {
  font-size: 13px;
  color: #64748b;
  margin-bottom: 0;
  max-width: 300px;
  line-height: 1.5;
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.footer-link {
  font-size: 13.5px;
  color: #475569;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-link:hover {
  color: #0060d2;
}

/* ================= RESPONSIVE ================= */
@media (max-width: 991px) {
  .classes-layout {
    flex-direction: column;
  }
  .sessions-column,
  .overview-column {
    flex: 1 1 100%;
    width: 100%;
  }
  .header-nav {
    display: none;
  }
}
</style>
