<template>
  <div class="edu-page">
    <!-- HEADER -->
    <header class="edu-header">
      <div class="header-container">
        <router-link to="/hoc-vien/lop-cua-toi" class="brand-logo">EduLink</router-link>
        <nav class="header-nav">
          <router-link to="/hoc-vien/lich-hoc" class="nav-item">Lịch học</router-link>
          <router-link to="/hoc-vien/lop-cua-toi" class="nav-item active">Lớp của tôi</router-link>
          <router-link to="/client/danh-sach-lop" class="nav-item">Khám phá lớp</router-link>
        </nav>
        <div class="header-right">
          <button class="btn-logout" @click="logout">Đăng xuất</button>
        </div>
      </div>
    </header>

    <main class="main-content">
      <div class="content-container">
        <div class="page-title-area">
          <h1 class="page-title">Lớp Của Tôi</h1>
          <p class="page-subtitle">Quản lý các lớp học bạn đã đăng ký</p>
        </div>

        <!-- Tabs -->
        <div class="tabs">
          <button :class="['tab-btn', { active: activeTab === 'all' }]" @click="filterTab('all')">
            Tất cả ({{ dsLop.length }})
          </button>
          <button :class="['tab-btn', { active: activeTab === 'active' }]" @click="filterTab('active')">
            Đang học ({{ countByStatus(['da_xac_nhan']) }})
          </button>
          <button :class="['tab-btn', { active: activeTab === 'pending' }]" @click="filterTab('pending')">
            Chờ duyệt ({{ countByStatus(['cho_thanh_toan', 'da_thanh_toan']) }})
          </button>
          <button :class="['tab-btn', { active: activeTab === 'cancelled' }]" @click="filterTab('cancelled')">
            Đã hủy ({{ countByStatus(['da_huy']) }})
          </button>
        </div>

        <!-- Cards -->
        <div v-if="loading" class="loading-box"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>
        <div v-else-if="!filteredLop || filteredLop.length === 0" class="empty-box">
          <i class="fa-solid fa-graduation-cap"></i>
          <p>Bạn chưa đăng ký lớp nào.</p>
          <router-link to="/client/danh-sach-lop" class="btn btn-primary">Khám phá lớp học</router-link>
        </div>
        <div v-else class="lop-grid">
          <div v-for="dk in filteredLop" :key="dk.id" class="lop-card">
            <div class="card-header-strip" :class="stripColor(dk.trang_thai)"></div>
            <div class="card-body">
              <div class="subject-row">
                <span class="subject-pill">{{ dk.lop_hoc?.mon_hoc?.ten_mon_hoc || 'Môn học' }}</span>
                <span :class="['status-pill', statusClass(dk.trang_thai)]">{{ statusText(dk.trang_thai) }}</span>
              </div>
              <h3 class="teacher-name">
                <i class="fa-solid fa-user-tie me-2 text-secondary"></i>
                {{ dk.lop_hoc?.giao_vien?.ho_ten || 'Đang cập nhật' }}
              </h3>
              <div class="info-list">
                <div class="info-item">
                  <i class="fa-regular fa-calendar"></i>
                  <span>{{ formatDate(dk.lop_hoc?.thoi_gian_bat_dau) }} → {{ formatDate(dk.lop_hoc?.thoi_gian_ket_thuc) }}</span>
                </div>
                <div class="info-item">
                  <i :class="dk.lop_hoc?.hinh_thuc === 'online' ? 'fa-solid fa-video' : 'fa-solid fa-location-dot'"></i>
                  <span>
                    {{ dk.lop_hoc?.hinh_thuc === 'online'
                        ? 'Lớp Online (có link meeting)'
                        : (dk.lop_hoc?.phong_hoc?.so_phong + ' - ' + dk.lop_hoc?.phong_hoc?.dia_chi) }}
                  </span>
                </div>
                <div class="info-item">
                  <i class="fa-solid fa-money-bill"></i>
                  <span>{{ formatMoney(dk.lop_hoc?.hoc_phi) }} VND</span>
                </div>
              </div>
              <div class="card-actions">
                <a v-if="dk.lop_hoc?.link_online && dk.trang_thai === 'da_xac_nhan'" :href="dk.lop_hoc.link_online" target="_blank" class="btn btn-primary">Vào lớp</a>
                <button v-if="dk.trang_thai !== 'da_huy' && dk.trang_thai !== 'da_xac_nhan'" class="btn btn-outline" disabled>Chờ duyệt</button>
                <button v-if="dk.trang_thai !== 'da_huy'" class="btn btn-danger-outline" @click="huyDangKy(dk)">Hủy đăng ký</button>
                <span v-if="dk.trang_thai === 'da_huy'" class="text-muted small">Đã hủy lúc {{ formatDate(dk.ngay_dang_ky) }}</span>
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
  name: 'LopCuaToi',
  data() {
    return {
      loading: false,
      dsLop: [],
      filteredLop: [],
      activeTab: 'all',
    }
  },
  mounted() { this.loadData() },
  methods: {
    async loadData() {
      this.loading = true
      try {
        const res = await lichHocService.getLopHocCuaToi()
        if (res.status) {
          this.dsLop = res.data || []
          this.filterTab(this.activeTab)
        }
      } catch (e) {
        alert(e.message || 'Lỗi tải dữ liệu.')
      } finally { this.loading = false }
    },

    filterTab(tab) {
      this.activeTab = tab
      if (tab === 'all') this.filteredLop = this.dsLop
      else if (tab === 'active') this.filteredLop = this.dsLop.filter(d => d.trang_thai === 'da_xac_nhan')
      else if (tab === 'pending') this.filteredLop = this.dsLop.filter(d => ['cho_thanh_toan','da_thanh_toan'].includes(d.trang_thai))
      else if (tab === 'cancelled') this.filteredLop = this.dsLop.filter(d => d.trang_thai === 'da_huy')
    },

    countByStatus(statusArr) {
      return this.dsLop.filter(d => statusArr.includes(d.trang_thai)).length
    },

    async huyDangKy(dk) {
      if (!confirm('Xác nhận HỦY đăng ký lớp này?')) return
      try {
        const res = await lichHocService.huyDangKy(dk.id)
        if (res.status) {
          alert(res.message)
          this.loadData()
        } else { alert(res.message || 'Lỗi.') }
      } catch (e) { alert(e.message || 'Lỗi.') }
    },

    // Helpers
    formatDate(iso) {
      if (!iso) return ''
      return new Date(iso).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    },
    formatMoney(v) { return new Intl.NumberFormat('vi-VN').format(v || 0) },
    statusText(t) {
      return { cho_thanh_toan:'Chờ thanh toán', da_thanh_toan:'Đã thanh toán', da_xac_nhan:'Đã duyệt', da_huy:'Đã hủy' }[t] || t
    },
    statusClass(t) {
      return { cho_thanh_toan:'st-warning', da_thanh_toan:'st-info', da_xac_nhan:'st-success', da_huy:'st-danger' }[t] || 'st-default'
    },
    stripColor(t) {
      return { da_xac_nhan:'strip-green', cho_thanh_toan:'strip-blue', da_thanh_toan:'strip-blue', da_huy:'strip-grey' }[t] || 'strip-grey'
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
.edu-header { background: #fff; border-bottom: 1px solid #e2e8f0; }
.header-container { max-width: 1260px; margin: 0 auto; padding: 0 28px; height: 64px; display: flex; align-items: center; gap: 32px; }
.brand-logo { font-size: 22px; font-weight: 800; color: #0060d2; text-decoration: none; }
.header-nav { display: flex; gap: 28px; flex: 1; }
.header-nav .nav-item { color: #475569; text-decoration: none; font-size: 14.5px; font-weight: 500; padding: 20px 0; border-bottom: 2.5px solid transparent; }
.header-nav .nav-item.active { color: #0060d2; border-bottom-color: #0060d2; font-weight: 600; }
.header-right .btn-logout { background: transparent; border: 1px solid #e2e8f0; padding: 7px 16px; border-radius: 8px; cursor: pointer; font-size: 13.5px; }

.main-content { flex: 1; padding: 30px 24px 60px; }
.content-container { max-width: 1260px; margin: 0 auto; }
.page-title-area { margin-bottom: 18px; }
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.page-subtitle { color: #64748b; font-size: 14px; }

.tabs { display: flex; gap: 6px; border-bottom: 1px solid #e2e8f0; margin-bottom: 22px; }
.tab-btn { background: transparent; border: 0; padding: 10px 18px; font-size: 14px; color: #475569; cursor: pointer; border-bottom: 2.5px solid transparent; font-weight: 500; }
.tab-btn.active { color: #0060d2; border-bottom-color: #0060d2; font-weight: 600; }

.loading-box, .empty-box { background: #fff; padding: 80px 20px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; color: #64748b; }
.empty-box i { font-size: 48px; margin-bottom: 16px; color: #cbd5e1; display: block; }

.lop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px; }
.lop-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.15s, box-shadow 0.15s; }
.lop-card:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(0,0,0,0.06); }
.card-header-strip { height: 4px; }
.strip-green { background: linear-gradient(90deg, #10b981, #6ee7b7); }
.strip-blue { background: linear-gradient(90deg, #0060d2, #93c5fd); }
.strip-grey { background: #cbd5e1; }
.card-body { padding: 18px 20px; }
.subject-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
.subject-pill { background: #e0f2fe; color: #075985; font-size: 11.5px; font-weight: 700; padding: 4px 11px; border-radius: 50px; }
.status-pill { font-size: 11.5px; font-weight: 700; padding: 4px 11px; border-radius: 50px; }
.st-success { background: #dcfce7; color: #15803d; }
.st-warning { background: #fef3c7; color: #92400e; }
.st-info { background: #dbeafe; color: #1e40af; }
.st-danger { background: #fee2e2; color: #b91c1c; }
.st-default { background: #f1f5f9; color: #475569; }

.teacher-name { font-size: 16px; font-weight: 700; color: #0f172a; margin: 6px 0 12px; }
.info-list { display: flex; flex-direction: column; gap: 6px; padding: 10px 0; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; margin-bottom: 12px; }
.info-item { display: flex; align-items: center; gap: 9px; font-size: 13px; color: #475569; }
.info-item i { width: 14px; text-align: center; color: #94a3b8; }

.card-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.btn { padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: 0; text-decoration: none; display: inline-block; }
.btn-primary { background: #0060d2; color: #fff; }
.btn-outline { background: transparent; color: #475569; border: 1px solid #cbd5e1; cursor: not-allowed; opacity: 0.7; }
.btn-danger-outline { background: transparent; color: #ef4444; border: 1px solid #fca5a5; }
.btn-danger-outline:hover { background: #fef2f2; }
.text-muted { color: #94a3b8; }
.small { font-size: 11.5px; }
</style>
