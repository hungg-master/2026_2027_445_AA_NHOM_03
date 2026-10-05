<template>
  <main class="container py-4">
    <div class="d-flex justify-content-between mb-3"><h1 class="h3">Quản lý học thử</h1><router-link to="/dat-lich-hoc-thu" class="btn btn-primary">Đặt học thử</router-link></div>
    <p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p>
    <p v-if="message" class="alert alert-info" role="status">{{ message }}</p>
    <p v-if="loading">Đang tải yêu cầu…</p><p v-else-if="!rows.length">Chưa có yêu cầu học thử.</p>
    <article v-for="row in rows" :key="row.id" class="card p-3 mb-3">
      <h5>{{ row.subject || row.mon_hoc?.ten_mon_hoc || 'Yêu cầu học thử' }}</h5>
      <p>{{ row.name || row.ho_ten }} · {{ row.giao_vien?.ho_ten || 'Chưa phân giảng viên' }}</p>
      <p>{{ row.thoi_gian_bat_dau || row.start || row.date }} → {{ row.thoi_gian_ket_thuc || row.end }}</p>
      <p>Trạng thái: {{ status(row.status || row.trang_thai) }}</p>
      <div class="d-flex gap-2">
        <button v-if="role !== 'hoc_vien' && pending(row)" class="btn btn-primary" :disabled="busy" @click="act(row, 'confirm')">Xác nhận lịch</button>
        <button v-if="pending(row) || ['confirmed','da_xac_nhan'].includes(row.status || row.trang_thai)" class="btn btn-outline-danger" :disabled="busy" @click="act(row, 'cancel')">Hủy yêu cầu</button>
        <router-link v-if="row.id_buoi_hoc" :to="role === 'giao_vien' ? '/giao-vien/lich-day' : '/hoc-vien/lich-hoc'" class="btn btn-outline-primary">Xem buổi học</router-link>
      </div>
    </article>
  </main>
</template>
<script>
import product from '../../services/productService';
export default {
  data: () => ({ role: localStorage.getItem('role'), rows: [], loading: false, busy: false, error: '', message: '' }),
  mounted() { this.load(); },
  methods: {
    status(value) { return { pending: 'Chờ xác nhận', confirmed: 'Đã xác nhận', cancelled: 'Đã hủy', cho_xac_nhan: 'Chờ xác nhận', da_xac_nhan: 'Đã xác nhận', da_huy: 'Đã hủy' }[value] || value; },
    pending(row) { return ['pending','cho_xac_nhan','cho_duyet'].includes(row.status || row.trang_thai); },
    async load() { this.loading = true; this.error = ''; try { this.rows = (await product.trials(this.role)).data || []; } catch (error) { this.error = error.message; } finally { this.loading = false; } },
    async act(row, action) { this.busy = true; this.error = ''; try { const res = await product.trialAction(this.role, row.id, action); this.message = res.message; await this.load(); } catch (error) { this.error = error.message; } finally { this.busy = false; } }
  }
};
</script>
