<template>
  <div class="edulink-page">
    <!-- 1. HEADER / NAVBAR -->
    <header class="edulink-header">
      <div class="header-container">
        <!-- Brand Logo -->
        <div class="header-left">
          <router-link to="/my-classes" class="brand-logo">EduLink</router-link>
        </div>

        <!-- Navigation Links -->
        <nav class="header-nav">
          <a href="javascript:void(0)" class="nav-item">Search for tutors</a>
          <router-link to="/my-schedule" class="nav-item">My Schedule</router-link>
          <router-link to="/my-classes" class="nav-item active">My Classes</router-link>
        </nav>

        <!-- Right User Actions -->
        <div class="header-right">
          <!-- Notification Bell -->
          <button class="icon-btn" title="Notifications">
            <i class="fa-regular fa-bell"></i>
          </button>

          <!-- User Avatar -->
          <div class="user-avatar">
            <img
              src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80"
              alt="Educator profile"
            />
          </div>

          <!-- Logout Link -->
          <router-link to="/dang-ky" class="logout-link">Logout</router-link>
        </div>
      </div>
    </header>

    <!-- 2. MAIN CONTENT -->
    <main class="main-content">
      <div class="content-container">
        <!-- Page Title Area -->
        <div class="page-title-area">
          <h1 class="page-title">Class Management</h1>
          <p class="page-subtitle">
            Manage your upcoming large lectures and private sessions.
          </p>
        </div>

        <!-- Two Columns Layout -->
        <div class="classes-layout">
          <!-- Cột trái: Upcoming Sessions -->
          <section class="sessions-column">
            <!-- Header của danh sách buổi học -->
            <div class="sessions-section-header">
              <h2 class="section-title">Upcoming Sessions</h2>
              <router-link to="/my-schedule" class="view-calendar-link">
                View Full Calendar <i class="fa-solid fa-arrow-right ms-1"></i>
              </router-link>
            </div>

            <!-- Danh sách các thẻ lớp học (Session Cards) -->
            <div class="session-cards-list">
              <!-- Card 1: Introduction to Advanced Calculus -->
              <div class="session-card accent-blue">
                <div class="session-date-box">
                  <span class="date-month">OCT</span>
                  <span class="date-day">24</span>
                  <span class="date-time">10:00 AM</span>
                </div>

                <div class="session-info">
                  <!-- Tag pills -->
                  <div class="session-tags">
                    <span class="tag-pill tag-grey">Large Class</span>
                    <span class="tag-pill tag-green">
                      <i class="fa-solid fa-users me-1"></i> Public
                    </span>
                  </div>

                  <!-- Tên lớp -->
                  <h3 class="session-name">Introduction to Advanced Calculus</h3>

                  <!-- Chi tiết địa điểm & thông báo -->
                  <div class="session-meta">
                    <div class="meta-item">
                      <i class="fa-solid fa-location-dot meta-icon"></i>
                      <span>Room 302, Science Building (Offline)</span>
                    </div>
                    <div class="meta-item">
                      <i class="fa-regular fa-envelope meta-icon"></i>
                      <span>Email notifications sent with addresses</span>
                    </div>
                  </div>
                </div>

                <!-- Nút thao tác Manage -->
                <div class="session-actions">
                  <button class="btn btn-manage" @click="handleManageClass('Introduction to Advanced Calculus')">
                    Manage
                  </button>
                </div>
              </div>

              <!-- Card 2: Creative Writing Workshop -->
              <div class="session-card accent-orange">
                <div class="session-date-box">
                  <span class="date-month">OCT</span>
                  <span class="date-day">24</span>
                  <span class="date-time">02:00 PM</span>
                </div>

                <div class="session-info">
                  <!-- Tag pills -->
                  <div class="session-tags">
                    <span class="tag-pill tag-grey">Private Class</span>
                    <span class="tag-pill tag-purple">
                      <i class="fa-solid fa-user-group me-1"></i> Group
                    </span>
                    <span class="tag-pill tag-neutral">
                      <i class="fa-solid fa-user me-1"></i> 3/5 students
                    </span>
                  </div>

                  <!-- Tên lớp -->
                  <h3 class="session-name">Creative Writing Workshop</h3>

                  <!-- Chi tiết phòng online & bảo mật FaceID -->
                  <div class="session-meta">
                    <div class="meta-item">
                      <i class="fa-solid fa-video meta-icon"></i>
                      <span>Online Room</span>
                    </div>
                    <div class="meta-item text-warning-dark">
                      <i class="fa-solid fa-lock meta-icon"></i>
                      <span class="fw-medium">FaceID Verification Required</span>
                    </div>
                  </div>
                </div>

                <!-- Nút thao tác Manage -->
                <div class="session-actions">
                  <button class="btn btn-manage" @click="handleManageClass('Creative Writing Workshop')">
                    Manage
                  </button>
                </div>
              </div>

              <!-- Card 3: SAT Math Tutoring - Sarah J. -->
              <div class="session-card accent-light-blue">
                <div class="session-date-box">
                  <span class="date-month">OCT</span>
                  <span class="date-day">25</span>
                  <span class="date-time">09:00 AM</span>
                </div>

                <div class="session-info">
                  <!-- Tag pills -->
                  <div class="session-tags">
                    <span class="tag-pill tag-grey">Private Class</span>
                    <span class="tag-pill tag-lavender">
                      <i class="fa-solid fa-user me-1"></i> 1:1
                    </span>
                  </div>

                  <!-- Tên lớp -->
                  <h3 class="session-name">SAT Math Tutoring - Sarah J.</h3>

                  <!-- Chi tiết phòng online & FaceID -->
                  <div class="session-meta">
                    <div class="meta-item">
                      <i class="fa-solid fa-video meta-icon"></i>
                      <span>Online Room</span>
                    </div>
                    <div class="meta-item text-warning-dark">
                      <i class="fa-solid fa-lock meta-icon"></i>
                      <span class="fw-medium">FaceID Verification Required</span>
                    </div>
                  </div>
                </div>

                <!-- Nút thao tác Manage -->
                <div class="session-actions">
                  <button class="btn btn-manage" @click="handleManageClass('SAT Math Tutoring - Sarah J.')">
                    Manage
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- Cột phải: Class Overview & Quick Actions -->
          <aside class="overview-column">
            <h2 class="section-title mb-3">Class Overview</h2>

            <!-- 2 thẻ thống kê (Stats Cards) -->
            <div class="stats-cards-row">
              <!-- Thẻ tổng học sinh -->
              <div class="stat-card">
                <div class="stat-icon-wrap text-primary">
                  <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-label">Total Students</div>
                <div class="stat-value">142</div>
              </div>

              <!-- Thẻ số buổi học trong tuần -->
              <div class="stat-card">
                <div class="stat-icon-wrap text-success">
                  <i class="fa-regular fa-circle-check"></i>
                </div>
                <div class="stat-label">Sessions This Week</div>
                <div class="stat-value">12</div>
              </div>
            </div>

            <!-- Card Quick Actions -->
            <div class="quick-actions-card">
              <h3 class="quick-actions-title">Quick Actions</h3>

              <div class="quick-buttons-list">
                <button class="btn quick-action-btn" @click="handleQuickAction('Schedule New Class')">
                  <i class="fa-solid fa-circle-plus action-icon text-primary"></i>
                  <span>Schedule New Class</span>
                </button>

                <button class="btn quick-action-btn" @click="handleQuickAction('Message Students')">
                  <i class="fa-regular fa-envelope action-icon text-secondary"></i>
                  <span>Message Students</span>
                </button>

                <button class="btn quick-action-btn" @click="handleQuickAction('Class Settings')">
                  <i class="fa-solid fa-gear action-icon text-secondary"></i>
                  <span>Class Settings</span>
                </button>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </main>

    <!-- 3. FOOTER -->
    <footer class="edulink-footer">
      <div class="footer-container">
        <!-- Bên trái: Logo & Bản quyền -->
        <div class="footer-left">
          <div class="footer-brand">EduLink</div>
          <p class="footer-copyright">
            © 2024 EduLink. Bridging the gap between educators and students.
          </p>
        </div>

        <!-- Bên phải: Các liên kết phụ -->
        <div class="footer-links">
          <a href="javascript:void(0)" class="footer-link">Contact info</a>
          <a href="javascript:void(0)" class="footer-link">FAQ</a>
          <a href="javascript:void(0)" class="footer-link">Terms</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: "EduLinkClassManagement",
  methods: {
    handleManageClass(className) {
      alert(`Đang mở trang quản lý chi tiết lớp học: "${className}"`);
    },
    handleQuickAction(actionName) {
      alert(`Thực hiện hành động: "${actionName}"`);
    }
  }
};
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
