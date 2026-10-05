<template>
  <section class="card p-4">
    <h3 class="h5">Tài khoản ngân hàng giáo viên</h3>
    <p v-if="error" class="text-danger" role="alert">{{ error }}</p><p v-if="message" role="status">{{ message }}</p>
    <p v-if="!rows.length">Chưa có tài khoản ngân hàng.</p>
    <p v-for="row in rows" :key="row.id">{{ row.bank_name }} · {{ row.account_holder }} · ****{{ row.account_last_four }} {{ row.is_current ? '(Đang dùng)' : '' }}</p>
    <form @submit.prevent="save" class="d-grid gap-2 mt-3">
      <label>Tên ngân hàng<input v-model.trim="form.bank_name" class="form-control" maxlength="150" required /></label>
      <label>Chủ tài khoản<input v-model.trim="form.account_holder" class="form-control" maxlength="150" required /></label>
      <label>Số tài khoản<input v-model.trim="form.account_number" class="form-control" autocomplete="off" inputmode="numeric" required /></label>
      <button class="btn btn-primary" :disabled="busy">{{ busy ? 'Đang lưu…' : 'Lưu tài khoản ngân hàng' }}</button>
    </form>
  </section>
</template>
<script>
import support from '../../services/supportService';
export default {
  data: () => ({ rows: [], form: { bank_name: '', account_holder: '', account_number: '' }, busy: false, error: '', message: '' }),
  mounted() { this.load(); },
  methods: {
    async load() { try { this.rows = (await support.banks()).data || []; } catch (error) { this.error = error.message; } },
    async save() { this.busy = true; this.error = ''; try { const res = await support.bank(this.form); this.message = res.message; this.form.account_number = ''; await this.load(); } catch (error) { this.error = error.message; } finally { this.busy = false; } }
  }
};
</script>
