<template>
  <main class="container py-4" style="max-width:850px;"><div class="card p-4"><h1 class="h3">Trợ lý học tập</h1><p>Hỏi về học tập hoặc dữ liệu tài khoản của bạn. Câu trả lời của AI cần được kiểm tra lại trước khi sử dụng.</p><p v-if="error" class="alert alert-warning" role="alert">{{ error }}</p><form @submit.prevent="ask" class="d-grid gap-3"><label>Câu hỏi<textarea v-model.trim="message" class="form-control" rows="4" maxlength="2000" required></textarea></label><button class="btn btn-primary" :disabled="busy">{{ busy ? 'Đang chờ câu trả lời…' : 'Gửi câu hỏi' }}</button></form><p v-if="reply" class="mt-3" style="white-space:pre-wrap;">{{ reply }}</p></div></main>
</template>
<script>
import support from '../../services/supportService';
export default { data: () => ({ message: '', reply: '', error: '', busy: false }), methods: { async ask() { this.busy = true; this.error = ''; this.reply = ''; try { this.reply = (await support.assist({ message: this.message })).data?.reply || ''; } catch (error) { this.error = error.message || 'Dịch vụ AI chưa sẵn sàng.'; } finally { this.busy = false; } } } };
</script>
