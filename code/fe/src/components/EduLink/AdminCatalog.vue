<template>
  <main class="container py-4"><h1 class="h3">Quản lý môn học và phòng học</h1><p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p><p v-if="message" class="alert alert-info" role="status">{{ message }}</p>
    <div class="d-flex gap-2 my-3"><button class="btn btn-outline-primary" @click="change('mon-hoc')">Môn học</button><button class="btn btn-outline-primary" @click="change('phong-hoc')">Phòng học</button></div>
    <div class="row g-3"><section class="col-md-7"><div class="card p-3"><p v-if="!rows.length">Chưa có dữ liệu.</p><article v-for="row in rows" :key="row.id" class="border-bottom py-3"><strong>{{ kind === 'mon-hoc' ? row.ten_mon_hoc : row.so_phong }}</strong><p>{{ row.mo_ta }} {{ row.dia_chi }}</p><button class="btn btn-outline-primary btn-sm me-2" @click="edit(row)">Sửa</button><button class="btn btn-outline-danger btn-sm" :disabled="busy" @click="remove(row)">Xóa</button></article></div></section>
    <aside class="col-md-5"><form class="card p-3 d-grid gap-2" @submit.prevent="save"><h3 class="h5">{{ form.id ? 'Cập nhật' : 'Thêm mới' }}</h3><label>{{ kind === 'mon-hoc' ? 'Tên môn' : 'Số phòng' }}<input v-if="kind === 'mon-hoc'" v-model.trim="form.ten_mon_hoc" class="form-control" required /><input v-else v-model.trim="form.so_phong" class="form-control" required /></label><label v-if="kind === 'mon-hoc'">Khối/lớp<input v-model.trim="form.lop" class="form-control" /></label><label v-else>Địa chỉ<input v-model.trim="form.dia_chi" class="form-control" required /></label><label>Mô tả<textarea v-model="form.mo_ta" class="form-control"></textarea></label><label v-if="kind === 'mon-hoc'">Trạng thái<select v-model.number="form.tinh_trang" class="form-select"><option :value="1">Hoạt động</option><option :value="0">Tạm dừng</option></select></label><button class="btn btn-primary" :disabled="busy">Lưu</button><button type="button" class="btn btn-outline-secondary" @click="form = empty()">Hủy sửa</button></form></aside></div>
  </main>
</template>
<script>
import support from '../../services/supportService';
export default {
  data() { return { kind: 'mon-hoc', rows: [], form: this.empty(), error: '', message: '', busy: false }; },
  mounted() { this.load(); },
  methods: {
    empty() { return { id: null, ten_mon_hoc: '', so_phong: '', mo_ta: '', lop: '', dia_chi: '', tinh_trang: 1 }; },
    change(kind) { this.kind = kind; this.form = this.empty(); this.rows = []; this.load(); },
    edit(row) { this.form = { ...this.empty(), ...row }; },
    async load() { this.error = ''; try { this.rows = (await support.catalog(this.kind)).data || []; } catch (error) { this.error = error.message; } },
    async save() { this.busy = true; this.error = ''; try { const data = this.kind === 'mon-hoc' ? { ten_mon_hoc: this.form.ten_mon_hoc, mo_ta: this.form.mo_ta, lop: this.form.lop, tinh_trang: this.form.tinh_trang } : { so_phong: this.form.so_phong, dia_chi: this.form.dia_chi, mo_ta: this.form.mo_ta }; const result = await support.saveCatalog(this.kind, this.form.id, data); this.message = result.message; this.form = this.empty(); await this.load(); } catch (error) { this.error = error.message; } finally { this.busy = false; } },
    async remove(row) { if (!confirm('Xóa mục này? Dữ liệu đang được sử dụng sẽ được máy chủ bảo vệ.')) return; this.busy = true; this.error = ''; try { const result = await support.deleteCatalog(this.kind, row.id); this.message = result.message; await this.load(); } catch (error) { this.error = error.message; } finally { this.busy = false; } }
  }
};
</script>
