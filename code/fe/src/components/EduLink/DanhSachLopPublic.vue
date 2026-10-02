<template>
  <div class="edu-page">
    <!-- HEADER -->
    <header class="edu-header">
      <div class="header-container">
        <router-link to="/client/danh-sach-lop" class="brand-logo">EduLink</router-link>
        <nav class="header-nav">
          <router-link to="/client/danh-sach-lop" class="nav-item active">Khám phá lớp</router-link>
          <router-link to="/hoc-vien/lich-hoc" class="nav-item">Lịch học</router-link>
          <router-link to="/hoc-vien/lop-cua-toi" class="nav-item">Lớp của tôi</router-link>
        </nav>
        <div class="header-right">
          <router-link v-if="!isLogged" to="/dang-ky" class="btn-login">Đăng nhập</router-link>
          <button v-else class="btn-logout" @click="logout">Đăng xuất</button>
        </div>
      </div>
    </header>

    <main class="main-content">
      <div class="content-container">
        <div class="page-title-area">
          <h1 class="page-title">Khám Phá Lớp Học</h1>
          <p class="page-subtitle">Tìm và đăng ký lớp học phù hợp với bạn</p>
        </div>

        <!-- Filter -->
        <div class="filter-bar">
          <div class="filter-group">
            <label>Môn học</label>
            <select class="form-input" v-model="filters.id_mon_hoc" @change="loadData">
              <option value="">Tất cả</option>
              <option v-for="mh in dsMonHoc" :key="mh.id" :value="mh.id">{{ mh.ten_mon_hoc }}</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Hình thức</label>
            <select class="form-input" v-model="filters.hinh_thuc" @change="loadData">
              <option value="">Tất cả</option>
              <option value="online">Online</option>
              <option value="offline">Offline</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Loại lớp</label>
            <select class="form-input" v-model="filters.loai_lop" @change="loadData">
              <option value="">Tất cả</option>
              <option value="dai_tra">Đại trà</option>
              <option value="kem">Kèm</option>
            </select>
          </div>
          <input type="text" class="form-input" placeholder="Tìm kiếm..." v-model="filters.keyword" @input="debouncedSearch" style="min-width:180px" />
        </div>

        <!-- Loading -->
        <div v-if="loading" class="loading-box"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>
        <div v-else-if="!dsLop || dsLop.length === 0" class="empty-box">
          <i class="fa-solid fa-search"></i>
          <p>Không có lớp nào phù hợp với bộ lọc hiện tại.</p>
        </div>
        <div v-else class="lop-grid">
          <div v-for="lop in dsLop" :key="lop.id" class="lop-card">
            <div class="teacher-row">
              <img :src="lop.giao_vien?.hinh_anh || 'https://i.pravatar.cc/80'" :alt="lop.giao_vien?.ho_ten" class="teacher-avatar" />
              <div>
                <div class="teacher-name">{{ lop.giao_vien?.ho_ten || 'Giáo viên' }}</div>
                <div class="teacher-sub">{{ lop.giao_vien?.chuc_danh }}</div>
              </div>
            </div>
            <div class="subject-name">{{ lop.mon_hoc?.ten_mon_hoc }}</div>
            <div class="lop-tags">
              <span :class="['tag-pill', lop.hinh_thuc === 'online' ? 'tag-info' : 'tag-green']">
                <i :class="lop.hinh_thuc === 'online' ? 'fa-solid fa-video me-1' : 'fa-solid fa-location-dot me-1'"></i>
                {{ lop.hinh_thuc === 'online' ? 'Online' : 'Offline' }}
              </span>
              <span :class="['tag-pill', lop.loai_lop === 'kem' ? 'tag-purple' : 'tag-blue']">
                {{ lop.loai_lop === 'kem' ? `Kèm (1-${lop.si_so_toi_da})` : 'Đại trà' }}
              </span>
            </div>
            <div class="info-list">
              <div class="info-item">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ formatDate(lop.thoi_gian_bat_dau) }}</span>
              </div>
              <div class="info-item">
                <i class="fa-solid fa-money-bill"></i>
                <span>{{ formatMoney(lop.hoc_phi) }} VND</span>
              </div>
              <div class="info-item">
                <i class="fa-solid fa-users"></i>
                <span>Sĩ số: {{ lop.so_hoc_vien_hien_tai || 0 }}/{{ lop.si_so_toi_da }}</span>
              </div>
            </div>
            <button class="btn-register" @click="dangKy(lop)" :disabled="registeringId === lop.id">
              <i v-if="registeringId === lop.id" class="fa-solid fa-spinner fa-spin me-1"></i>
              Đăng ký ngay
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { lopHocService } from '../../services/lopHocService'
import { lichHocService } from '../../services/lichHocService'

export default {
  name: 'DanhSachLopPublic',
  data() {
    return {
      loading: false,
      registeringId: null,
      dsLop: [],
      dsMonHoc: [],
      filters: { id_mon_hoc: '', hinh_thuc: '', loai_lop: '', keyword: '' },
      isLogged: !!localStorage.getItem('edulink_token'),
      searchTimer: null,
    }
  },
  mounted() {
    this.loadData()
    this.loadMonHoc()
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        const res = await lopHocService.getPublicLop(this.filters)
        if (res.status) {
          this.dsLop = res.data?.data || res.data || []
        }
      } catch (e) {
        alert(e.message || 'Lỗi.')
      } finally { this.loading = false }
    },
    async loadMonHoc() {
      try {
        const res = await lopHocService.getMonHoc()
        if (res.status) this.dsMonHoc = res.data || []
      } catch (e) {}
    },
    debouncedSearch() {
      clearTimeout(this.searchTimer)
      this.searchTimer = setTimeout(() => this.loadData(), 400)
    },
    async dangKy(lop) {
      if (!this.isLogged) {
        if (confirm('Bạn cần đăng nhập để đăng ký. Chuyển đến trang đăng nhập?')) {
          this.$router.push('/dang-ky')
        }
        return
      }
      // Gọi API đăng ký - Sanctum middleware sẽ tự kiểm tra role HocVien
      this.registeringId = lop.id
      try {
        const res = await lichHocService.dangKyLop(lop.id)
        if (res.status) {
          alert(res.message)
          this.loadData()
        } else {
          alert(res.message || 'Lỗi.')
        }
      } catch (e) {
        alert(e.message || 'Lỗi.')
      } finally {
        this.registeringId = null
      }
    },
    formatDate(iso) {
      if (!iso) return ''
      return new Date(iso).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
    },
    formatMoney(v) { return new Intl.NumberFormat('vi-VN').format(v || 0) },
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
.header-right .btn-logout, .header-right .btn-login { background: transparent; border: 1px solid #e2e8f0; padding: 7px 16px; border-radius: 8px; cursor: pointer; font-size: 13.5px; color: #475569; text-decoration: none; }

.main-content { flex: 1; padding: 30px 24px 60px; }
.content-container { max-width: 1260px; margin: 0 auto; }
.page-title-area { margin-bottom: 20px; }
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.page-subtitle { color: #64748b; font-size: 14px; }

.filter-bar { display: flex; align-items: end; gap: 12px; background: #fff; padding: 14px 18px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 22px; flex-wrap: wrap; }
.filter-group label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
.form-input { padding: 8px 12px; border: 1px solid #d8dee4; border-radius: 8px; font-size: 13.5px; min-width: 140px; }

.loading-box, .empty-box { background: #fff; padding: 80px 20px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; color: #64748b; }
.empty-box i { font-size: 48px; margin-bottom: 16px; color: #cbd5e1; display: block; }

.lop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 18px; }
.lop-card { background: #fff; border-radius: 14px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.15s, box-shadow 0.15s; }
.lop-card:hover { transform: translateY(-3px); box-shadow: 0 10px 26px rgba(0,0,0,0.07); }
.teacher-row { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.teacher-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
.teacher-name { font-size: 14.5px; font-weight: 700; color: #0f172a; }
.teacher-sub { font-size: 12px; color: #64748b; }
.subject-name { font-size: 16px; font-weight: 700; color: #0060d2; margin-bottom: 10px; }

.lop-tags { display: flex; gap: 6px; margin-bottom: 12px; flex-wrap: wrap; }
.tag-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 50px; }
.tag-blue { background: #dbeafe; color: #1e40af; }
.tag-green { background: #dcfce7; color: #15803d; }
.tag-info { background: #e0f2fe; color: #075985; }
.tag-purple { background: #f3e8ff; color: #7e22ce; }

.info-list { display: flex; flex-direction: column; gap: 5px; padding: 10px 0; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; margin-bottom: 12px; font-size: 13px; color: #475569; }
.info-item { display: flex; align-items: center; gap: 9px; }
.info-item i { width: 14px; color: #94a3b8; }

.btn-register { width: 100%; background: #0060d2; color: #fff; border: 0; padding: 10px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-register:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-register:hover:not(:disabled) { background: #004fb0; }
</style>
