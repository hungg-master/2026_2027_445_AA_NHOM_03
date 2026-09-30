<template>
  <div class="edu-page">
    <!-- HEADER -->
    <header class="edu-header">
      <div class="header-container">
        <router-link to="/giao-vien/quan-ly-lop" class="brand-logo">EduLink</router-link>
        <nav class="header-nav">
          <router-link to="/giao-vien/lich-day" class="nav-item">Lịch dạy</router-link>
          <router-link to="/giao-vien/quan-ly-lop" class="nav-item active">Quản lý lớp</router-link>
          <router-link to="/ho-so-giang-vien" class="nav-item">Hồ sơ</router-link>
        </nav>
        <div class="header-right">
          <button class="btn-logout" @click="logout">Đăng xuất</button>
        </div>
      </div>
    </header>

    <main class="main-content">
      <div class="content-container">
        <div class="page-header">
          <div>
            <h1 class="page-title">Quản Lý Lớp Học</h1>
            <p class="page-subtitle">Tạo mới, chỉnh sửa, theo dõi các lớp bạn phụ trách</p>
          </div>
          <button class="btn btn-primary" @click="openCreateModal">
            <i class="fa-solid fa-plus me-1"></i> Tạo lớp mới
          </button>
        </div>

        <!-- Stats -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fa-solid fa-chalkboard"></i></div>
            <div>
              <div class="stat-value">{{ stats.tong }}</div>
              <div class="stat-label">Tổng lớp</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon icon-green"><i class="fa-solid fa-circle-play"></i></div>
            <div>
              <div class="stat-value">{{ stats.dangHoatDong }}</div>
              <div class="stat-label">Đang hoạt động</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon icon-purple"><i class="fa-solid fa-users"></i></div>
            <div>
              <div class="stat-value">{{ stats.tongHV }}</div>
              <div class="stat-label">Tổng học viên</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon icon-red"><i class="fa-solid fa-ban"></i></div>
            <div>
              <div class="stat-value">{{ stats.daHuy }}</div>
              <div class="stat-label">Đã hủy</div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="table-card">
          <table class="data-table">
            <thead>
              <tr>
                <th>Tên lớp / Môn</th>
                <th>Loại</th>
                <th>Hình thức</th>
                <th>Sĩ số</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="7" class="text-center text-muted" style="padding:30px">Đang tải...</td></tr>
              <tr v-else-if="!dsLop || dsLop.length === 0"><td colspan="7" class="text-center text-muted" style="padding:30px">Bạn chưa có lớp học nào. Hãy tạo lớp mới!</td></tr>
              <tr v-for="lop in dsLop" :key="lop.id" v-else>
                <td><strong>{{ lop.mon_hoc?.ten_mon_hoc }}</strong></td>
                <td>
                  <span :class="['tag-pill', lop.loai_lop === 'kem' ? 'tag-purple' : 'tag-blue']">
                    {{ lop.loai_lop === 'kem' ? 'Lớp kèm' : 'Đại trà' }}
                  </span>
                </td>
                <td>
                  <span :class="['tag-pill', lop.hinh_thuc === 'online' ? 'tag-info' : 'tag-green']">
                    <i :class="lop.hinh_thuc === 'online' ? 'fa-solid fa-video me-1' : 'fa-solid fa-location-dot me-1'"></i>
                    {{ lop.hinh_thuc === 'online' ? 'Online' : 'Offline' }}
                  </span>
                </td>
                <td>{{ lop.so_hoc_vien_hien_tai || 0 }}/{{ lop.si_so_toi_da }}</td>
                <td>{{ formatDate(lop.thoi_gian_bat_dau) }} → {{ formatDate(lop.thoi_gian_ket_thuc) }}</td>
                <td>
                  <span :class="['tag-pill', statusTagClass(lop.tinh_trang)]">{{ statusText(lop.tinh_trang) }}</span>
                </td>
                <td>
                  <button class="btn-icon" @click="viewDetail(lop)" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></button>
                  <button class="btn-icon" @click="openEditModal(lop)" title="Chỉnh sửa" :disabled="lop.tinh_trang === 'da_huy'"><i class="fa-solid fa-pen"></i></button>
                  <button class="btn-icon btn-icon-danger" @click="confirmHuy(lop)" title="Hủy lớp" :disabled="lop.tinh_trang === 'da_huy'"><i class="fa-solid fa-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- MODAL TẠO/SỬA LỚP -->
    <div v-if="showFormModal" class="modal-overlay" @click.self="showFormModal = false">
      <div class="modal-content large">
        <div class="modal-header">
          <h3 class="modal-title">{{ form.id ? 'Chỉnh sửa lớp học' : 'Tạo lớp học mới' }}</h3>
          <button class="modal-close" @click="showFormModal = false">×</button>
        </div>
        <form @submit.prevent="saveForm" class="modal-body">
          <div class="form-grid">
            <div class="form-field">
              <label>Môn học <span class="required">*</span></label>
              <select class="form-input" v-model="form.id_mon_hoc" required>
                <option value="">-- Chọn môn --</option>
                <option v-for="mh in dsMonHoc" :key="mh.id" :value="mh.id">{{ mh.ten_mon_hoc }}</option>
              </select>
            </div>
            <div class="form-field">
              <label>Loại lớp <span class="required">*</span></label>
              <select class="form-input" v-model="form.loai_lop" required>
                <option value="dai_tra">Đại trà</option>
                <option value="kem">Kèm (1-5 người)</option>
              </select>
            </div>
            <div class="form-field">
              <label>Hình thức <span class="required">*</span></label>
              <select class="form-input" v-model="form.hinh_thuc" @change="onHinhThucChange" required>
                <option value="online">Online</option>
                <option value="offline">Offline (có phòng)</option>
              </select>
            </div>
            <div class="form-field" v-if="form.hinh_thuc === 'offline'">
              <label>Phòng học <span class="required">*</span></label>
              <select class="form-input" v-model="form.id_phong_hoc">
                <option value="">-- Chọn phòng --</option>
                <option v-for="p in dsPhongHoc" :key="p.id" :value="p.id">{{ p.so_phong }} - {{ p.dia_chi }}</option>
              </select>
            </div>
            <div class="form-field" v-if="form.hinh_thuc === 'online'">
              <label>Link online <span class="required">*</span></label>
              <input type="url" class="form-input" v-model="form.link_online" placeholder="https://meet.google.com/..." />
            </div>
            <div class="form-field">
              <label>Học phí (VND) <span class="required">*</span></label>
              <input type="number" class="form-input" v-model.number="form.hoc_phi" min="0" required />
            </div>
            <div class="form-field">
              <label>Sĩ số tối đa <span class="required">*</span></label>
              <input type="number" class="form-input" v-model.number="form.si_so_toi_da" :max="form.loai_lop === 'kem' ? 5 : 30" min="1" required />
              <small class="hint">Lớp kèm tối đa 5, đại trà tối đa 30</small>
            </div>
            <div class="form-field">
              <label>Bắt đầu <span class="required">*</span></label>
              <input type="datetime-local" class="form-input" v-model="form.thoi_gian_bat_dau" required />
            </div>
            <div class="form-field">
              <label>Kết thúc <span class="required">*</span></label>
              <input type="datetime-local" class="form-input" v-model="form.thoi_gian_ket_thuc" required />
            </div>
            <div class="form-field" style="grid-column: span 2;">
              <label>Trạng thái</label>
              <select class="form-input" v-model="form.tinh_trang">
                <option value="sap_mo">Sắp mở</option>
                <option value="dang_mo">Đang mở</option>
              </select>
            </div>
          </div>
          <div v-if="formError" class="alert alert-error">{{ formError }}</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline" @click="showFormModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary" :disabled="formSaving">
              <i v-if="formSaving" class="fa-solid fa-spinner fa-spin me-1"></i>
              {{ form.id ? 'Cập nhật' : 'Tạo lớp' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL XEM CHI TIẾT + DUYỆT HV -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal = false">
      <div class="modal-content large">
        <div class="modal-header">
          <h3 class="modal-title">Chi tiết: {{ selectedLop?.mon_hoc?.ten_mon_hoc }}</h3>
          <button class="modal-close" @click="showDetailModal = false">×</button>
        </div>
        <div class="modal-body" v-if="selectedLop">
          <div class="detail-grid">
            <div><strong>Hình thức:</strong> {{ selectedLop.hinh_thuc === 'online' ? 'Online' : 'Offline' }}</div>
            <div><strong>Sĩ số:</strong> {{ selectedLop.so_hoc_vien_hien_tai }}/{{ selectedLop.si_so_toi_da }}</div>
            <div><strong>Học phí:</strong> {{ formatMoney(selectedLop.hoc_phi) }} VND</div>
            <div><strong>Thời gian:</strong> {{ formatDate(selectedLop.thoi_gian_bat_dau) }} → {{ formatDate(selectedLop.thoi_gian_ket_thuc) }}</div>
            <div v-if="selectedLop.link_online"><strong>Link:</strong> <a :href="selectedLop.link_online" target="_blank">{{ selectedLop.link_online }}</a></div>
            <div v-if="selectedLop.phong_hoc"><strong>Phòng:</strong> {{ selectedLop.phong_hoc.so_phong }} - {{ selectedLop.phong_hoc.dia_chi }}</div>
          </div>

          <h4 style="margin-top: 18px; margin-bottom: 10px;">Danh sách đăng ký ({{ selectedLop.dang_ky_lops?.length || 0 }})</h4>
          <table class="data-table small">
            <thead>
              <tr>
                <th>Học viên</th>
                <th>Email</th>
                <th>Ngày đăng ký</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!selectedLop.dang_ky_lops || selectedLop.dang_ky_lops.length === 0">
                <td colspan="5" class="text-center text-muted">Chưa có học viên đăng ký.</td>
              </tr>
              <tr v-for="dk in selectedLop.dang_ky_lops" :key="dk.id" v-else>
                <td>{{ dk.hoc_vien?.ho_ten }}</td>
                <td>{{ dk.hoc_vien?.email }}</td>
                <td>{{ formatDate(dk.ngay_dang_ky) }}</td>
                <td><span :class="['tag-pill', dkStatusClass(dk.trang_thai)]">{{ dkStatusText(dk.trang_thai) }}</span></td>
                <td>
                  <button v-if="['cho_thanh_toan', 'da_thanh_toan'].includes(dk.trang_thai)" class="btn-icon" @click="duyetDK(dk, 'duyet')" title="Duyệt"><i class="fa-solid fa-check text-success"></i></button>
                  <button v-if="dk.trang_thai !== 'da_huy'" class="btn-icon btn-icon-danger" @click="duyetDK(dk, 'tu_choi')" title="Từ chối"><i class="fa-solid fa-xmark"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline" @click="showDetailModal = false">Đóng</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { lopHocService } from '../../services/lopHocService'

export default {
  name: 'QuanLyLopHoc',
  data() {
    return {
      loading: false,
      dsLop: [],
      stats: { tong: 0, dangHoatDong: 0, tongHV: 0, daHuy: 0 },

      // Form modal
      showFormModal: false,
      form: this.emptyForm(),
      formSaving: false,
      formError: '',
      dsMonHoc: [],
      dsPhongHoc: [],

      // Detail modal
      showDetailModal: false,
      selectedLop: null,
    }
  },
  mounted() {
    this.loadData()
    this.loadOptions()
  },
  methods: {
    emptyForm() {
      return {
        id: null,
        id_mon_hoc: '',
        id_phong_hoc: '',
        loai_lop: 'dai_tra',
        hinh_thuc: 'online',
        link_online: '',
        hoc_phi: 0,
        si_so_toi_da: 20,
        thoi_gian_bat_dau: '',
        thoi_gian_ket_thuc: '',
        tinh_trang: 'sap_mo',
      }
    },

    async loadData() {
      this.loading = true
      try {
        const res = await lopHocService.getDanhSachLop()
        if (res.status) {
          this.dsLop = res.data || []
          this.calcStats()
        }
      } catch (e) {
        alert(e.message || 'Lỗi tải dữ liệu.')
      } finally {
        this.loading = false
      }
    },

    async loadOptions() {
      try {
        const [resMH, resPH] = await Promise.all([
          lopHocService.getMonHoc(),
          lopHocService.getPhongHoc(),
        ])
        this.dsMonHoc = resMH.data || []
        this.dsPhongHoc = resPH.data || []
      } catch (e) { /* ignore */ }
    },

    calcStats() {
      this.stats.tong = this.dsLop.length
      this.stats.dangHoatDong = this.dsLop.filter(l => !['da_huy','da_ket_thuc'].includes(l.tinh_trang)).length
      this.stats.daHuy = this.dsLop.filter(l => l.tinh_trang === 'da_huy').length
      this.stats.tongHV = this.dsLop.reduce((sum, l) => sum + (l.so_hoc_vien_hien_tai || 0), 0)
    },

    openCreateModal() {
      this.form = this.emptyForm()
      this.formError = ''
      // Default: 1 tuần sau
      const start = new Date()
      start.setDate(start.getDate() + 7)
      start.setHours(19, 0, 0, 0)
      const end = new Date(start)
      end.setHours(start.getHours() + 2)
      this.form.thoi_gian_bat_dau = this.toDateTimeLocal(start)
      this.form.thoi_gian_ket_thuc = this.toDateTimeLocal(end)
      this.showFormModal = true
    },

    openEditModal(lop) {
      this.form = {
        ...lop,
        thoi_gian_bat_dau: this.toDateTimeLocal(new Date(lop.thoi_gian_bat_dau)),
        thoi_gian_ket_thuc: this.toDateTimeLocal(new Date(lop.thoi_gian_ket_thuc)),
      }
      this.formError = ''
      this.showFormModal = true
    },

    onHinhThucChange() {
      if (this.form.hinh_thuc === 'online') {
        this.form.id_phong_hoc = ''
      } else {
        this.form.link_online = ''
      }
    },

    async saveForm() {
      this.formError = ''
      this.formSaving = true
      try {
        const payload = {
          ...this.form,
          thoi_gian_bat_dau: new Date(this.form.thoi_gian_bat_dau).toISOString(),
          thoi_gian_ket_thuc: new Date(this.form.thoi_gian_ket_thuc).toISOString(),
        }
        let res
        if (this.form.id) {
          res = await lopHocService.capNhatLop(this.form.id, payload)
        } else {
          delete payload.id
          res = await lopHocService.taoLop(payload)
        }
        if (res.status) {
          alert(this.form.id ? 'Cập nhật thành công!' : 'Tạo lớp thành công!')
          this.showFormModal = false
          this.loadData()
        } else {
          this.formError = res.message || 'Lỗi.'
        }
      } catch (e) {
        this.formError = e.message || 'Lỗi.'
      } finally {
        this.formSaving = false
      }
    },

    async viewDetail(lop) {
      try {
        const res = await lopHocService.getChiTietLop(lop.id)
        if (res.status) {
          this.selectedLop = res.data
          this.showDetailModal = true
        }
      } catch (e) { alert(e.message) }
    },

    async duyetDK(dk, hanhDong) {
      if (!confirm(`Xác nhận ${hanhDong === 'duyet' ? 'DUYỆT' : 'TỪ CHỐI'} học viên này?`)) return
      try {
        const res = await lopHocService.duyetHocVien(this.selectedLop.id, {
          id_dang_ky: dk.id,
          hanh_dong: hanhDong,
        })
        if (res.status) {
          alert(res.message)
          this.viewDetail(this.selectedLop) // Reload
          this.loadData()
        } else {
          alert(res.message || 'Lỗi.')
        }
      } catch (e) { alert(e.message || 'Lỗi.') }
    },

    async confirmHuy(lop) {
      if (!confirm(`Bạn có chắc muốn HỦY lớp "${lop.mon_hoc?.ten_mon_hoc}"? Tất cả đăng ký sẽ bị hủy!`)) return
      try {
        const res = await lopHocService.huyLop(lop.id)
        if (res.status) {
          alert(res.message)
          this.loadData()
        } else {
          alert(res.message || 'Lỗi.')
        }
      } catch (e) { alert(e.message || 'Lỗi.') }
    },

    // Helpers
    toDateTimeLocal(d) {
      const pad = (n) => String(n).padStart(2, '0')
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
    },
    formatDate(iso) {
      if (!iso) return ''
      return new Date(iso).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    },
    formatMoney(v) { return new Intl.NumberFormat('vi-VN').format(v || 0) },
    statusText(t) {
      return { sap_mo:'Sắp mở', dang_mo:'Đang mở', dang_hoc:'Đang học', da_ket_thuc:'Đã kết thúc', da_huy:'Đã hủy' }[t] || t
    },
    statusTagClass(t) {
      return { sap_mo:'tag-blue', dang_mo:'tag-green', dang_hoc:'tag-purple', da_ket_thuc:'tag-grey', da_huy:'tag-red' }[t] || 'tag-grey'
    },
    dkStatusText(t) {
      return { cho_thanh_toan:'Chờ thanh toán', da_thanh_toan:'Đã thanh toán', da_xac_nhan:'Đã duyệt', da_huy:'Đã hủy' }[t] || t
    },
    dkStatusClass(t) {
      return { cho_thanh_toan:'tag-blue', da_thanh_toan:'tag-info', da_xac_nhan:'tag-green', da_huy:'tag-red' }[t] || 'tag-grey'
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
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 12px; }
.page-title { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.page-subtitle { color: #64748b; font-size: 14px; }
.btn-primary { background: #0060d2; color: #fff; border: 0; padding: 9px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
.btn-outline { background: transparent; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 8px; cursor: pointer; font-size: 14px; }

.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
.stat-card { background: #fff; padding: 16px 18px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 14px; }
.stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
.icon-blue { background: #dbeafe; color: #1e40af; }
.icon-green { background: #dcfce7; color: #15803d; }
.icon-purple { background: #f3e8ff; color: #7e22ce; }
.icon-red { background: #fee2e2; color: #b91c1c; }
.stat-value { font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1; }
.stat-label { font-size: 12.5px; color: #64748b; margin-top: 4px; }

.table-card { background: #fff; border-radius: 14px; padding: 8px; border: 1px solid #e2e8f0; overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.data-table th { background: #f8fafc; padding: 12px; text-align: left; font-weight: 700; color: #475569; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.3px; }
.data-table td { padding: 14px 12px; border-top: 1px solid #f1f5f9; }
.data-table.small th, .data-table.small td { padding: 8px 10px; font-size: 13px; }
.tag-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 50px; display: inline-block; }
.tag-blue { background: #dbeafe; color: #1e40af; }
.tag-green { background: #dcfce7; color: #15803d; }
.tag-purple { background: #f3e8ff; color: #7e22ce; }
.tag-info { background: #e0f2fe; color: #075985; }
.tag-red { background: #fee2e2; color: #b91c1c; }
.tag-grey { background: #f1f5f9; color: #475569; }
.btn-icon { background: transparent; border: 1px solid #e2e8f0; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #475569; margin-right: 4px; }
.btn-icon:hover { background: #f1f5f9; }
.btn-icon-danger { color: #ef4444; }
.btn-icon:disabled { opacity: 0.4; cursor: not-allowed; }

.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.modal-content { background: #fff; border-radius: 16px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
.modal-content.large { max-width: 800px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid #e2e8f0; }
.modal-title { font-size: 18px; font-weight: 700; }
.modal-close { background: transparent; border: 0; font-size: 26px; cursor: pointer; color: #64748b; }
.modal-body { padding: 22px; }
.modal-footer { padding: 16px 22px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-field label { display: block; font-size: 12.5px; font-weight: 600; color: #475569; margin-bottom: 6px; }
.required { color: #ef4444; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid #d8dee4; border-radius: 8px; font-size: 14px; }
.hint { font-size: 11.5px; color: #64748b; margin-top: 3px; display: block; }
.alert { padding: 10px 14px; border-radius: 8px; margin-top: 12px; font-size: 13.5px; }
.alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 24px; font-size: 13.5px; }
.detail-grid > div { padding: 4px 0; }

@media (max-width: 768px) {
  .stats-row, .form-grid, .detail-grid { grid-template-columns: 1fr; }
}
</style>
