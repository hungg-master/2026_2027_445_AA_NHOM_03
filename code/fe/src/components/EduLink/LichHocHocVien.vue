<template>
  <div class="edu-page">
    <!-- HEADER -->
    <header class="edu-header">
      <div class="header-container">
        <router-link to="/hoc-vien/lich-hoc" class="brand-logo">EduLink</router-link>
        <nav class="header-nav">
          <router-link to="/hoc-vien/lich-hoc" class="nav-item active">Lịch học</router-link>
          <router-link to="/hoc-vien/lop-cua-toi" class="nav-item">Lớp của tôi</router-link>
          <router-link to="/hoc-vien" class="nav-item">Hồ sơ</router-link>
        </nav>
        <div class="header-right">
          <button class="btn-logout" @click="logout">Đăng xuất</button>
        </div>
      </div>
    </header>

    <main class="main-content">
      <div class="content-container">
        <div class="page-title-area">
          <h1 class="page-title">Lịch Học Của Tôi</h1>
          <p class="page-subtitle">Theo dõi lịch học các lớp bạn đã đăng ký</p>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
          <div class="filter-group">
            <label>Từ ngày</label>
            <input type="date" class="form-input" v-model="filters.from_date" @change="loadData" />
          </div>
          <div class="filter-group">
            <label>Đến ngày</label>
            <input type="date" class="form-input" v-model="filters.to_date" @change="loadData" />
          </div>
          <div class="filter-group">
            <label>Trạng thái buổi</label>
            <select class="form-input" v-model="filters.trang_thai_buoi" @change="filterByStatus">
              <option value="">-- Tất cả --</option>
              <option value="sap_toi">Sắp tới</option>
              <option value="dang_dien_ra">Đang diễn ra</option>
              <option value="da_hoc">Đã học</option>
            </select>
          </div>
          <div class="filter-group" style="margin-left: auto;">
            <label>Chế độ xem</label>
            <div class="view-switcher">
              <button :class="['view-btn', { active: viewMode === 'week' }]" @click="setView('week')">Tuần</button>
              <button :class="['view-btn', { active: viewMode === 'list' }]" @click="setView('list')">Danh sách</button>
            </div>
          </div>
        </div>

        <!-- Loading / Empty -->
        <div v-if="loading" class="loading-box"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>
        <div v-else-if="!filteredLichHoc || filteredLichHoc.length === 0" class="empty-box">
          <i class="fa-regular fa-calendar-xmark"></i>
          <p>Bạn chưa có buổi học nào trong khoảng thời gian này.</p>
          <router-link to="/client/lop-hoc" class="btn btn-primary">Khám phá lớp học mới</router-link>
        </div>

        <!-- List View -->
        <div v-else-if="viewMode === 'list'" class="list-view">
          <div class="session-cards-list">
            <div v-for="dk in filteredLichHoc" :key="dk.id" class="session-card" :class="'accent-' + getAccent(dk.lop_hoc?.hinh_thuc)">
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
                  <span :class="['tag-pill', dk.trang_thai === 'da_xac_nhan' ? 'tag-green' : 'tag-grey']">
                    {{ dk.trang_thai === 'da_xac_nhan' ? 'Đã duyệt' : 'Chờ duyệt' }}
                  </span>
                </div>
                <h3 class="session-name">
                  <i class="fa-solid fa-user-tie me-1 text-secondary"></i>
                  {{ dk.lop_hoc?.giao_vien?.ho_ten || 'Đang cập nhật GV' }}
                </h3>
                <div class="session-meta">
                  <div class="meta-item">
                    <i :class="dk.lop_hoc?.hinh_thuc === 'online' ? 'fa-solid fa-video meta-icon' : 'fa-solid fa-location-dot meta-icon'"></i>
                    <span>
                      {{ dk.lop_hoc?.hinh_thuc === 'online'
                        ? 'Lớp Online'
                        : (dk.lop_hoc?.phong_hoc?.so_phong || 'Chưa rõ phòng') }}
                    </span>
                  </div>
                  <div class="meta-item">
                    <i class="fa-regular fa-clock meta-icon"></i>
                    <span>{{ formatTime(dk.lop_hoc?.thoi_gian_ket_thuc) }} kết thúc</span>
                  </div>
                </div>
              </div>
              <div class="session-actions">
                <a v-if="dk.lop_hoc?.link_online && dk.trang_thai_buoi !== 'da_hoc'" :href="dk.lop_hoc.link_online" target="_blank" class="btn btn-primary">
                  Vào lớp
                </a>
                <button v-else-if="dk.trang_thai_buoi === 'da_hoc'" class="btn btn-outline" disabled>Đã kết thúc</button>
                <button v-else class="btn btn-outline" disabled>Sắp tới</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Week View -->
        <div v-else class="calendar-grid-wrapper">
          <div class="calendar-grid">
            <div class="grid-header">
              <div class="time-col-header">Khung giờ</div>
              <div v-for="day in weekDays" :key="day.key" class="day-col-header" :class="{ 'is-today': day.isToday }">
                <div class="day-name">{{ day.name }}</div>
                <div class="day-date">{{ day.date }}</div>
              </div>
            </div>
            <div class="grid-body">
              <div v-for="hour in timeSlots" :key="hour" class="grid-row">
                <div class="time-cell">{{ hour }}</div>
                <div v-for="day in weekDays" :key="day.key + '_' + hour" class="slot-cell">
                  <div
                    v-for="dk in getLopInSlot(day, hour)"
                    :key="dk.id"
                    class="session-pill"
                    :class="'pill-' + (dk.lop_hoc?.hinh_thuc || 'offline')"
                    @click="goToClass(dk)"
                  >
                    <div class="pill-time">{{ formatTime(dk.lop_hoc?.thoi_gian_bat_dau) }}</div>
                    <div class="pill-name">{{ dk.lop_hoc?.mon_hoc?.ten_mon_hoc }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { lichHocService } from '../../services/lichHocService'

export default {
  name: 'LichHocHocVien',
  data() {
    return {
      loading: false,
      viewMode: 'week',
      filters: { from_date: '', to_date: '', trang_thai_buoi: '' },
      dsLichHoc: [],
      filteredLichHoc: [],
      weekDays: [],
      timeSlots: ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00'],
    }
  },
  mounted() {
    this.setDefaultFilters()
    this.buildWeekDays()
    this.loadData()
  },
  methods: {
    setDefaultFilters() {
      const now = new Date()
      const monday = new Date(now)
      monday.setDate(now.getDate() - (now.getDay() === 0 ? 6 : now.getDay() - 1))
      const sunday = new Date(monday)
      sunday.setDate(monday.getDate() + 6)
      this.filters.from_date = this.formatDateInput(monday)
      this.filters.to_date = this.formatDateInput(sunday)
    },
    buildWeekDays() {
      if (!this.filters.from_date) return
      const start = new Date(this.filters.from_date)
      const days = []
      const dayNames = ['CN','T2','T3','T4','T5','T6','T7']
      const today = new Date(); today.setHours(0,0,0,0)
      for (let i = 0; i < 7; i++) {
        const d = new Date(start)
        d.setDate(start.getDate() + i)
        days.push({
          key: dayNames[d.getDay()],
          date: d.getDate().toString().padStart(2,'0'),
          fullDate: d,
          isToday: d.getTime() === today.getTime(),
        })
      }
      this.weekDays = days
    },
    async loadData() {
      this.loading = true
      try {
        const res = await lichHocService.getLichHoc({
          from_date: this.filters.from_date,
          to_date: this.filters.to_date,
        })
        if (res.status) {
          this.dsLichHoc = res.data || []
          this.filterByStatus()
        }
      } catch (e) {
        alert(e.message || 'Lỗi tải dữ liệu.')
      } finally {
        this.loading = false
        this.buildWeekDays()
      }
    },
    filterByStatus() {
      if (!this.filters.trang_thai_buoi) {
        this.filteredLichHoc = this.dsLichHoc
      } else {
        this.filteredLichHoc = this.dsLichHoc.filter(dk => dk.trang_thai_buoi === this.filters.trang_thai_buoi)
      }
    },
    setView(mode) {
      this.viewMode = mode
    },
    getLopInSlot(day, hour) {
      const hourInt = parseInt(hour.split(':')[0])
      return (this.dsLichHoc || []).filter(dk => {
        if (!dk.lop_hoc) return false
        const start = new Date(dk.lop_hoc.thoi_gian_bat_dau)
        return start.getDate() === day.fullDate.getDate()
          && start.getMonth() === day.fullDate.getMonth()
          && start.getFullYear() === day.fullDate.getFullYear()
          && start.getHours() === hourInt
      })
    },
    goToClass(dk) {
      if (dk.lop_hoc?.link_online && dk.trang_thai_buoi !== 'da_hoc') {
        window.open(dk.lop_hoc.link_online, '_blank')
      }
    },
    formatTime(iso) { if (!iso) return ''; return new Date(iso).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) },
    formatDay(iso) { if (!iso) return ''; return new Date(iso).getDate().toString().padStart(2, '0') },
    formatMonth(iso) {
      if (!iso) return ''
      const m = new Date(iso).getMonth() + 1
      const arr = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']
      return arr[m-1]
    },
    formatDateInput(d) { return d.toISOString().split('T')[0] },
    getAccent(h) { return h === 'online' ? 'blue' : 'orange' },
    statusPillClass(s) {
      return { sap_toi: 'tag-blue', dang_dien_ra: 'tag-green', da_hoc: 'tag-grey' }[s] || 'tag-grey'
    },
    statusPillText(s) {
      return { sap_toi: 'Sắp tới', dang_dien_ra: 'Đang diễn ra', da_hoc: 'Đã học' }[s] || ''
    },
    logout() {
      localStorage.removeItem('edulink_token')
      localStorage.removeItem('edulink_user')
      this.$router.push('/dang-ky')
    },
  },
}
</script>

<style scoped>
.edu-page { min-height: 100vh; background: #f8fafc; color: #1e293b; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; flex-direction: column; }
.edu-header { background: #fff; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 50; }
.header-container { max-width: 1260px; margin: 0 auto; padding: 0 28px; height: 64px; display: flex; align-items: center; gap: 32px; }
.brand-logo { font-size: 22px; font-weight: 800; color: #0060d2; text-decoration: none; }
.header-nav { display: flex; gap: 28px; flex: 1; }
.header-nav .nav-item { color: #475569; text-decoration: none; font-size: 14.5px; font-weight: 500; padding: 20px 0; border-bottom: 2.5px solid transparent; }
.header-nav .nav-item.active { color: #0060d2; border-bottom-color: #0060d2; font-weight: 600; }
.header-right .btn-logout { background: transparent; border: 1px solid #e2e8f0; padding: 7px 16px; border-radius: 8px; cursor: pointer; font-size: 13.5px; }

.main-content { flex: 1; padding: 30px 24px 60px; }
.content-container { max-width: 1260px; margin: 0 auto; }
.page-title-area { margin-bottom: 22px; }
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.page-subtitle { color: #64748b; font-size: 14px; }

.filter-bar { display: flex; align-items: end; gap: 14px; background: #fff; padding: 16px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; flex-wrap: wrap; }
.filter-group label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
.form-input { padding: 8px 12px; border: 1px solid #d8dee4; border-radius: 8px; font-size: 13.5px; min-width: 140px; }
.view-switcher { display: flex; background: #f1f5f9; padding: 3px; border-radius: 8px; }
.view-btn { background: transparent; border: 0; padding: 6px 14px; border-radius: 6px; font-size: 13px; cursor: pointer; color: #475569; }
.view-btn.active { background: #fff; color: #0060d2; box-shadow: 0 1px 3px rgba(0,0,0,0.06); font-weight: 600; }

.loading-box, .empty-box { background: #fff; padding: 60px 20px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; color: #64748b; }
.empty-box i { font-size: 36px; margin-bottom: 12px; color: #cbd5e1; display: block; }

.session-cards-list { display: flex; flex-direction: column; gap: 14px; }
.session-card { display: flex; align-items: center; gap: 18px; background: #fff; padding: 18px 22px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
.session-card.accent-blue { border-left: 4px solid #0060d2; }
.session-card.accent-orange { border-left: 4px solid #ea580c; }
.session-date-box { background: #f1f5f9; border-radius: 10px; padding: 10px 8px; width: 76px; text-align: center; flex-shrink: 0; }
.date-month { font-size: 11px; font-weight: 700; color: #64748b; }
.date-day { font-size: 22px; font-weight: 800; color: #0060d2; }
.date-time { font-size: 11px; color: #64748b; }
.session-info { flex: 1; }
.session-tags { display: flex; gap: 6px; margin-bottom: 6px; flex-wrap: wrap; }
.tag-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 50px; }
.tag-type-info { background: #eff6ff; color: #075985; }
.tag-blue { background: #dbeafe; color: #1e40af; }
.tag-green { background: #dcfce7; color: #15803d; }
.tag-grey { background: #f1f5f9; color: #475569; }
.session-name { font-size: 16px; font-weight: 700; color: #0f172a; margin: 4px 0; }
.session-meta { display: flex; flex-direction: column; gap: 3px; font-size: 13px; color: #64748b; }
.meta-icon { width: 14px; }
.session-actions .btn-primary, .session-actions .btn-outline { background: #0060d2; color: #fff; border: 0; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; cursor: pointer; }
.session-actions .btn-outline { background: transparent; color: #475569; border: 1px solid #cbd5e1; }

.calendar-grid-wrapper { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; }
.calendar-grid { display: grid; grid-template-columns: 90px repeat(7, 1fr); }
.grid-header .time-col-header, .grid-header .day-col-header { background: #f8fafc; padding: 10px 6px; font-size: 12px; font-weight: 700; color: #475569; text-align: center; border-bottom: 1px solid #e2e8f0; }
.grid-header .day-col-header.is-today { color: #0060d2; }
.grid-body .time-cell { background: #fff; padding: 6px; font-size: 11px; color: #64748b; text-align: center; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #f1f5f9; min-height: 64px; }
.grid-body .slot-cell { background: #fff; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; min-height: 64px; padding: 3px; }
.session-pill { border-radius: 6px; padding: 4px 6px; margin-bottom: 3px; cursor: pointer; }
.session-pill.pill-online { background: #dbeafe; border-left: 3px solid #0060d2; }
.session-pill.pill-offline { background: #dcfce7; border-left: 3px solid #16a34a; }
.pill-time { font-size: 10px; color: #475569; font-weight: 600; }
.pill-name { font-size: 11px; color: #0f172a; font-weight: 700; line-height: 1.2; }

@media (max-width: 768px) {
  .calendar-grid { grid-template-columns: 60px repeat(7, 1fr); font-size: 10px; }
  .session-card { flex-direction: column; align-items: flex-start; }
}
</style>
