<template>
  <main class="container py-4"><h1 class="h3">Giảng viên đã được duyệt</h1><p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p><p v-if="loading">Đang tải giảng viên…</p><p v-else-if="!teachers.length">Chưa có giảng viên trong danh sách.</p><div class="row g-3"><article v-for="teacher in teachers" :key="teacher.id" class="col-md-6 col-lg-4"><div class="card p-3 h-100"><img v-if="teacher.hinh_anh" :src="teacher.hinh_anh" :alt="teacher.ho_ten" style="height:160px;object-fit:cover;" class="rounded mb-3" /><h2 class="h5">{{ teacher.ho_ten }}</h2><p>{{ teacher.chuc_danh }} · {{ teacher.so_nam_kinh_nghiem || 0 }} năm kinh nghiệm</p><p style="white-space:pre-wrap;">{{ teacher.mo_ta || 'Chưa cập nhật giới thiệu.' }}</p><router-link to="/my-schedule" class="btn btn-primary mt-auto">Tìm lịch học thử phù hợp</router-link></div></article></div></main>
</template>
<script>
import http from '../../services/http';
import { accepted } from '../../services/productContract';
export default { data: () => ({ teachers: [], error: '', loading: false }), mounted() { this.load(); }, methods: { async load() { this.loading = true; this.error = ''; try { this.teachers = (await accepted(http.get('/client/giao-vien/data'))).data || []; } catch (error) { this.error = error.message; } finally { this.loading = false; } } } };
</script>
