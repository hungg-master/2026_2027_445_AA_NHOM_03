<template>
  <div class="edu-page">
    <main class="main-content">
      <div class="content-container">
        <!-- Page Header -->
        <div class="page-title-area">
          <div class="title-left">
            <h1 class="page-title">Lịch Học Của Tôi</h1>
            <p class="page-subtitle">Theo dõi lịch học các lớp bạn đã đăng ký và chuẩn bị cho buổi học</p>
          </div>
          <div class="title-right">
            <router-link to="/client/lop-hoc" class="btn btn-primary-outline">
              <i class="fa-solid fa-magnifying-glass me-2"></i>Khám phá thêm lớp học
            </router-link>
          </div>
        </div>

        <!-- Filter & Week Navigation Bar -->
        <div class="filter-bar">
          <div class="filter-left">
            <!-- Điều hướng tuần nhanh -->
            <div class="week-nav-group">
              <button class="nav-arrow-btn" @click="changeWeek(-1)" title="Tuần trước">
                <i class="fa-solid fa-chevron-left"></i>
              </button>
              <button class="btn-today" @click="goToCurrentWeek">Tuần này</button>
              <button class="nav-arrow-btn" @click="changeWeek(1)" title="Tuần sau">
                <i class="fa-solid fa-chevron-right"></i>
              </button>
            </div>

            <!-- Date pickers -->
            <div class="filter-group">
              <label>Từ ngày</label>
              <input type="date" class="form-input" v-model="filters.from_date" @change="onDateFilterChange" />
            </div>
            <div class="filter-group">
              <label>Đến ngày</label>
              <input type="date" class="form-input" v-model="filters.to_date" @change="onDateFilterChange" />
            </div>

            <!-- Status filter -->
            <div class="filter-group">
              <label>Trạng thái</label>
              <select class="form-input form-select" v-model="filters.trang_thai_buoi" @change="filterByStatus">
                <option value="">-- Tất cả buổi học --</option>
                <option value="sap_toi">Sắp tới</option>
                <option value="dang_dien_ra">Đang diễn ra</option>
                <option value="da_hoc">Đã hoàn thành</option>
              </select>
            </div>
          </div>

          <!-- View Mode Switcher -->
          <div class="filter-right">
            <label class="d-none d-md-block">Chế độ xem</label>
            <div class="view-switcher">
              <button :class="['view-btn', { active: viewMode === 'week' }]" @click="setView('week')">
                <i class="fa-solid fa-table-cells me-1"></i> Theo Tuần
              </button>
              <button :class="['view-btn', { active: viewMode === 'list' }]" @click="setView('list')">
                <i class="fa-solid fa-list-ul me-1"></i> Danh sách
              </button>
            </div>
          </div>
        </div>

        <!-- Chú thích hình thức học -->
        <div class="schedule-legend-bar">
          <div class="legend-items">
            <span class="legend-item"><span class="legend-chip pill-online"></span> Lớp Online (Google Meet)</span>
            <span class="legend-item"><span class="legend-chip pill-offline"></span> Lớp Trực tiếp (Tại trung tâm)</span>
            <span class="legend-item"><span class="legend-chip status-da_hoc"></span> Buổi đã kết thúc</span>
          </div>
          <div class="total-sessions-badge">
            Tổng cộng: <strong>{{ filteredLichHoc.length }}</strong> buổi học
          </div>
        </div>

        <!-- Loading -->
        <p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p>
        <div v-if="loading" class="loading-box">
          <i class="fa-solid fa-spinner fa-spin fs-2 text-primary mb-3"></i>
          <p class="mb-0 fw-medium">Đang tải lịch học của bạn...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="!filteredLichHoc || filteredLichHoc.length === 0" class="empty-box">
          <div class="empty-icon-wrap">
            <i class="fa-regular fa-calendar-xmark"></i>
          </div>
          <h4>Không có buổi học nào</h4>
          <p class="text-muted">Bạn chưa có lịch học nào trong khoảng thời gian này hoặc không khớp bộ lọc.</p>
          <div class="d-flex justify-content-center gap-2 mt-3">
            <button class="btn btn-outline" @click="resetFilters">Xóa bộ lọc</button>
            <router-link to="/client/lop-hoc" class="btn btn-primary">Đăng ký lớp học ngay</router-link>
          </div>
        </div>

        <!-- 1. WEEK VIEW (DẠNG BẢNG LƯỚI TUẦN CHUẨN) -->
        <div v-else-if="viewMode === 'week'" class="calendar-table-wrapper">
          <table class="calendar-table">
            <thead>
              <tr>
                <th class="time-col-header">
                  <div class="time-header-title">Khung giờ</div>
                </th>
                <th
                  v-for="day in weekDays"
                  :key="day.key"
                  :class="['day-col-header', { 'is-today': day.isToday }]"
                >
                  <div class="day-name">{{ day.name }}</div>
                  <div class="day-date">{{ day.date }} / {{ day.month }}</div>
                  <div v-if="day.isToday" class="today-badge">Hôm nay</div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="hour in timeSlots" :key="hour">
                <td class="time-cell">
                  <span class="time-text">{{ hour }}</span>
                </td>
                <td
                  v-for="day in weekDays"
                  :key="day.key + '_' + hour"
                  :class="['slot-cell', { 'today-col': day.isToday }]"
                >
                  <div
                    v-for="dk in getLopInSlot(day, hour)"
                    :key="dk.id_buoi_hoc"
                    class="session-pill"
                    :class="[
                      'pill-' + (dk.lop_hoc?.hinh_thuc || 'offline'),
                      'pill-status-' + (dk.trang_thai_buoi || 'sap_toi')
                    ]"
                    @click="openDetail(dk)"
                  >
                    <div class="pill-header">
                      <span class="pill-time">
                        <i class="fa-regular fa-clock me-1"></i>
                        {{ formatTime(dk.lop_hoc?.thoi_gian_bat_dau) }} - {{ formatTime(dk.lop_hoc?.thoi_gian_ket_thuc) }}
                      </span>
                      <span :class="['pill-badge', dk.lop_hoc?.hinh_thuc === 'online' ? 'badge-blue' : 'badge-green']">
                        {{ dk.lop_hoc?.hinh_thuc === 'online' ? 'Online' : (dk.lop_hoc?.phong_hoc?.so_phong || 'Offline') }}
                      </span>
                    </div>

                    <div class="pill-title">{{ dk.lop_hoc?.mon_hoc?.ten_mon_hoc }}</div>

                    <div class="pill-footer">
                      <span class="pill-gv text-truncate">
                        <i class="fa-solid fa-user-tie me-1"></i>
                        {{ dk.lop_hoc?.giao_vien?.ho_ten || 'Giảng viên' }}
                      </span>
                      <span class="status-indicator" :class="'indicator-' + dk.trang_thai_buoi">
                        {{ statusTextShort(dk.trang_thai_buoi) }}
                      </span>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 2. LIST VIEW (DẠNG DANH SÁCH CHI TIẾT) -->
        <div v-else-if="viewMode === 'list'" class="list-view">
          <div class="session-cards-list">
            <div
              v-for="dk in filteredLichHoc"
              :key="dk.id_buoi_hoc"
              class="session-card"
              :class="['accent-' + getAccent(dk.lop_hoc?.hinh_thuc), 'card-' + dk.trang_thai_buoi]"
            >
              <div class="session-date-box">
                <span class="date-month">{{ formatMonth(dk.lop_hoc?.thoi_gian_bat_dau) }}</span>
                <span class="date-day">{{ formatDay(dk.lop_hoc?.thoi_gian_bat_dau) }}</span>
                <span class="date-time">{{ formatTime(dk.lop_hoc?.thoi_gian_bat_dau) }}</span>
              </div>

              <div class="session-info">
                <div class="session-tags">
                  <span class="tag-pill tag-type-info">{{ dk.lop_hoc?.mon_hoc?.ten_mon_hoc }}</span>
                  <span :class="['tag-pill', statusPillClass(dk.trang_thai_buoi)]">
                    {{ statusPillText(dk.trang_thai_buoi) }}
                  </span>
                  <span :class="['tag-pill', dk.lop_hoc?.hinh_thuc === 'online' ? 'tag-online' : 'tag-offline']">
                    <i :class="dk.lop_hoc?.hinh_thuc === 'online' ? 'fa-solid fa-video me-1' : 'fa-solid fa-location-dot me-1'"></i>
                    {{ dk.lop_hoc?.hinh_thuc === 'online' ? 'Online qua Meet' : (dk.lop_hoc?.phong_hoc?.so_phong || 'Tại trung tâm') }}
                  </span>
                </div>

                <h3 class="session-name">
                  {{ dk.lop_hoc?.mon_hoc?.ten_mon_hoc }}
                  <span class="session-teacher-sub text-muted fw-normal ms-2 fs-6">
                    (GV: {{ dk.lop_hoc?.giao_vien?.ho_ten || 'Đang cập nhật' }})
                  </span>
                </h3>

                <div class="session-meta">
                  <div class="meta-item">
                    <i class="fa-regular fa-clock meta-icon"></i>
                    <span>{{ formatTime(dk.lop_hoc?.thoi_gian_bat_dau) }} - {{ formatTime(dk.lop_hoc?.thoi_gian_ket_thuc) }}</span>
                  </div>
                  <div class="meta-item">
                    <i :class="dk.lop_hoc?.hinh_thuc === 'online' ? 'fa-solid fa-video meta-icon' : 'fa-solid fa-location-dot meta-icon'"></i>
                    <span>
                      {{ dk.lop_hoc?.hinh_thuc === 'online'
                        ? 'Google Meet (Đường dẫn phòng học bảo mật)'
                        : (dk.lop_hoc?.phong_hoc?.so_phong + ' - ' + (dk.lop_hoc?.phong_hoc?.dia_chi || 'EduLink')) }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="session-actions">
                <button
                  v-if="canJoinSession(dk)"
                  class="btn btn-primary"
                  @click="onJoinRoomClick(dk)"
                >
                  <i class="fa-solid fa-video me-1"></i> Vào phòng học
                </button>
                <button
                  v-else-if="isSessionExpired(dk)"
                  class="btn btn-secondary disabled opacity-75"
                  disabled
                >
                  <i class="fa-solid fa-ban me-1"></i> Buổi học đã qua
                </button>
                <button
                  v-else
                  class="btn btn-secondary disabled opacity-75"
                  disabled
                >
                  <i class="fa-solid fa-clock me-1"></i> Chưa đến giờ học
                </button>
                <button
                  class="btn btn-outline ms-2"
                  @click="openDetail(dk)"
                >
                  Chi tiết
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL CHI TIẾT BUỔI HỌC -->
        <div v-if="selectedSession" class="modal-overlay" @click.self="selectedSession = null">
          <div class="modal-card">
            <div class="modal-card-header">
              <div>
                <span class="modal-badge mb-1" :class="selectedSession.lop_hoc?.hinh_thuc === 'online' ? 'badge-blue' : 'badge-green'">
                  {{ selectedSession.lop_hoc?.hinh_thuc === 'online' ? 'Lớp Trực Tuyến' : 'Lớp Trực Tiếp' }}
                </span>
                <h4 class="mb-0 fw-bold">{{ selectedSession.lop_hoc?.mon_hoc?.ten_mon_hoc }}</h4>
              </div>
              <button class="btn-close-modal" @click="selectedSession = null">&times;</button>
            </div>

            <div class="modal-card-body">
              <!-- Giảng viên -->
              <div class="detail-gv-box">
                <img
                  :src="selectedSession.lop_hoc?.giao_vien?.hinh_anh || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=240&q=80'"
                  alt="GV"
                  class="gv-avatar"
                />
                <div>
                  <h6 class="mb-0 fw-bold text-dark">{{ selectedSession.lop_hoc?.giao_vien?.ho_ten }}</h6>
                  <small class="text-muted">{{ selectedSession.lop_hoc?.giao_vien?.chuc_danh || 'Giảng viên chuyên môn' }}</small>
                </div>
              </div>

              <!-- Thông tin chi tiết -->
              <div class="detail-rows">
                <div class="detail-row">
                  <span class="detail-label"><i class="fa-regular fa-calendar me-2 text-primary"></i>Thời gian:</span>
                  <span class="detail-val fw-bold">
                    {{ formatFullDateVN(selectedSession.lop_hoc?.thoi_gian_bat_dau) }}
                  </span>
                </div>
                <div class="detail-row">
                  <span class="detail-label"><i class="fa-regular fa-clock me-2 text-primary"></i>Khung giờ:</span>
                  <span class="detail-val">
                    {{ formatTime(selectedSession.lop_hoc?.thoi_gian_bat_dau) }} - {{ formatTime(selectedSession.lop_hoc?.thoi_gian_ket_thuc) }}
                  </span>
                </div>
                <div class="detail-row">
                  <span class="detail-label"><i class="fa-solid fa-flag-checkered me-2 text-primary"></i>Trạng thái:</span>
                  <span :class="['tag-pill', statusPillClass(selectedSession.trang_thai_buoi)]">
                    {{ statusPillText(selectedSession.trang_thai_buoi) }}
                  </span>
                </div>
                <div class="detail-row" v-if="selectedSession.lop_hoc?.hinh_thuc === 'offline'">
                  <span class="detail-label"><i class="fa-solid fa-location-dot me-2 text-primary"></i>Phòng học:</span>
                  <span class="detail-val">
                    {{ selectedSession.lop_hoc?.phong_hoc?.so_phong }} - {{ selectedSession.lop_hoc?.phong_hoc?.dia_chi }}
                  </span>
                </div>
                <div class="detail-row" v-if="selectedSession.lop_hoc?.hinh_thuc === 'online' || selectedSession.lop_hoc?.link_online">
                  <span class="detail-label"><i class="fa-solid fa-video me-2 text-primary"></i>Phòng trực tuyến:</span>
                  <span class="detail-val">
                    <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 fs-6 rounded-pill fw-bold">
                      <i class="fa-solid fa-chalkboard-user me-1"></i>
                      {{ getRoomCode(selectedSession) }}
                    </span>
                  </span>
                </div>
              </div>
            </div>

            <div class="modal-card-footer d-flex flex-column gap-2">
              <div v-if="isSessionExpired(selectedSession)" class="alert alert-warning py-2 px-3 mb-0 small text-center fw-medium border-0 rounded-3">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                Buổi học này đã kết thúc, bạn không thể tham gia vào phòng học nữa.
              </div>
              <div v-else-if="isSessionNotStarted(selectedSession)" class="alert alert-info py-2 px-3 mb-0 small text-center fw-medium border-0 rounded-3">
                <i class="fa-solid fa-clock me-1"></i>
                Chưa đến thời gian buổi học (phòng học sẽ mở trước giờ học 15 phút).
              </div>
              <div v-else-if="canJoinSession(selectedSession)" class="alert alert-success py-2 px-3 mb-0 small text-center fw-medium border-0 rounded-3">
                <i class="fa-solid fa-circle-check me-1"></i>
                Buổi học đang diễn ra. Bạn có thể tham gia phòng học trực tuyến ngay bây giờ!
              </div>

              <div class="d-flex gap-2 w-100">
                <button
                  v-if="canJoinSession(selectedSession)"
                  class="btn btn-primary flex-grow-1 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                  @click="onJoinRoomClick(selectedSession)"
                >
                  <i class="fa-solid fa-video"></i>
                  <span>Vào phòng học</span>
                </button>
                <button
                  v-else-if="isSessionExpired(selectedSession)"
                  class="btn btn-secondary flex-grow-1 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                  disabled
                >
                  <i class="fa-solid fa-ban"></i>
                  <span>Buổi học đã qua</span>
                </button>
                <button
                  v-else
                  class="btn btn-secondary flex-grow-1 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                  disabled
                >
                  <i class="fa-solid fa-clock"></i>
                  <span>Chưa đến giờ học</span>
                </button>
                <button class="btn btn-outline-secondary px-4 py-2" @click="selectedSession = null">Đóng</button>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL CẢNH BÁO: CHƯA CÓ FACE ID -->
      </div>
    </main>
  </div>
</template>

<script>
import { lichHocService } from '../../services/lichHocService'
import { calendarSession, dateParts } from '../../services/flowHelpers'

export default {
  name: 'LichHocHocVien',
  data() {
    return {
      loading: false,
      error: '',
      viewMode: 'week',
      filters: { from_date: '', to_date: '', trang_thai_buoi: '' },
      dsLichHoc: [],
      filteredLichHoc: [],
      weekDays: [],
      timeSlots: [
        '07:00','08:00','09:00','10:00','11:00','12:00',
        '13:00','14:00','15:00','16:00','17:00','18:00',
        '19:00','20:00','21:00'
      ],
      selectedSession: null,

      // Face verification is performed in the selected room.
      showFaceIdRequiredModal: false,
      showFaceVerifyModal: false,
      isFaceModelLoading: false,
      isFaceModelLoaded: false,
      isFaceVerifying: false,
      verifyResult: 'pending',
      verifyMessageText: '',
      targetRoomSession: null,
      verifyStream: null,
      verifyInterval: null,
      autoVerifySafetyTimer: null
    }
  },
  computed: {
    verifyStatusClass() {
      if (this.verifyResult === 'success') return 'border-success-neon';
      if (this.verifyResult === 'failed') return 'border-danger-neon';
      return 'border-primary-neon';
    },
    verifyAlertClass() {
      if (this.verifyResult === 'success') return 'alert-success';
      if (this.verifyResult === 'failed') return 'alert-danger';
      return 'alert-info';
    },
    verifyAlertIcon() {
      if (this.verifyResult === 'success') return 'fa-solid fa-circle-check text-success';
      if (this.verifyResult === 'failed') return 'fa-solid fa-circle-xmark text-danger';
      return 'fa-solid fa-spinner fa-spin text-primary';
    }
  },
  mounted() {
    this.setDefaultFilters()
    this.buildWeekDays()
    this.loadData()
  },
  methods: {
    // Định dạng YYYY-MM-DD an toàn theo giờ địa phương, không bị lệch timezone UTC
    formatDateYMD(d) {
      const y = d.getFullYear()
      const m = String(d.getMonth() + 1).padStart(2, '0')
      const day = String(d.getDate()).padStart(2, '0')
      return `${y}-${m}-${day}`
    },

    // Phân tích chuỗi ngày giờ từ API mà không bị lệch múi giờ (UTC -> GMT+7)
    parseDateParts(iso) { return dateParts(iso) },

    setDefaultFilters() {
      const now = new Date()
      const dayOfWeek = now.getDay()
      // Thứ 2 là đầu tuần
      const diffToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek
      const monday = new Date(now)
      monday.setDate(now.getDate() + diffToMonday)

      const sunday = new Date(monday)
      sunday.setDate(monday.getDate() + 6)

      this.filters.from_date = this.formatDateYMD(monday)
      this.filters.to_date = this.formatDateYMD(sunday)
    },

    buildWeekDays() {
      if (!this.filters.from_date) return
      const [y, m, d] = this.filters.from_date.split('-').map(Number)
      const start = new Date(y, m - 1, d)
      const days = []
      const dayNames = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7']
      const today = new Date()
      const todayKey = this.formatDateYMD(today)

      for (let i = 0; i < 7; i++) {
        const cur = new Date(start)
        cur.setDate(start.getDate() + i)
        const curKey = this.formatDateYMD(cur)
        days.push({
          key: 'day_' + i,
          name: dayNames[cur.getDay()],
          date: String(cur.getDate()).padStart(2, '0'),
          month: String(cur.getMonth() + 1).padStart(2, '0'),
          dateKey: curKey,
          isToday: curKey === todayKey,
        })
      }
      this.weekDays = days
    },

    changeWeek(step) {
      if (!this.filters.from_date) return
      const [y, m, d] = this.filters.from_date.split('-').map(Number)
      const start = new Date(y, m - 1, d)
      start.setDate(start.getDate() + step * 7)

      const end = new Date(start)
      end.setDate(start.getDate() + 6)

      this.filters.from_date = this.formatDateYMD(start)
      this.filters.to_date = this.formatDateYMD(end)

      this.buildWeekDays()
      this.loadData()
    },

    goToCurrentWeek() {
      this.setDefaultFilters()
      this.buildWeekDays()
      this.loadData()
    },

    onDateFilterChange() {
      this.buildWeekDays()
      this.loadData()
    },

    async loadData() {
      this.loading = true
      this.error = ''
      try {
        const res = await lichHocService.getLichHoc({
          from_date: this.filters.from_date,
          to_date: this.filters.to_date,
        })
        if (res && res.status) {
          this.dsLichHoc = (res.data || []).map(calendarSession)
          this.filterByStatus()
        } else throw new Error(res?.message || 'Không thể tải lịch học.')
      } catch (e) {
        this.error = e.message || 'Không thể tải lịch học.'
        this.dsLichHoc = []
        this.filteredLichHoc = []
      } finally {
        this.loading = false
      }
    },

    filterByStatus() {
      if (!this.filters.trang_thai_buoi) {
        this.filteredLichHoc = this.dsLichHoc
      } else {
        this.filteredLichHoc = this.dsLichHoc.filter(dk => dk.trang_thai_buoi === this.filters.trang_thai_buoi)
      }
    },

    resetFilters() {
      this.filters.trang_thai_buoi = ''
      this.goToCurrentWeek()
    },

    setView(mode) {
      this.viewMode = mode
    },

    getLopInSlot(day, hour) {
      const hourInt = parseInt(hour.split(':')[0])
      return (this.filteredLichHoc || []).filter(dk => {
        if (!dk.lop_hoc?.thoi_gian_bat_dau) return false
        const p = this.parseDateParts(dk.lop_hoc.thoi_gian_bat_dau)
        if (!p) return false
        return p.dateKey === day.dateKey && p.hour === hourInt
      })
    },

    openDetail(dk) {
      this.selectedSession = dk
    },

    formatTime(iso) {
      const p = this.parseDateParts(iso)
      return p ? p.timeStr : ''
    },

    formatDay(iso) {
      const p = this.parseDateParts(iso)
      return p ? String(p.day).padStart(2, '0') : ''
    },

    formatMonth(iso) {
      const p = this.parseDateParts(iso)
      if (!p) return ''
      return `Thg ${p.month}`
    },

    formatFullDateVN(iso) {
      const p = this.parseDateParts(iso)
      if (!p) return ''
      const dayNames = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7']
      const d = new Date(p.year, p.month - 1, p.day)
      return `${dayNames[d.getDay()]}, ngày ${String(p.day).padStart(2, '0')}/${String(p.month).padStart(2, '0')}/${p.year}`
    },

    getAccent(h) {
      return h === 'online' ? 'blue' : 'green'
    },

    statusPillClass(s) {
      return {
        sap_toi: 'tag-blue',
        dang_dien_ra: 'tag-green',
        da_hoc: 'tag-grey',
      }[s] || 'tag-grey'
    },

    statusPillText(s) {
      return {
        sap_toi: 'Sắp tới',
        dang_dien_ra: 'Đang diễn ra',
        da_hoc: 'Đã hoàn thành',
      }[s] || 'Chưa diễn ra'
    },

    statusTextShort(s) {
      return {
        sap_toi: 'Sắp tới',
        dang_dien_ra: 'Đang học',
        da_hoc: 'Đã xong',
      }[s] || ''
    },

    // ===== KIỂM TRA THỜI GIAN BUỔI HỌC =====
    getSessionDates(session) {
      if (!session || !session.lop_hoc) return { start: null, end: null };

      const parseToDate = iso => { const date = new Date(iso); return Number.isNaN(date.getTime()) ? null : date; };

      return {
        start: parseToDate(session.lop_hoc.thoi_gian_bat_dau),
        end: parseToDate(session.lop_hoc.thoi_gian_ket_thuc)
      };
    },

    getSessionTimingStatus(session) {
      if (!session) return 'expired';

      // 1. Nếu backend đã đánh dấu là 'da_hoc'
      if (['da_hoc','da_huy','cancelled'].includes(session.trang_thai_buoi) || ['completed','cancelled'].includes(session.trang_thai)) return 'expired';

      const { start, end } = this.getSessionDates(session);
      if (!start || !end) return 'expired';
      const now = new Date();

      // 2. Buổi học đã qua thời gian kết thúc
      if (end && end < now) {
        return 'expired';
      }

      // 3. Đang trong khung giờ học
      if (start && end && now >= start && now <= end) {
        return 'in_progress';
      }

      // 4. Nếu chưa tới giờ học (cho phép vào trước giờ học 15 phút)
      if (start) {
        const earlyAllowedTime = new Date(start.getTime() - 15 * 60 * 1000);
        if (now < earlyAllowedTime) {
          return 'not_started';
        }
        return 'in_progress';
      }

      if (session.trang_thai_buoi === 'dang_dien_ra') {
        return 'in_progress';
      }

      return 'expired';
    },

    isSessionExpired(session) {
      return this.getSessionTimingStatus(session) === 'expired';
    },

    isSessionNotStarted(session) {
      return this.getSessionTimingStatus(session) === 'not_started';
    },

    canJoinSession(session) {
      return this.getSessionTimingStatus(session) === 'in_progress';
    },

    // The room requests a new actor-bound Face ID proof for this dated session.
    onJoinRoomClick(session) {
      if (!session?.id_buoi_hoc || !this.canJoinSession(session)) return;
      this.$router.push({ name: 'phong-hoc', params: { id: session.id_buoi_hoc }, query: { session: session.id_buoi_hoc } });
    }
  }
}
</script>

<style scoped>
.edu-page {
  min-height: 100vh;
  background: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 30px 24px 60px;
}

.content-container {
  max-width: 1320px;
  margin: 0 auto;
}

/* Page Header */
.page-title-area {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 22px;
}

.page-title {
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 4px;
}

.page-subtitle {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 0;
}

.btn-primary-outline {
  display: inline-flex;
  align-items: center;
  background: #fff;
  border: 1.5px solid #0060d2;
  color: #0060d2;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.15s ease;
}

.btn-primary-outline:hover {
  background: #eff6ff;
}

/* Filter Bar */
.filter-bar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  background: #fff;
  padding: 16px 20px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.filter-left {
  display: flex;
  align-items: flex-end;
  gap: 14px;
  flex-wrap: wrap;
}

.week-nav-group {
  display: flex;
  align-items: center;
  gap: 6px;
}

.nav-arrow-btn {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s ease;
}

.nav-arrow-btn:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.btn-today {
  height: 36px;
  padding: 0 14px;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-today:hover {
  background: #e2e8f0;
  color: #0060d2;
}

.filter-group label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 5px;
}

.form-input {
  height: 36px;
  padding: 6px 12px;
  border: 1px solid #d8dee4;
  border-radius: 8px;
  font-size: 13.5px;
  color: #1e293b;
  outline: none;
  transition: border-color 0.15s ease;
}

.form-input:focus {
  border-color: #0060d2;
}

.form-select {
  min-width: 170px;
  cursor: pointer;
}

.filter-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
}

.filter-right label {
  font-size: 12px;
  font-weight: 600;
  color: #475569;
}

.view-switcher {
  display: flex;
  background: #f1f5f9;
  padding: 3px;
  border-radius: 8px;
  gap: 2px;
}

.view-btn {
  background: transparent;
  border: 0;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  color: #64748b;
  font-weight: 600;
  transition: all 0.15s ease;
  display: inline-flex;
  align-items: center;
}

.view-btn.active {
  background: #fff;
  color: #0060d2;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

/* Legend Bar */
.schedule-legend-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
  padding: 0 4px;
  flex-wrap: wrap;
  gap: 10px;
}

.legend-items {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #475569;
}

.legend-chip {
  width: 14px;
  height: 14px;
  border-radius: 4px;
  display: inline-block;
}

.legend-chip.pill-online {
  background: #dbeafe;
  border: 1px solid #93c5fd;
}

.legend-chip.pill-offline {
  background: #dcfce7;
  border: 1px solid #86efac;
}

.legend-chip.status-da_hoc {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
}

.total-sessions-badge {
  font-size: 13px;
  color: #64748b;
}

.total-sessions-badge strong {
  color: #0060d2;
}

/* Loading & Empty State */
.loading-box, .empty-box {
  background: #fff;
  padding: 60px 20px;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  text-align: center;
  color: #64748b;
}

.empty-icon-wrap {
  width: 64px;
  height: 64px;
  background: #f1f5f9;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  color: #94a3b8;
  margin-bottom: 14px;
}

.empty-box h4 {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 6px;
}

/* ========================================================
   1. BẢNG THỜI KHÓA BIỂU DẠNG TUẦN (CALENDAR TABLE)
   ======================================================== */
.calendar-table-wrapper {
  background: #ffffff;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  overflow-x: auto;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.calendar-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  min-width: 980px;
}

.calendar-table th,
.calendar-table td {
  border: 1px solid #e2e8f0;
}

/* Cột giờ Header */
.time-col-header {
  width: 96px;
  background: #f8fafc;
  padding: 14px 8px;
  text-align: center;
  font-size: 13px;
  font-weight: 700;
  color: #475569;
}

/* Header các ngày trong tuần */
.day-col-header {
  background: #f8fafc;
  padding: 12px 8px;
  text-align: center;
  transition: background 0.15s ease;
}

.day-col-header.is-today {
  background: #eff6ff;
  border-bottom: 3px solid #0060d2;
}

.day-name {
  font-size: 13px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 2px;
}

.is-today .day-name {
  color: #0060d2;
}

.day-date {
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
}

.is-today .day-date {
  color: #0060d2;
}

.today-badge {
  display: inline-block;
  background: #0060d2;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 1px 7px;
  border-radius: 10px;
  margin-top: 3px;
}

/* Ô giờ bên trái */
.time-cell {
  background: #f8fafc;
  text-align: center;
  vertical-align: middle;
  height: 72px;
  width: 96px;
}

.time-text {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
}

/* Ô chứa các lớp học trong tuần */
.slot-cell {
  background: #ffffff;
  vertical-align: top;
  padding: 5px;
  height: 72px;
  transition: background 0.15s;
}

.slot-cell.today-col {
  background: #fafcff;
}

.slot-cell:hover {
  background: #f8fafc;
}

/* THẺ LỚP HỌC TRONG Ô (SESSION PILL) */
.session-pill {
  border-radius: 8px;
  padding: 7px 9px;
  margin-bottom: 4px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
}

.session-pill:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.session-pill.pill-online {
  background: #eff6ff;
  border-left: 4px solid #0284c7;
}

.session-pill.pill-offline {
  background: #f0fdf4;
  border-left: 4px solid #16a34a;
}

.session-pill.pill-status-da_hoc {
  background: #f8fafc;
  border-left: 4px solid #94a3b8;
  opacity: 0.85;
}

.pill-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4px;
  margin-bottom: 3px;
}

.pill-time {
  font-size: 11px;
  font-weight: 700;
  color: #334155;
}

.pill-badge {
  font-size: 9.5px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 4px;
}

.badge-blue {
  background: #dbeafe;
  color: #0369a1;
}

.badge-green {
  background: #dcfce7;
  color: #15803d;
}

.pill-title {
  font-size: 12px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.3;
  margin-bottom: 3px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.pill-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11px;
  color: #64748b;
  gap: 4px;
}

.pill-gv {
  font-weight: 500;
}

.status-indicator {
  font-size: 9.5px;
  font-weight: 600;
  padding: 1px 5px;
  border-radius: 4px;
}

.indicator-sap_toi {
  background: #e0f2fe;
  color: #0369a1;
}

.indicator-dang_dien_ra {
  background: #dcfce7;
  color: #166534;
  animation: pulse-green 1.5s infinite;
}

.indicator-da_hoc {
  background: #e2e8f0;
  color: #475569;
}

@keyframes pulse-green {
  0% { opacity: 1; }
  50% { opacity: 0.6; }
  100% { opacity: 1; }
}

/* ========================================================
   2. DANH SÁCH CHI TIẾT (LIST VIEW)
   ======================================================== */
.session-cards-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.session-card {
  display: flex;
  align-items: center;
  gap: 18px;
  background: #fff;
  padding: 18px 22px;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
  transition: all 0.2s ease;
}

.session-card:hover {
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
}

.session-card.accent-blue {
  border-left: 4px solid #0060d2;
}

.session-card.accent-green {
  border-left: 4px solid #16a34a;
}

.session-card.card-da_hoc {
  opacity: 0.85;
  background: #fafafa;
}

.session-date-box {
  background: #f1f5f9;
  border-radius: 12px;
  padding: 10px 8px;
  width: 76px;
  text-align: center;
  flex-shrink: 0;
}

.date-month {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  display: block;
  text-transform: uppercase;
}

.date-day {
  font-size: 24px;
  font-weight: 800;
  color: #0060d2;
  display: block;
  line-height: 1.1;
}

.date-time {
  font-size: 11px;
  color: #64748b;
  font-weight: 600;
  display: block;
  margin-top: 2px;
}

.session-info {
  flex: 1;
}

.session-tags {
  display: flex;
  gap: 6px;
  margin-bottom: 6px;
  flex-wrap: wrap;
}

.tag-pill {
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 50px;
}

.tag-type-info {
  background: #eff6ff;
  color: #075985;
}

.tag-blue {
  background: #dbeafe;
  color: #1e40af;
}

.tag-green {
  background: #dcfce7;
  color: #15803d;
}

.tag-grey {
  background: #f1f5f9;
  color: #475569;
}

.tag-online {
  background: #e0f2fe;
  color: #0369a1;
}

.tag-offline {
  background: #f0fdf4;
  color: #166534;
}

.session-name {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  margin: 4px 0;
}

.session-meta {
  display: flex;
  gap: 20px;
  font-size: 13px;
  color: #64748b;
  flex-wrap: wrap;
  margin-top: 6px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.meta-icon {
  width: 14px;
  color: #94a3b8;
}

.session-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: #0060d2;
  color: #fff;
  border: 0;
  padding: 9px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  transition: all 0.15s ease;
}

.btn-primary:hover {
  background: #0050b3;
  color: #fff;
}

.btn-outline {
  background: transparent;
  color: #475569;
  border: 1px solid #cbd5e1;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-outline:hover {
  background: #f8fafc;
  color: #0f172a;
}

/* ========================================================
   3. MODAL CHI TIẾT
   ======================================================== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
  padding: 20px;
}

.modal-card {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  animation: modal-pop 0.2s ease-out;
}

@keyframes modal-pop {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.modal-card-header {
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.btn-close-modal {
  background: transparent;
  border: 0;
  font-size: 26px;
  line-height: 1;
  color: #94a3b8;
  cursor: pointer;
}

.btn-close-modal:hover {
  color: #0f172a;
}

.modal-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
}

.modal-card-body {
  padding: 20px 24px;
}

.detail-gv-box {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #f8fafc;
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 18px;
}

.gv-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e2e8f0;
}

.detail-rows {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.detail-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 13.5px;
  padding-bottom: 10px;
  border-bottom: 1px dashed #e2e8f0;
}

.detail-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-label {
  color: #64748b;
  font-weight: 500;
}

.detail-val {
  color: #0f172a;
}

.modal-card-footer {
  padding: 16px 24px 20px;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
}

@media (max-width: 768px) {
  .session-card {
    flex-direction: column;
    align-items: flex-start;
  }
  .session-actions {
    width: 100%;
    margin-top: 12px;
  }
  .session-actions .btn-primary,
  .session-actions .btn-outline {
    width: 100%;
    justify-content: center;
  }
}

/* ========================================================
   4. FACE ID MODALS & SCANNER
   ======================================================== */
.face-warning-icon .warning-circle {
  width: 80px;
  height: 80px;
  background-color: #fef3c7;
  border-radius: 50%;
}

.face-cam-wrapper {
  width: 260px;
  height: 260px;
  border-radius: 20px;
  overflow: hidden;
  background-color: #0f172a;
  border: 3px solid #0060d2;
  box-shadow: 0 0 18px rgba(0, 96, 210, 0.3);
  transition: all 0.3s ease;
}

.face-cam-wrapper.border-success-neon {
  border-color: #22c55e !important;
  box-shadow: 0 0 25px rgba(34, 197, 94, 0.7) !important;
}

.face-cam-wrapper.border-danger-neon {
  border-color: #ef4444 !important;
  box-shadow: 0 0 25px rgba(239, 68, 68, 0.7) !important;
}

.face-cam-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scaleX(-1);
  display: block;
}

.face-cam-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  transform: scaleX(-1);
}

.cam-overlay-loading {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.85);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 5;
}

.scan-laser-line {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, transparent, #38bdf8, #22c55e, #38bdf8, transparent);
  box-shadow: 0 0 12px #38bdf8;
  animation: laserScan 2s infinite ease-in-out;
  z-index: 4;
}

@keyframes laserScan {
  0% { top: 5%; opacity: 0.3; }
  50% { top: 90%; opacity: 1; }
  100% { top: 5%; opacity: 0.3; }
}

.verify-badge-result {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 4rem;
  background: rgba(15, 23, 42, 0.65);
  z-index: 6;
  animation: badgePop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.result-success {
  color: #22c55e;
}

.result-failed {
  color: #ef4444;
}

@keyframes badgePop {
  0% { transform: scale(0.4); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
</style>
