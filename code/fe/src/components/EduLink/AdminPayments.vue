<template>
  <main class="container py-4"><h1 class="h3">Đối soát thanh toán thử nghiệm</h1><FinanceSummary /><p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p><p v-if="message" class="alert alert-info" role="status">{{ message }}</p><p v-if="!rows.length">Chưa có giao dịch.</p><article v-for="row in rows" :key="row.id" class="card p-3 mb-3"><h5>Giao dịch #{{ row.id }}</h5><p>{{ row.amount }} {{ row.currency }} · {{ row.status }} · {{ row.created_at }}</p><form v-if="row.status === 'awaiting_manual'" class="d-flex gap-2" @submit.prevent="settle(row)"><input v-model.trim="references[row.id]" class="form-control" placeholder="Mã tham chiếu đối soát" required /><button class="btn btn-primary" :disabled="busy">Xác nhận thử nghiệm</button></form></article></main>
</template>
<script>
import support from '../../services/supportService';
import FinanceSummary from './FinanceSummary.vue';
export default { components: { FinanceSummary }, data: () => ({ rows: [], references: {}, keys: {}, busy: false, error: '', message: '' }), mounted() { this.load(); }, methods: { async load() { try { this.rows = (await support.transactions()).data || []; } catch (error) { this.error = error.message; } }, async settle(row) { this.busy = true; this.error = ''; const key = this.keys[row.id] || crypto.randomUUID(); this.keys[row.id] = key; try { const result = await support.settle(row.id, key, this.references[row.id]); this.message = result.message; await this.load(); } catch (error) { this.error = error.message; } finally { this.busy = false; } } } };
</script>
