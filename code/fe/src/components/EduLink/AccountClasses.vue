<template><QuanLyLopHoc v-if="role === 'giao_vien'" /><LopCuaToi v-else-if="role === 'hoc_vien'" /><main v-else class="container py-4"><router-link to="/admin/danh-muc">Quản lý danh mục</router-link> · <router-link to="/admin/thanh-toan">Đối soát học phí</router-link></main></template>
<script>
import QuanLyLopHoc from './QuanLyLopHoc.vue';
import LopCuaToi from './LopCuaToi.vue';
export default { components: { QuanLyLopHoc, LopCuaToi }, data: () => ({ role: localStorage.getItem('role') }) };
</script>
