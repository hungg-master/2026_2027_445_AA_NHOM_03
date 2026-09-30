<template>
  <div class="edulink-page">
    <!-- 1. HEADER / NAVBAR -->
    <header class="edulink-header">
      <div class="header-container">
        <!-- Logo -->
        <div class="header-left">
          <router-link to="/my-schedule" class="brand-logo">EduLink</router-link>
        </div>

        <!-- Navigation Links -->
        <nav class="header-nav">
          <a href="javascript:void(0)" class="nav-item">Search for tutors</a>
          <router-link to="/my-schedule" class="nav-item active">My Schedule</router-link>
          <router-link to="/my-classes" class="nav-item">My Classes</router-link>
        </nav>

        <!-- Right User Actions -->
        <div class="header-right">
          <!-- Notification Bell -->
          <button class="icon-btn" title="Notifications">
            <i class="fa-regular fa-bell"></i>
          </button>

          <!-- Logout Link -->
          <router-link to="/logout" class="logout-link">Logout</router-link>

          <!-- User Avatar -->
          <div class="user-avatar">
            <img
              src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80"
              alt="User profile"
            />
          </div>
        </div>
      </div>
    </header>

    <!-- 2. MAIN CONTENT -->
    <main class="main-content">
      <div class="content-container">
        <!-- Title & Subtitle -->
        <div class="page-title-area">
          <h1 class="page-title">Đăng ký Lịch rảnh Học thử</h1>
          <p class="page-subtitle">
            Chọn lịch rảnh và gửi yêu cầu học thử. Lịch học sẽ được xác nhận sau khi tiếp nhận.
          </p>
        </div>

        <!-- Two Columns Layout -->
        <div class="schedule-layout">
          <!-- Cột trái: Bảng lịch biểu (Schedule Grid) -->
          <section class="schedule-column">
            <div class="schedule-card">
              <!-- Header của Card: Tuần & Chú thích (Legend) -->
              <div class="schedule-card-header">
                <div class="week-nav">Lịch rảnh hằng tuần</div>

                <!-- Chú thích trạng thái -->
                <div class="schedule-legend">
                  <div class="legend-item">
                    <span class="legend-dot available"></span>
                    <span class="legend-text">Available</span>
                  </div>
                  <div class="legend-item">
                    <span class="legend-dot selected"></span>
                    <span class="legend-text">Selected</span>
                  </div>
                </div>
              </div>

              <!-- Bảng Grid thời gian -->
              <div class="calendar-table-wrapper">
                <table class="calendar-table">
                  <thead>
                    <tr>
                      <th class="time-col-header">Time</th>
                      <th
                        v-for="(day, index) in days"
                        :key="day"
                        :class="['day-col-header', { 'active-day': day === 'Thu' }]"
                      >
                        <div class="day-name">{{ dayLabels[index] }}</div>
                        
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="time in timeSlots" :key="time">
                      <!-- Cột hiển thị giờ -->
                      <td class="time-cell">{{ time }}</td>

                      <!-- Các ô chọn giờ theo từng ngày trong tuần -->
                      <td
                        v-for="(day, index) in days"
                        :key="day + '_' + time"
                        :class="['slot-cell', { 'is-selected': isSelected(day, time) }]"
                        @click="toggleSlot(day, time)"
                      >
                        <span v-if="isSelected(day, time)" class="selected-badge">
                          Selected
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

          <aside class="tutors-column">
            <form class="schedule-card p-4" @submit.prevent="submitTrialBooking">
              <h2 class="h5">Thông tin học thử</h2>
              <label class="d-block mt-3">Họ tên
                <input v-model.trim="form.name" class="form-control" required maxlength="100" />
              </label>
              <label class="d-block mt-3">Số điện thoại
                <input v-model.trim="form.phone" class="form-control" required pattern="[0-9]{10}" inputmode="tel" />
              </label>
              <label class="d-block mt-3">Môn học
                <input v-model.trim="form.subject" class="form-control" required maxlength="150" />
              </label>
              <p class="mt-3">Đã chọn {{ selectedSlots.length }} khung giờ.</p>
              <p v-if="message" role="status">{{ message }}</p>
              <button class="btn btn-primary w-100" :disabled="loading || !selectedSlots.length">{{ loading ? 'Đang gửi…' : 'Gửi yêu cầu học thử' }}</button>
            </form>
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
import { API_BASE } from '../../services/api';
export default {
  name: "TrialBooking",
  data() {
    return {
      days: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
      dayLabels: ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "CN"],
      timeSlots: [
        "7:00 AM", "8:00 AM", "9:00 AM", "10:00 AM", "11:00 AM",
        "1:00 PM", "2:00 PM", "3:00 PM", "4:00 PM", "5:00 PM",
        "6:00 PM", "7:00 PM", "8:00 PM", "9:00 PM"
      ],
      selectedSlots: [],
      loading: false,
      message: "",
      isDragging: false,
      dragMode: "select",
      form: {
        name: "",
        phone: "",
        subject: ""
      }
    };
  },
  mounted() {
    // Get subject from URL query if any
    if (this.$route.query.subject) {
      this.form.subject = this.$route.query.subject;
    } else {
      this.form.subject = "Học thử Tổng quát";
    }
  },
  methods: {
    getSlotId(day, time) {
      return `${day}_${time}`;
    },
    isSelected(day, time) {
      return this.selectedSlots.includes(this.getSlotId(day, time));
    },
    toggleSlot(day, time) {
      const slotId = this.getSlotId(day, time);
      const index = this.selectedSlots.indexOf(slotId);
      if (index === -1) {
        this.selectedSlots.push(slotId);
      } else {
        this.selectedSlots.splice(index, 1);
      }
    },
    startDrag(day, time) {
      this.isDragging = true;
      const slotId = this.getSlotId(day, time);
      if (this.selectedSlots.includes(slotId)) {
        this.dragMode = "deselect";
        this.selectedSlots = this.selectedSlots.filter((id) => id !== slotId);
      } else {
        this.dragMode = "select";
        this.selectedSlots.push(slotId);
      }
    },
    onDrag(day, time) {
      if (!this.isDragging) return;
      const slotId = this.getSlotId(day, time);
      if (this.dragMode === "select" && !this.selectedSlots.includes(slotId)) {
        this.selectedSlots.push(slotId);
      } else if (this.dragMode === "deselect" && this.selectedSlots.includes(slotId)) {
        this.selectedSlots = this.selectedSlots.filter((id) => id !== slotId);
      }
    },
    stopDrag() {
      this.isDragging = false;
    },
    async submitTrialBooking() {
      if (this.loading || !this.selectedSlots.length) return;
      this.loading = true;
      this.message = '';
      try {
        const response = await fetch(API_BASE + '/hoc-thu', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ ...this.form, schedules: this.selectedSlots })
        });
        const data = await response.json();
        if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || data.message);
        this.message = data.message;
        this.selectedSlots = [];
      } catch (error) {
        this.message = error.message || 'Không thể gửi yêu cầu. Vui lòng thử lại.';
      } finally {
        this.loading = false;
      }
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

/* Logo */
.brand-logo {
  font-size: 23px;
  font-weight: 800;
  color: #0060d2;
  text-decoration: none;
  letter-spacing: -0.5px;
}

/* Menu điều hướng giữa */
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

/* Actions bên phải */
.header-right {
  display: flex;
  align-items: center;
  gap: 20px;
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

.user-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #e2e8f0;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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
  margin-bottom: 28px;
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

/* Layout 2 cột */
.schedule-layout {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}

/* --- CỘT TRÁI: BẢNG LỊCH BIỂU --- */
.schedule-column {
  flex: 1 1 68%;
}

.schedule-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  padding: 24px 28px 30px;
}

/* Header của Schedule Card */
.schedule-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.week-nav {
  display: flex;
  align-items: center;
  gap: 16px;
}

.nav-arrow-btn {
  background: transparent;
  border: none;
  color: #475569;
  font-size: 14px;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.nav-arrow-btn:hover {
  background: #f1f5f9;
  color: #0060d2;
}

.week-range-text {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
}

.schedule-legend {
  display: flex;
  align-items: center;
  gap: 18px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
  color: #475569;
}

.legend-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
}

.legend-dot.available {
  background-color: #e2e8f0;
}

.legend-dot.selected {
  background-color: #0060d2;
}

/* Bảng thời gian (Calendar Table) */
.calendar-table-wrapper {
  overflow-x: auto;
}

.calendar-table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
}

.calendar-table th,
.calendar-table td {
  border: 1px solid #e2e8f0;
}

/* Header Columns */
.time-col-header {
  width: 100px;
  padding: 14px 10px;
  font-size: 13.5px;
  font-weight: 600;
  color: #475569;
  background-color: #f8fafc;
}

.day-col-header {
  padding: 12px 10px;
  background-color: #f8fafc;
  font-size: 13px;
  color: #475569;
}

.day-col-header.active-day {
  color: #0060d2;
  font-weight: 700;
}

.day-name {
  font-weight: 600;
  line-height: 1.2;
}

.day-num {
  font-size: 14px;
  line-height: 1.2;
}

/* Time rows & cells */
.time-cell {
  background-color: #ffffff;
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
  padding: 18px 10px;
  vertical-align: middle;
}

.slot-cell {
  height: 60px;
  background-color: #ffffff;
  cursor: pointer;
  vertical-align: middle;
  transition: background-color 0.15s ease;
  position: relative;
  padding: 6px;
}

.slot-cell:hover:not(.is-selected) {
  background-color: #f1f5f9;
}

/* Ô đã được chọn: Màu xanh đậm đặc trưng */
.slot-cell.is-selected {
  background-color: #0060d2;
}

.selected-badge {
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  user-select: none;
}

/* --- CỘT PHẢI: GỢI Ý GIA SƯ (SUGGESTED TUTORS) --- */
.tutors-column {
  flex: 0 0 32%;
}

.tutors-header {
  margin-bottom: 18px;
}

.tutors-title {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 2px;
}

.tutors-subtitle {
  font-size: 13px;
  color: #64748b;
  margin-bottom: 0;
}

.matches-badge {
  background-color: #dcfce7;
  color: #15803d;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 50px;
}

/* Tutor Card */
.tutors-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.tutor-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
  padding: 20px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.tutor-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.tutor-info-row {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 14px;
}

.tutor-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.tutor-details {
  flex: 1;
}

.tutor-name {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 2px;
}

.tutor-subject {
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 4px;
}

.tutor-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
}

.star-icon {
  color: #f59e0b;
  font-size: 11px;
}

.rating-num {
  font-weight: 700;
  color: #0f172a;
}

.sessions-count {
  color: #64748b;
}

/* Matched slot pills */
.tutor-matched-slots {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
}

.matched-slot-pill {
  background-color: #f1f5f9;
  color: #334155;
  font-size: 12px;
  font-weight: 500;
  padding: 4px 12px;
  border-radius: 50px;
}

/* Nút Select Tutor */
.btn-select-tutor {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  height: 40px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-select-tutor:hover {
  background-color: #004fb0;
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 96, 210, 0.25);
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
  .schedule-layout {
    flex-direction: column;
  }
  .schedule-column,
  .tutors-column {
    flex: 1 1 100%;
    width: 100%;
  }
  .header-nav {
    display: none;
  }
}
</style>


