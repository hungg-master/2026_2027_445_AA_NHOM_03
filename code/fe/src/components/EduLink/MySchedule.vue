<template>
  <div class="edulink-page">
    <main class="main-content">
      <div class="content-container">
        <!-- 1. Tiêu đề trang & Nút lưu lịch trên đầu -->
        <div class="page-title-area">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <h1 class="page-title">Chọn Lịch Rảnh Của Bạn</h1>
              <p class="page-subtitle">
                Nhấp vào các ô bên dưới để chọn khung giờ rảnh của bạn. Chúng tôi sẽ gợi ý giảng viên phù hợp nhất.
              </p>
            </div>

            <!-- Nút Lưu Lịch Rảnh ở đầu trang -->
            <div class="top-action-bar d-flex align-items-center gap-2">
              <button
                class="btn btn-primary btn-save-top px-4 py-2 fw-semibold shadow-sm d-flex align-items-center"
                @click="saveSchedule"
                :disabled="saving"
              >
                <i v-if="saving" class="fa-solid fa-spinner fa-spin me-2"></i>
                <i v-else class="fa-solid fa-floppy-disk me-2"></i>
                <span>{{ saving ? 'Đang lưu...' : 'Lưu lịch rảnh' }}</span>
              </button>
            </div>
          </div>
        </div>

        <p v-if="scheduleError" class="alert alert-danger" role="alert">{{ scheduleError }} <button class="btn btn-outline-danger btn-sm" @click="loadSavedSchedule">Tải lại</button></p>
        <!-- Thông báo Toast -->
        <transition name="toast-fade">
          <div v-if="toast.show" :class="['schedule-toast', 'toast-' + toast.type]">
            <i :class="toast.icon" class="me-2 fs-5"></i>
            <span class="fw-medium">{{ toast.message }}</span>
          </div>
        </transition>

        <!-- 2. Bố cục 2 cột (Lịch biểu & Giảng viên gợi ý) -->
        <div class="schedule-layout">
          <!-- Cột trái: Bảng lịch biểu (Schedule Grid) -->
          <section class="schedule-column">
            <div class="schedule-card">
              <!-- Header của Card: Tuần, Bộ lọc ca & Chú thích -->
              <div class="schedule-card-header">
                <!-- Điều hướng tuần -->
                <div class="week-nav">
                  <button class="nav-arrow-btn" @click="changeWeek(-1)" title="Tuần trước">
                    <i class="fa-solid fa-chevron-left"></i>
                  </button>
                  <span class="week-range-text">{{ currentWeekText }}</span>
                  <button class="nav-arrow-btn" @click="changeWeek(1)" title="Tuần sau">
                    <i class="fa-solid fa-chevron-right"></i>
                  </button>
                  <button
                    v-if="currentWeekOffset !== 0"
                    class="btn-today ms-2"
                    @click="resetToCurrentWeek"
                    title="Trở về tuần hiện tại"
                  >
                    Hôm nay
                  </button>
                </div>

                <!-- Lọc theo ca học -->
                <div class="time-filter-tabs">
                  <button
                    :class="['filter-tab-btn', { active: activeShiftFilter === 'all' }]"
                    @click="activeShiftFilter = 'all'"
                  >
                    Tất cả ca
                  </button>
                  <button
                    :class="['filter-tab-btn', { active: activeShiftFilter === 'morning' }]"
                    @click="activeShiftFilter = 'morning'"
                  >
                    Sáng
                  </button>
                  <button
                    :class="['filter-tab-btn', { active: activeShiftFilter === 'afternoon' }]"
                    @click="activeShiftFilter = 'afternoon'"
                  >
                    Chiều
                  </button>
                  <button
                    :class="['filter-tab-btn', { active: activeShiftFilter === 'evening' }]"
                    @click="activeShiftFilter = 'evening'"
                  >
                    Tối
                  </button>
                </div>

                <!-- Chú thích trạng thái -->
                <div class="schedule-legend">
                  <div class="legend-item">
                    <span class="legend-dot available"></span>
                    <span class="legend-text">Trống</span>
                  </div>
                  <div class="legend-item">
                    <span class="legend-tick"><i class="fa-solid fa-check"></i></span>
                    <span class="legend-text">Đã chọn</span>
                  </div>
                </div>
              </div>

              <!-- Bảng Grid thời gian -->
              <div class="calendar-table-wrapper">
                <table class="calendar-table">
                  <thead>
                    <tr>
                      <th class="time-col-header">Thời gian</th>
                      <th
                        v-for="day in weekDays"
                        :key="day.key"
                        :class="['day-col-header', { 'active-day': day.isToday }]"
                      >
                        <div class="day-name">{{ day.name }}</div>
                        <div class="day-num">{{ day.date }}</div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="slot in visibleTimeSlots" :key="slot.key">
                      <!-- Cột hiển thị giờ tiếng Việt -->
                      <td class="time-cell">
                        <div class="time-main">{{ slot.label }}</div>
                        <div class="time-sub">{{ slot.period }}</div>
                      </td>

                      <!-- Các ô chọn giờ theo từng ngày trong tuần -->
                      <td
                        v-for="day in weekDays"
                        :key="day.key + '_' + slot.key"
                        :class="['slot-cell', { 'is-selected': isSlotSelected(day.key, slot.key) }]"
                        @click="toggleSlot(day.key, slot.key)"
                        :title="getCellTooltip(day, slot)"
                      >
                        <!-- DẤU TÍCH KHI ĐƯỢC CHỌN (Không dùng chữ Selected) -->
                        <div v-if="isSlotSelected(day.key, slot.key)" class="selected-tick-wrapper">
                          <i class="fa-solid fa-check check-icon"></i>
                        </div>
                        <div v-else class="cell-hover-hint">
                          <i class="fa-solid fa-plus hint-icon"></i>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Thanh hành động bên dưới: Tổng số khung giờ & Nút Lưu lại -->
              <div class="schedule-bottom-bar">
                <div class="selection-summary">
                  <i class="fa-regular fa-clock text-primary me-2 fs-5"></i>
                  <span>Đã chọn: <strong class="text-primary fs-6">{{ selectedSlots.length }}</strong> khung giờ</span>
                  <span v-if="hasUnsavedChanges" class="badge-status unsaved ms-2">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> Chưa lưu thay đổi
                  </span>
                  <span v-else-if="lastSavedTime" class="badge-status saved ms-2">
                    <i class="fa-solid fa-circle-check me-1"></i> Đã đồng bộ CSDL
                  </span>
                </div>

                <div class="bottom-action-buttons">
                  <button
                    class="btn btn-outline-secondary btn-sm px-3"
                    @click="clearAllSlots"
                    :disabled="selectedSlots.length === 0 || saving"
                  >
                    <i class="fa-solid fa-rotate-left me-1"></i> Xóa tất cả
                  </button>
                  <button
                    class="btn btn-primary btn-save-main px-4"
                    @click="saveSchedule"
                    :disabled="saving"
                  >
                    <i v-if="saving" class="fa-solid fa-spinner fa-spin me-2"></i>
                    <i v-else class="fa-solid fa-floppy-disk me-2"></i>
                    <span>{{ saving ? 'Đang lưu...' : 'Lưu lịch rảnh' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- Cột phải: Danh sách gia sư / giảng viên gợi ý -->
          <aside v-if="roleIsStudent" class="tutors-column">
            <!-- Header gợi ý & badge đếm số match -->
            <div class="tutors-header">
              <div class="d-flex align-items-center justify-content-between">
                <h3 class="tutors-title">Giảng viên gợi ý</h3>
                <span class="matches-badge">{{ filteredTutors.length }} Phù hợp</span>
              </div>
              <p class="tutors-subtitle">Phù hợp với khung giờ bạn đã chọn</p>
            </div>

            <form @submit.prevent="findTutors" class="card p-3 mb-3 d-grid gap-2">
              <label>Môn học<select v-model="matchSubject" class="form-select" required><option value="">Chọn môn</option><option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.ten_mon_hoc }}</option></select></label>
              <label>Ngày học thử<input v-model="matchDate" type="date" class="form-control" required /></label>
              <label>Thời lượng (phút)<input v-model.number="matchDuration" type="number" min="15" max="180" step="15" class="form-control" required /></label>
              <button class="btn btn-primary" :disabled="matching || hasUnsavedChanges">{{ matching ? 'Đang tìm…' : 'Tìm giảng viên' }}</button>
              <small v-if="hasUnsavedChanges">Lưu lịch rảnh trước khi tìm giảng viên.</small>
              <p v-if="matchError" class="text-danger" role="alert">{{ matchError }}</p>
              <p v-if="!matching && !tutors.length">Chưa có giảng viên phù hợp. Chọn môn/ngày rồi tìm lại.</p>
            </form>
            <!-- Danh sách thẻ gia sư -->
            <div class="tutors-list">
              <div v-for="tutor in filteredTutors" :key="tutor.id" class="tutor-card">
                <!-- Info gia sư: Avatar, Tên, Môn học & Đánh giá -->
                <div class="tutor-info-row">
                  <img :src="tutor.avatar" :alt="tutor.name" class="tutor-avatar" />
                  <div class="tutor-details">
                    <h4 class="tutor-name">{{ tutor.name }}</h4>
                    <p class="tutor-subject">{{ tutor.subject }}</p>

                  </div>
                </div>

                <div class="tutor-matched-slots"><button v-for="(slot, index) in tutor.slots" :key="index" class="btn btn-outline-primary mb-2" @click="handleSelectTutor(tutor, slot)">{{ new Date(slot.start).toLocaleString('vi-VN') }} → {{ new Date(slot.end).toLocaleTimeString('vi-VN', {hour:'2-digit', minute:'2-digit'}) }} · Đặt học thử</button></div>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import thoiGianRanhService from "../../services/thoiGianRanhService";
import product from "../../services/productService";
import { lopHocService } from "../../services/lopHocService";
import { availabilitySlots } from "../../services/flowHelpers";

const DAY_MAP = {
  1: { key: "Mon", name: "Thứ 2", short: "T2" },
  2: { key: "Tue", name: "Thứ 3", short: "T3" },
  3: { key: "Wed", name: "Thứ 4", short: "T4" },
  4: { key: "Thu", name: "Thứ 5", short: "T5" },
  5: { key: "Fri", name: "Thứ 6", short: "T6" },
  6: { key: "Sat", name: "Thứ 7", short: "T7" },
  0: { key: "Sun", name: "Chủ nhật", short: "CN" }
};

const KEY_TO_DAY_NUM = {
  Mon: 1,
  Tue: 2,
  Wed: 3,
  Thu: 4,
  Fri: 5,
  Sat: 6,
  Sun: 0
};

export default {
  name: "EduLinkMySchedule",
  data() {
    return {
      currentWeekOffset: 0,
      activeShiftFilter: "all", // 'all' | 'morning' | 'afternoon' | 'evening'
      saving: false,
      loading: false,
      hasUnsavedChanges: false,
      lastSavedTime: null,
      toast: {
        show: false,
        message: "",
        type: "success", // 'success' | 'error' | 'info'
        icon: "fa-solid fa-circle-check",
        timer: null
      },
      // Danh sách khung giờ tiếng Việt
      timeSlots: [
        { key: "07:00", label: "07:00 - 08:00", start: "07:00:00", end: "08:00:00", shift: "morning", period: "Sáng" },
        { key: "08:00", label: "08:00 - 09:00", start: "08:00:00", end: "09:00:00", shift: "morning", period: "Sáng" },
        { key: "09:00", label: "09:00 - 10:00", start: "09:00:00", end: "10:00:00", shift: "morning", period: "Sáng" },
        { key: "10:00", label: "10:00 - 11:00", start: "10:00:00", end: "11:00:00", shift: "morning", period: "Sáng" },
        { key: "11:00", label: "11:00 - 12:00", start: "11:00:00", end: "12:00:00", shift: "morning", period: "Trưa" },
        { key: "14:00", label: "14:00 - 15:00", start: "14:00:00", end: "15:00:00", shift: "afternoon", period: "Chiều" },
        { key: "15:00", label: "15:00 - 16:00", start: "15:00:00", end: "16:00:00", shift: "afternoon", period: "Chiều" },
        { key: "16:00", label: "16:00 - 17:00", start: "16:00:00", end: "17:00:00", shift: "afternoon", period: "Chiều" },
        { key: "18:00", label: "18:00 - 19:00", start: "18:00:00", end: "19:00:00", shift: "evening", period: "Tối" },
        { key: "19:00", label: "19:00 - 20:00", start: "19:00:00", end: "20:00:00", shift: "evening", period: "Tối" },
        { key: "20:00", label: "20:00 - 21:00", start: "20:00:00", end: "21:00:00", shift: "evening", period: "Tối" }
      ],
      selectedSlots: [], tutors: [], subjects: [], matchSubject: '', matchDate: '', matchDuration: 60, matching: false, matchError: '', scheduleError: '', loadGeneration: 0
    };
  },
  computed: {
    roleIsStudent() { return localStorage.getItem('role') === 'hoc_vien' },
    // Tính toán các ngày trong tuần hiện tại / đang chọn
    weekDays() {
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      const ref = new Date();
      const currentDay = ref.getDay();
      const diffToMonday = currentDay === 0 ? -6 : 1 - currentDay;
      ref.setDate(ref.getDate() + diffToMonday + this.currentWeekOffset * 7);
      ref.setHours(0, 0, 0, 0);

      const days = [
        { key: "Mon", name: "Thứ 2", short: "T2" },
        { key: "Tue", name: "Thứ 3", short: "T3" },
        { key: "Wed", name: "Thứ 4", short: "T4" },
        { key: "Thu", name: "Thứ 5", short: "T5" },
        { key: "Fri", name: "Thứ 6", short: "T6" },
        { key: "Sat", name: "Thứ 7", short: "T7" },
        { key: "Sun", name: "Chủ nhật", short: "CN" }
      ];

      return days.map((d, index) => {
        const dateObj = new Date(ref);
        dateObj.setDate(ref.getDate() + index);
        const dayNum = dateObj.getDate().toString().padStart(2, "0");
        const monthNum = (dateObj.getMonth() + 1).toString().padStart(2, "0");
        const isToday = dateObj.getTime() === today.getTime();
        return {
          ...d,
          date: dayNum,
          month: monthNum,
          fullDate: dateObj,
          isToday
        };
      });
    },
    // Chuỗi hiển thị khoảng thời gian tuần
    currentWeekText() {
      if (!this.weekDays || this.weekDays.length < 7) return "";
      const start = this.weekDays[0];
      const end = this.weekDays[6];
      return `Tuần từ ${start.date}/${start.month} đến ${end.date}/${end.month}/${end.fullDate.getFullYear()}`;
    },
    // Danh sách khung giờ hiển thị theo bộ lọc ca
    visibleTimeSlots() {
      if (this.activeShiftFilter === "all") return this.timeSlots;
      return this.timeSlots.filter(s => s.shift === this.activeShiftFilter);
    },
    // Sắp xếp giảng viên có ca trùng khớp lên trước
    filteredTutors() { return this.tutors }
  },
  mounted() {
    this.loadSavedSchedule();
    this.loadSubjects();
    window.addEventListener('edulink:user-updated', this.accountChanged);
    window.addEventListener('storage', this.accountChanged);
  },
  beforeUnmount() { this.loadGeneration++; clearTimeout(this.toast.timer); window.removeEventListener('edulink:user-updated', this.accountChanged); window.removeEventListener('storage', this.accountChanged); },
  methods: {
    // Tải lịch đã lưu từ Backend CSDL hoặc LocalStorage
    accountChanged() { this.selectedSlots = []; this.tutors = []; this.loadSavedSchedule(); },
    async loadSavedSchedule() {
      const generation = ++this.loadGeneration;
      this.selectedSlots = []; this.loading = true; this.scheduleError = '';
      try {
        const response = await thoiGianRanhService.getSchedule(localStorage.getItem('role'));
        if (generation !== this.loadGeneration) return;
        if (!response.status || !Array.isArray(response.data)) throw new Error(response.message || 'Không đọc được lịch rảnh.');
        this.selectedSlots = availabilitySlots(response.data, this.timeSlots.map(slot => slot.key));
        this.lastSavedTime = new Date(); this.hasUnsavedChanges = false;
      } catch (error) { if (generation === this.loadGeneration) this.scheduleError = error.message || 'Không thể tải lịch. Vui lòng thử lại.'; }
      finally { if (generation === this.loadGeneration) this.loading = false; }
    },
    async loadSubjects() { try { this.subjects = (await lopHocService.getMonHoc()).data || []; } catch (error) { this.matchError = error.message; } },
    async findTutors() {
      this.matching = true; this.matchError = ''; this.tutors = [];
      try {
        const result = await product.matching({ id_mon_hoc: this.matchSubject, date: this.matchDate, duration_minutes: this.matchDuration });
        this.tutors = (result.data || []).map(row => ({ ...row, id: row.id_giao_vien, name: row.ho_ten, avatar: row.hinh_anh, subject: this.subjects.find(subject => subject.id === Number(this.matchSubject))?.ten_mon_hoc }));
      } catch (error) { this.matchError = error.message; }
      finally { this.matching = false; }
    },

    // Kiểm tra slot có đang được chọn hay không (hỗ trợ cả định dạng cũ)
    isSlotSelected(dayKey, timeKey) {
      const standardKey = `${dayKey}_${timeKey}`;
      if (this.selectedSlots.includes(standardKey)) return true;
      // Fallback kiểm tra dạng cũ "Wed_9:00 AM"
      const legacyKey = `${dayKey}_${parseInt(timeKey)}:00 AM`;
      return this.selectedSlots.includes(legacyKey);
    },

    // Bật/tắt chọn khung giờ
    toggleSlot(dayKey, timeKey) {
      const standardKey = `${dayKey}_${timeKey}`;
      const idx = this.selectedSlots.indexOf(standardKey);
      if (idx > -1) {
        this.selectedSlots.splice(idx, 1);
      } else {
        // Nếu có legacy key thì xóa đi
        const legacyKey = `${dayKey}_${parseInt(timeKey)}:00 AM`;
        const legacyIdx = this.selectedSlots.indexOf(legacyKey);
        if (legacyIdx > -1) {
          this.selectedSlots.splice(legacyIdx, 1);
        }
        this.selectedSlots.push(standardKey);
      }
      this.hasUnsavedChanges = true;
    },

    // Tooltip giải thích khi rê chuột vào ô
    getCellTooltip(day, slot) {
      const isSelected = this.isSlotSelected(day.key, slot.key);
      return `${isSelected ? 'Bỏ chọn' : 'Chọn'} ${day.name} (${slot.label})`;
    },

    // Đổi tuần
    changeWeek(offset) {
      this.currentWeekOffset += offset;
    },

    // Quay về tuần này
    resetToCurrentWeek() {
      this.currentWeekOffset = 0;
    },

    // Xóa tất cả các ô đang chọn
    clearAllSlots() {
      if (this.selectedSlots.length === 0) return;
      if (confirm("Bạn có chắc chắn muốn bỏ chọn tất cả các khung giờ rảnh hiện tại không?")) {
        this.selectedSlots = [];
        this.hasUnsavedChanges = true;
      }
    },

    // LƯU LỊCH RẢNH VÀO CƠ SỞ DỮ LIỆU
    async saveSchedule() {
      if (this.saving) return;
      this.saving = true;
      const schedules = this.selectedSlots.map(key => {
        const [day, time] = key.split('_'); const slot = this.timeSlots.find(value => value.key === time);
        return { ngay_trong_tuan: KEY_TO_DAY_NUM[day], thoi_gian_bat_dau: slot.start, thoi_gian_ket_thuc: slot.end };
      });
      try {
        const result = await thoiGianRanhService.updateSchedule(localStorage.getItem('role'), schedules);
        if (!result.status) throw new Error(result.message || 'Không thể lưu lịch.');
        this.hasUnsavedChanges = false; this.lastSavedTime = new Date(); this.scheduleError = '';
        this.showToast('success', result.message || 'Đã lưu lịch rảnh vào máy chủ.'); this.tutors = [];
      } catch (error) { this.showToast('error', error.message || 'Không thể lưu lịch rảnh.'); }
      finally { this.saving = false; }
    },

    // Tính thời gian kết thúc mặc định (+1 tiếng)
    calcEndTime(timeStr) {
      const hour = parseInt(timeStr.split(":")[0]) || 0;
      const endHour = (hour + 1).toString().padStart(2, "0");
      return `${endHour}:00:00`;
    },

    // Hiển thị thông báo Toast
    showToast(type, message) {
      if (this.toast.timer) clearTimeout(this.toast.timer);
      this.toast.type = type;
      this.toast.message = message;
      this.toast.icon =
        type === "success"
          ? "fa-solid fa-circle-check"
          : type === "error"
          ? "fa-solid fa-triangle-exclamation"
          : "fa-solid fa-circle-info";
      this.toast.show = true;

      this.toast.timer = setTimeout(() => {
        this.toast.show = false;
      }, 3500);
    },

    // Xử lý khi bấm nút chọn gia sư
    handleSelectTutor(tutor, slot) {
      this.$router.push({ path: '/dat-lich-hoc-thu', query: { id_giao_vien: tutor.id_giao_vien, id_mon_hoc: this.matchSubject, subject: tutor.subject, start: slot.start, end: slot.end } });
    }
  }
};
</script>

<style scoped>
/* Reset & Căn chỉnh chung */
.edulink-page {
  min-height: calc(100vh - 70px);
  display: flex;
  flex-direction: column;
  background-color: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

.main-content {
  flex: 1;
  padding: 32px 24px 60px;
}

.content-container {
  max-width: 1260px;
  margin: 0 auto;
}

/* Tiêu đề trang */
.page-title-area {
  margin-bottom: 24px;
}

.page-title {
  font-size: 26px;
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

.btn-save-top {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 14.5px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.btn-save-top:hover {
  background-color: #0050b3;
  border-color: #0050b3;
  transform: translateY(-1px);
}

/* Thông báo Toast */
.schedule-toast {
  position: fixed;
  top: 85px;
  right: 28px;
  z-index: 1050;
  padding: 14px 20px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  font-size: 14.5px;
}

.toast-success {
  background-color: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.toast-error {
  background-color: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.toast-info {
  background-color: #eff6ff;
  color: #1e40af;
  border: 1px solid #bfdbfe;
}

.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.25s ease;
}

.toast-fade-enter-from,
.toast-fade-leave-to {
  opacity: 0;
  transform: translateY(-12px);
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
  min-width: 0;
}

.schedule-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  padding: 24px 24px 20px;
}

/* Header của Schedule Card */
.schedule-card-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
}

/* Điều hướng tuần */
.week-nav {
  display: flex;
  align-items: center;
  gap: 10px;
}

.nav-arrow-btn {
  background: transparent;
  border: 1px solid #e2e8f0;
  width: 34px;
  height: 34px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s ease;
}

.nav-arrow-btn:hover {
  background-color: #f1f5f9;
  color: #0f172a;
  border-color: #cbd5e1;
}

.week-range-text {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  white-space: nowrap;
}

.btn-today {
  font-size: 12px;
  padding: 4px 10px;
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  color: #475569;
  font-weight: 600;
  cursor: pointer;
}

.btn-today:hover {
  background-color: #e2e8f0;
  color: #0f172a;
}

/* Bộ lọc ca học (Tabs) */
.time-filter-tabs {
  display: flex;
  background-color: #f1f5f9;
  padding: 3px;
  border-radius: 8px;
  gap: 2px;
}

.filter-tab-btn {
  border: none;
  background: transparent;
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 12.5px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
}

.filter-tab-btn:hover {
  color: #0f172a;
}

.filter-tab-btn.active {
  background-color: #ffffff;
  color: #0060d2;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

/* Chú thích Legend */
.schedule-legend {
  display: flex;
  align-items: center;
  gap: 18px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.legend-dot.available {
  background-color: #e2e8f0;
}

.legend-tick {
  width: 18px;
  height: 18px;
  background-color: #0060d2;
  color: #ffffff;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
}

/* Bảng thời gian (Calendar Table) */
.calendar-table-wrapper {
  overflow-x: auto;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
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
  width: 115px;
  padding: 14px 10px;
  font-size: 13px;
  font-weight: 700;
  color: #475569;
  background-color: #f8fafc;
}

.day-col-header {
  padding: 12px 8px;
  background-color: #f8fafc;
  font-size: 13px;
  color: #475569;
  min-width: 65px;
}

.day-col-header.active-day {
  color: #0060d2;
  background-color: #eff6ff;
  font-weight: 800;
}

.day-name {
  font-weight: 700;
  font-size: 13px;
  line-height: 1.3;
}

.day-num {
  font-size: 15px;
  font-weight: 700;
  line-height: 1.3;
}

/* Time rows & cells */
.time-cell {
  background-color: #ffffff;
  padding: 12px 8px;
  vertical-align: middle;
}

.time-main {
  font-size: 12.5px;
  font-weight: 700;
  color: #334155;
  white-space: nowrap;
}

.time-sub {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 2px;
}

/* Ô chọn giờ */
.slot-cell {
  height: 60px;
  background-color: #ffffff;
  cursor: pointer;
  vertical-align: middle;
  text-align: center;
  transition: all 0.15s ease;
  position: relative;
  padding: 0;
  user-select: none;
}

.slot-cell:hover:not(.is-selected) {
  background-color: #eff6ff;
}

.slot-cell:hover:not(.is-selected) .cell-hover-hint {
  opacity: 1;
}

.cell-hover-hint {
  opacity: 0;
  color: #93c5fd;
  font-size: 13px;
  transition: opacity 0.15s ease;
}

/* Ô đã được chọn: Màu xanh đậm đặc trưng + Dấu tích */
.slot-cell.is-selected {
  background-color: #0060d2;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
}

.slot-cell.is-selected:hover {
  background-color: #0052b3;
}

/* Wrapper dấu tích (Tick mark) */
.selected-tick-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.check-icon {
  color: #ffffff;
  font-size: 22px;
  font-weight: 900;
  animation: tickPop 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes tickPop {
  0% {
    transform: scale(0.35);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Thanh hành động bên dưới bảng */
.schedule-bottom-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid #f1f5f9;
}

.selection-summary {
  display: flex;
  align-items: center;
  font-size: 14.5px;
  color: #475569;
}

.badge-status {
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
}

.badge-status.unsaved {
  background-color: #fef3c7;
  color: #92400e;
}

.badge-status.saved {
  background-color: #ecfdf5;
  color: #065f46;
}

.bottom-action-buttons {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-save-main {
  background-color: #0060d2;
  border-color: #0060d2;
  font-size: 14px;
  font-weight: 700;
  border-radius: 8px;
  padding: 8px 20px;
  box-shadow: 0 2px 6px rgba(0, 96, 210, 0.25);
  transition: all 0.2s ease;
}

.btn-save-main:hover {
  background-color: #0050b3;
  border-color: #0050b3;
  transform: translateY(-1px);
}

/* --- CỘT PHẢI: GỢI Ý GIẢNG VIÊN (SUGGESTED TUTORS) --- */
.tutors-column {
  flex: 1 1 32%;
  min-width: 290px;
}

.tutors-header {
  margin-bottom: 18px;
}

.tutors-title {
  font-size: 19px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.3px;
  margin-bottom: 0;
}

.matches-badge {
  background-color: #e6f9ed;
  color: #0e8345;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
}

.tutors-subtitle {
  font-size: 13.5px;
  color: #64748b;
  margin-top: 4px;
  margin-bottom: 0;
}

/* Danh sách card gia sư */
.tutors-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.tutor-card {
  background: #ffffff;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
  padding: 18px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.tutor-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.tutor-info-row {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 14px;
}

.tutor-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
  border: 2px solid #e2e8f0;
}

.tutor-details {
  flex: 1;
  min-width: 0;
}

.tutor-name {
  font-size: 15.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 2px;
}

.tutor-subject {
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.tutor-rating {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
}

.star-icon {
  color: #f59e0b;
  font-size: 12px;
}

.rating-num {
  font-weight: 700;
  color: #0f172a;
}

.sessions-count {
  color: #94a3b8;
}

/* Pills khung giờ khớp */
.tutor-matched-slots {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 14px;
}

.matched-slot-pill {
  background-color: #f1f5f9;
  color: #475569;
  font-size: 11.5px;
  font-weight: 600;
  padding: 5px 10px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

/* Nút chọn gia sư */
.btn-select-tutor {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  font-size: 13.5px;
  font-weight: 700;
  padding: 10px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.btn-select-tutor:hover {
  background-color: #0050b3;
  color: #ffffff;
}

/* Responsive */
@media (max-width: 992px) {
  .schedule-layout {
    flex-direction: column;
  }
  .schedule-column,
  .tutors-column {
    width: 100%;
  }
}
</style>
