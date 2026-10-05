<template>
  <main class="container py-5" style="max-width:580px;">
    <div class="card p-4"><h1 class="h4">{{ form.token ? 'Đặt lại mật khẩu' : 'Quên mật khẩu' }}</h1>
      <p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p><p v-if="message" class="alert alert-info" role="status">{{ message }}</p>
      <form class="d-grid gap-3" @submit.prevent="submit">
        <label>Loại tài khoản<select v-model="form.role" class="form-select"><option value="hoc_vien">Học viên</option><option value="giao_vien">Giáo viên</option><option value="admin">Quản trị viên</option></select></label>
        <label>Email<input v-model.trim="form.email" type="email" class="form-control" required /></label>
        <label v-if="form.token">Mật khẩu mới<input v-model="form.password" type="password" class="form-control" minlength="8" autocomplete="new-password" required /></label>
        <label v-if="form.token">Xác nhận mật khẩu<input v-model="form.password_confirmation" type="password" class="form-control" minlength="8" autocomplete="new-password" required /></label>
        <button class="btn btn-primary" :disabled="busy">{{ busy ? 'Đang gửi…' : form.token ? 'Đặt lại mật khẩu' : 'Gửi liên kết qua email' }}</button>
      </form><router-link class="mt-3" to="/dang-ky">Quay lại đăng nhập</router-link>
    </div>
  </main>
</template>
<script>
import support from '../../services/supportService';
export default {
  data() { return { busy: false, error: '', message: '', form: { role: this.$route.query.role || 'hoc_vien', email: this.$route.query.email || '', token: this.$route.query.token || '', password: '', password_confirmation: '' } }; },
  methods: { async submit() { this.busy = true; this.error = ''; this.message = ''; try { const res = this.form.token ? await support.reset(this.form) : await support.forgot({ role: this.form.role, email: this.form.email }); this.message = res.message; this.form.password = ''; this.form.password_confirmation = ''; } catch (error) { this.error = error.message; } finally { this.busy = false; } } }
};
</script>
