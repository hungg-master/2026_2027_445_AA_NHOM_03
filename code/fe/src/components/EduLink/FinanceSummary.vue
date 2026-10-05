<template>
  <section class="card p-3 mb-3"><h3 class="h5">Thống kê tài chính</h3><p v-if="error" class="text-danger" role="alert">{{ error }}</p><p v-else-if="!data">Đang tải thống kê…</p><dl v-else class="row mb-0"><template v-for="(value, key) in data" :key="key"><dt class="col-sm-7">{{ labels[key] || key }}</dt><dd class="col-sm-5">{{ typeof value === 'number' ? new Intl.NumberFormat('vi-VN').format(value) : value }}</dd></template></dl></section>
</template>
<script>
import support from '../../services/supportService';
export default { data: () => ({ data: null, error: '', labels: { confirmed_amount: 'Số tiền đã xác nhận (VND)', confirmed_revenue: 'Doanh thu đã xác nhận (VND)', confirmed_count: 'Giao dịch đã xác nhận', pending_amount: 'Số tiền chờ thanh toán (VND)', paid_amount: 'Số tiền đã thanh toán (VND)', total_paid: 'Đã thanh toán (VND)', total_revenue: 'Doanh thu (VND)', pending_count: 'Khoản chờ đối soát', currency: 'Đơn vị tiền', mode: 'Chế độ', revenue: 'Doanh thu đã đối soát (VND)', transaction_count: 'Giao dịch đã xác nhận' } }), mounted() { this.load(); }, methods: { async load() { try { this.data = (await support.finance(localStorage.getItem('role'))).data; } catch (error) { this.error = error.message; } } } };
</script>
