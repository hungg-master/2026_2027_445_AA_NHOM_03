<template>
  <div class="edu-page">
    <!-- 1. HEADER -->
    <header class="edu-header">
      <div class="header-container">
        <router-link to="/giao-vien/lich-day" class="brand-logo">EduLink</router-link>
        <nav class="header-nav">
          <router-link to="/giao-vien/lich-day" class="nav-item active">Lịch dạy</router-link>
          <router-link to="/giao-vien/quan-ly-lop" class="nav-item">Quản lý lớp</router-link>
          <router-link to="/ho-so-giang-vien" class="nav-item">Hồ sơ</router-link>
        </nav>
        <div class="header-right">
          <button class="btn-logout" @click="logout">Đăng xuất</button>
        </div>
      </div>
    </header>

    <!-- 2. MAIN CONTENT -->
    <main class="main-content">
      <div class="content-container">
        <!-- Title & Filter -->
        <div class="page-title-area">
          <h1 class="page-title">Lịch Dạy Của Tôi</h1>
          <p class="page-subtitle">Quản lý các buổi dạy theo tuần / tháng</p>
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
            <label>Trạng thái</label>
            <select class="form-input" v-model="filters.tinh_trang" @change="loadData">
              <option value="">-- Tất cả --</option>
              <option value="sap_mo">Sắp mở</option>
              <option value="dang_mo">Đang mở</option>
              <option value="dang_hoc">Đang học</option>
              <option value="da_ket_thuc">Đã kết thúc</option>
              <option value="da_huy">Đã hủy</option>
            </select>
          </div>
          <div class="filter-group" style="margin-left: auto;">
            <label>Chế độ xem</label>
            <div class="view-switcher">
              <button :class="['view-btn', { active: viewMode === 'week' }]" @click="viewMode = 'week'; groupByWeek()">Tuần</button>
              <button :class="['view-btn', { active: viewMode === 'list' }]" @click="viewMode = 'list'; groupByList()">Danh sách</button>
            </div>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="loading-box">
          <i class="fa-solid fa-spinner fa-spin"></i> Đang tải dữ liệu...
        </div>

        <!-- Empty -->
        <div v-else-if="!dsLopHoc || dsLopHoc.length === 0" class="empty-box">
          <i class="fa-regular fa-calendar-xmark"></i>
          <p>Bạn chưa có buổi dạy nào trong khoảng thời gian này.</p>
          <router-link to="/giao-vien/quan-ly-lop" class="btn btn-primary">Tạo lớp mới</router-link>
        </div>

        <!-- Week View -->
        <div v-else-if="viewMode === 'week'" class="calendar-grid-wrapper">
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
                    v-for="lop in getLopInSlot(day, hour)"
                    :key="lop.id"
                    :class="['session-pill', 'pill-' + lop.hinh_thuc]"
                    @click="openLopDetail(lop)"
                  >
                    <div class="pill-time">{{ formatTime(lop.thoi_gian_bat_dau) }} - {{ formatTime(lop.thoi_gian_ket_thuc) }}</div>
                    <div class="pill-name">{{ lop.mon_hoc?.ten_mon_hoc }}</div>
                    <div class="pill-meta">
                      <i :class="lop.hinh_thuc === 'online' ? 'fa-solid fa-video' : 'fa-solid fa-location-dot'"></i>
                      {{ getSiSo(lop) }}/{{ lop.si_so_toi_da }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- List View -->
        <div v-else class="list-view">
          <div class="list-toolbar">
            <span class="result-count">{{ dsLopHoc.length }} buổi dạy</span>
          </div>
          <div class="session-cards-list">
            <div v-for="lop in dsLopHoc" :key="lop.id" class="session-card" :class="'accent-' + getAccent(lop.hinh_thuc)">
              <div class="session-date-box">
                <span class="date-month">{{ formatMonth(lop.thoi_gian_bat_dau) }}</span>
                <span class="date-day">{{ formatDay(lop.thoi_gian_bat_dau) }}</span>
                <span class="date-time">{{ formatTime(lop.thoi_gian_bat_dau) }}</span>
              </div>
              <div class="session-info">
                <div class="session-tags">
                  <span :class="['tag-pill', 'tag-type-' + lop.loai_lop]">
                    {{ lop.loai_lop === 'kem' ? 'Lớp kèm' : 'Lớp đại trà' }}
                  </span>
                  <span :class="['tag-pill', 'tag-form-' + lop.hinh_thuc]">
                    <i :class="lop.hinh_thuc === 'online' ? 'fa-solid fa-video me-1' : 'fa-solid fa-location-dot me-1'"></i>
                    {{ lop.hinh_thuc === 'online' ? 'Online' : 'Offline' }}
                  </span>
                  <span :class="['tag-pill', statusTagClass(lop.tinh_trang)]">
                    {{ statusText(lop.tinh_trang) }}
                  </span>
                </div>
                <h3 class="session-name">{{ lop.mon_hoc?.ten_mon_hoc }}</h3>
                <div class="session-meta">
                  <div class="meta-item">
                    <i :class="lop.hinh_thuc === 'online' ? 'fa-solid fa-link meta-icon' : 'fa-solid fa-location-dot meta-icon'"></i>
                    <span>{{ lop.hinh_thuc === 'online' ? 'Online' : (lop.phong_hoc?.so_phong + ' - ' + lop.phong_hoc?.dia_chi) }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="fa-solid fa-users meta-icon"></i>
                    <span>Sĩ số: {{ getSiSo(lop) }}/{{ lop.si_so_toi_da }}</span>
                  </div>
                </div>
              </div>
              <div class="session-actions">
                <router-link :to="`/giao-vien/lop-hoc/${lop.id}`" class="btn btn-manage">Chi tiết</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- 3. MODAL: Chi tiết lớp + DS HV -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">{{ selectedLop?.mon_hoc?.ten_mon_hoc }}</h3>
          <button class="modal-close" @click="showModal = false">×</button>
        </div>
        <div class="modal-body" v-if="selectedLop">
          <div class="detail-row">
            <span class="detail-label">Thời gian:</span>
            <span class="detail-value">{{ formatFullDateTime(selectedLop.thoi_gian_bat_dau) }} → {{ formatTime(selectedLop.thoi_gian_ket_thuc) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Hình thức:</span>
            <span class="detail-value">{{ selectedLop.hinh_thuc === 'online' ? 'Online' : 'Offline tại ' + selectedLop.phong_hoc?.so_phong }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Học phí:</span>
            <span class="detail-value">{{ formatMoney(selectedLop.hoc_phi) }} VND</span>
          </div>
          <div class="detail-row" v-if="selectedLop.hinh_thuc === 'online' && selectedLop.link_online">
            <span class="detail-label">Link:</span>
            <a :href="selectedLop.link_online" target="_blank" class="detail-value text-primary">{{ selectedLop.link_online }}</a>
          </div>
          <h4 class="student-list-title">Danh sách học viên ({{ selectedLop?.dang_ky_lops?.length || 0 }}/{{ selectedLop?.si_so_toi_da }})</h4>
          <ul class="student-list" v-if="selectedLop?.dang_ky_lops?.length">
            <li v-for="dk in selectedLop.dang_ky_lops" :key="dk.id">
              <span class="student-name">{{ dk.hoc_vien?.ho_ten }}</span>
              <span :class="['status-mini', statusMiniClass(dk.trang_thai)]">{{ statusText(dk.trang_thai) }}</span>
            </li>
          </ul>
          <p v-else class="text-muted">Chưa có học viên đăng ký.</p>
        </div>
        <div class="modal-footer">
          <router-link v-if="selectedLop" :to="`/giao-vien/lop-hoc/${selectedLop.id}`" class="btn btn-primary">Quản lý lớp</router-link>
          <button class="btn btn-outline" @click="showModal = false">Đóng</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { lichDayService } from '../../services/lichDayService'
import { lopHocService } from '../../services/lopHocService'

export default {
  name: 'LichDayGiaoVien',
  data() {
    return {
      loading: false,
      viewMode: 'week',
      filters: {
        from_date: '',
        to_date: '',
        tinh_trang: '',
      },
      dsLopHoc: [],
      selectedLop: null,
      showModal: false,
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
    /** Set khoảng ngày mặc định: tuần hiện tại */
    setDefaultFilters() {
      const now = new Date()
      const monday = new Date(now)
      monday.setDate(now.getDate() - (now.getDay() === 0 ? 6 : now.getDay() - 1))
      const sunday = new Date(monday)
      sunday.setDate(monday.getDate() + 6)
      this.filters.from_date = this.formatDateInput(monday)
      this.filters.to_date = this.formatDateInput(sunday)
    },

    /** Build 7 ngày trong tuần */
    buildWeekDays() {
      if (!this.filters.from_date) return
      const start = new Date(this.filters.from_date)
      const days = []
      const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
      const today = new Date()
      today.setHours(0,0,0,0)
      for (let i = 0; i < 7; i++) {
        const d = new Date(start)
        d.setDate(start.getDate() + i)
        days.push({
          key: dayNames[d.getDay()],
          date: d.getDate().toString().padStart(2, '0'),
          fullDate: d,
          isToday: d.getTime() === today.getTime(),
        })
      }
      this.weekDays = days
    },

    /** Load dữ liệu từ API */
    async loadData() {
      this.loading = true
      try {
        const res = await lichDayService.getLichDay(this.filters)
        if (res.status) {
          this.dsLopHoc = res.data || []
          if (this.viewMode === 'week') this.groupByWeek()
        } else {
          this.dsLopHoc = []
          alert(res.message || 'Có lỗi xảy ra.')
        }
      } catch (e) {
        this.dsLopHoc = []
        alert(e.message || 'Có lỗi xảy ra khi tải dữ liệu.')
      } finally {
        this.loading = false
        this.buildWeekDays()
      }
    },

    groupByWeek() {},
    groupByList() {},

    /** Lấy các lớp diễn ra trong ô (ngày + khung giờ) */
    getLopInSlot(day, hour) {
      if (!this.dsLopHoc) return []
      const hourInt = parseInt(hour.split(':')[0])
      return this.dsLopHoc.filter(lop => {
        const start = new Date(lop.thoi_gian_bat_dau)
        const end = new Date(lop.thoi_gian_ket_thuc)
        return start.getDate() === day.fullDate.getDate()
          && start.getMonth() === day.fullDate.getMonth()
          && start.getFullYear() === day.fullDate.getFullYear()
          && start.getHours() === hourInt
      })
    },

    /** Mở modal chi tiết */
    async openLopDetail(lop) {
      try {
        const res = await lopHocService.getChiTietLop(lop.id)
        if (res.status) {
          this.selectedLop = res.data
          this.showModal = true
        } else {
          alert(res.message || 'Lỗi tải chi tiết.')
        }
      } catch (e) {
        alert(e.message || 'Lỗi tải chi tiết.')
      }
    },

    /** Helpers format */
    formatTime(iso) {
      if (!iso) return ''
      return new Date(iso).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
    },
    formatFullDateTime(iso) {
      if (!iso) return ''
      return new Date(iso).toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' })
    },
    formatDay(iso) {
      return new Date(iso).getDate().toString().padStart(2, '0')
    },
    formatMonth(iso) {
      const m = new Date(iso).getMonth() + 1
      const months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']
      return months[m-1]
    },
    formatDateInput(d) {
      return d.toISOString().split('T')[0]
    },
    formatMoney(v) {
      return new Intl.NumberFormat('vi-VN').format(v || 0)
    },
    getSiSo(lop) {
      return lop.so_hoc_vien_hien_tai ?? 0
    },
    getAccent(hinhThuc) {
      return hinhThuc === 'online' ? 'blue' : 'orange'
    },
    statusText(t) {
      return {
        sap_mo: 'Sắp mở',
        dang_mo: 'Đang mở',
        dang_hoc: 'Đang học',
        da_ket_thuc: 'Đã kết thúc',
        da_huy: 'Đã hủy',
        cho_thanh_toan: 'Chờ thanh toán',
        da_thanh_toan: 'Đã thanh toán',
        da_xac_nhan: 'Đã duyệt',
      }[t] || t
    },
    statusTagClass(t) {
      return {
        sap_mo: 'tag-blue',
        dang_mo: 'tag-green',
        dang_hoc: 'tag-purple',
        da_ket_thuc: 'tag-grey',
        da_huy: 'tag-red',
      }[t] || 'tag-grey'
    },
    statusMiniClass(t) {
      return {
        cho_thanh_toan: 'mini-warning',
        da_thanh_toan: 'mini-info',
        da_xac_nhan: 'mini-success',
        da_huy: 'mini-danger',
      }[t] || 'mini-default'
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
.empty-box i, .empty-box .fa { font-size: 36px; margin-bottom: 12px; color: #cbd5e1; }

.calendar-grid-wrapper { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; }
.calendar-grid { display: grid; grid-template-columns: 90px repeat(7, 1fr); }
.grid-header .time-col-header, .grid-header .day-col-header { background: #f8fafc; padding: 10px 6px; font-size: 12px; font-weight: 700; color: #475569; text-align: center; border-bottom: 1px solid #e2e8f0; }
.grid-header .day-col-header.is-today { color: #0060d2; }
.grid-body .time-cell { background: #fff; padding: 6px; font-size: 11px; color: #64748b; text-align: center; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #f1f5f9; min-height: 64px; }
.grid-body .slot-cell { background: #fff; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; min-height: 64px; padding: 3px; }
.session-pill { border-radius: 6px; padding: 4px 6px; margin-bottom: 3px; cursor: pointer; transition: transform 0.15s; }
.session-pill:hover { transform: translateY(-1px); }
.session-pill.pill-online { background: #dbeafe; border-left: 3px solid #0060d2; }
.session-pill.pill-offline { background: #dcfce7; border-left: 3px solid #16a34a; }
.pill-time { font-size: 10px; color: #475569; font-weight: 600; }
.pill-name { font-size: 11px; color: #0f172a; font-weight: 700; line-height: 1.2; margin: 1px 0; }
.pill-meta { font-size: 10px; color: #64748b; display: flex; align-items: center; gap: 3px; }

.session-cards-list { display: flex; flex-direction: column; gap: 14px; }
.session-card { display: flex; align-items: center; gap: 18px; background: #fff; padding: 18px 22px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
.session-card.accent-blue { border-left: 4px solid #0060d2; }
.session-card.accent-orange { border-left: 4px solid #ea580c; }
.session-date-box { background: #f1f5f9; border-radius: 10px; padding: 10px 8px; width: 76px; text-align: center; }
.date-month { font-size: 11px; font-weight: 700; color: #64748b; }
.date-day { font-size: 22px; font-weight: 800; color: #0060d2; }
.date-time { font-size: 11px; color: #64748b; }
.session-info { flex: 1; }
.session-tags { display: flex; gap: 6px; margin-bottom: 6px; flex-wrap: wrap; }
.tag-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 50px; }
.tag-type-kem { background: #f3e8ff; color: #7e22ce; }
.tag-type-dai_tra { background: #e0f2fe; color: #075985; }
.tag-form-online { background: #dbeafe; color: #1e40af; }
.tag-form-offline { background: #dcfce7; color: #166534; }
.tag-green { background: #dcfce7; color: #15803d; }
.tag-blue { background: #dbeafe; color: #1e40af; }
.tag-purple { background: #f3e8ff; color: #7e22ce; }
.tag-grey { background: #f1f5f9; color: #475569; }
.tag-red { background: #fee2e2; color: #b91c1c; }
.session-name { font-size: 16px; font-weight: 700; color: #0f172a; margin: 4px 0; }
.session-meta { display: flex; flex-direction: column; gap: 3px; font-size: 13px; color: #64748b; }
.meta-icon { width: 14px; }
.session-actions .btn-manage { background: #0060d2; color: #fff; border: 0; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; }
.list-toolbar { margin-bottom: 12px; color: #64748b; font-size: 13px; }
.result-count { font-weight: 600; }

.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.modal-content { background: #fff; border-radius: 16px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid #e2e8f0; }
.modal-title { font-size: 18px; font-weight: 700; }
.modal-close { background: transparent; border: 0; font-size: 26px; cursor: pointer; color: #64748b; }
.modal-body { padding: 22px; }
.detail-row { display: flex; gap: 12px; padding: 8px 0; font-size: 14px; }
.detail-label { width: 100px; color: #64748b; font-weight: 500; }
.detail-value { color: #0f172a; }
.student-list-title { font-size: 15px; font-weight: 700; margin: 18px 0 10px; color: #0f172a; }
.student-list { list-style: none; padding: 0; }
.student-list li { display: flex; justify-content: space-between; padding: 8px 12px; background: #f8fafc; border-radius: 6px; margin-bottom: 6px; font-size: 13.5px; }
.status-mini { font-size: 11px; padding: 2px 8px; border-radius: 50px; font-weight: 600; }
.mini-warning { background: #fef3c7; color: #92400e; }
.mini-info { background: #dbeafe; color: #1e40af; }
.mini-success { background: #dcfce7; color: #15803d; }
.mini-danger { background: #fee2e2; color: #b91c1c; }
.mini-default { background: #f1f5f9; color: #475569; }
.modal-footer { padding: 16px 22px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; }
.btn-primary { background: #0060d2; color: #fff; border: 0; padding: 9px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
.btn-outline { background: transparent; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 8px; cursor: pointer; }

@media (max-width: 768px) {
  .calendar-grid { grid-template-columns: 60px repeat(7, 1fr); font-size: 10px; }
  .session-card { flex-direction: column; align-items: flex-start; }
}
</style>
