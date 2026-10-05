<template>
  <section class="face-proof card p-3 text-center">
    <h5>{{ purpose === 'sample' ? 'Đăng ký mẫu Face ID' : 'Xác thực Face ID' }}</h5>
    <video ref="video" autoplay playsinline muted class="mx-auto rounded bg-dark" width="260" height="220"></video>
    <p class="mt-2" role="status">{{ message || 'Một người trong khung hình, đủ ánh sáng, nhìn thẳng camera.' }}</p>
    <p v-if="error" class="text-danger" role="alert">{{ error }}</p>
    <label v-if="purpose === 'sample'" class="text-start mb-3">Mật khẩu hiện tại (khi thay mẫu khuôn mặt khác)<input v-model="currentPassword" type="password" autocomplete="current-password" class="form-control" /></label>
    <div class="d-flex gap-2 justify-content-center">
      <button class="btn btn-primary" :disabled="busy" @click="scan">{{ busy ? 'Đang xử lý…' : 'Quét khuôn mặt' }}</button>
      <button class="btn btn-outline-secondary" @click="cancel">Đóng</button>
    </div>
    <router-link class="mt-2" to="/client/profile#faceid">Đăng ký hoặc cập nhật mẫu khuôn mặt</router-link>
    <small class="text-muted mt-2">Đối sánh mẫu khuôn mặt; chưa bao gồm kiểm tra chống giả mạo.</small>
  </section>
</template>
<script>
import { captureFace } from '../../services/faceCapture';
import product from '../../services/productService';
export default {
  props: { purpose: { type: String, required: true }, target: { type: Object, default: () => ({}) } },
  emits: ['verified','cancel'],
  data: () => ({ busy: false, error: '', message: '', controller: null, stream: null, currentPassword: '' }),
  beforeUnmount() { this.stop(); },
  methods: {
    stop() { this.controller?.abort(); this.stream?.getTracks().forEach(track => track.stop()); this.stream = null; },
    cancel() { this.stop(); this.$emit('cancel'); },
    async scan() {
      if (this.busy) return;
      this.busy = true; this.error = ''; this.controller = new AbortController();
      const signal = this.controller.signal;
      try {
        const descriptor = await captureFace(this.$refs.video, { signal, onProgress: value => this.message = value, onStream: value => this.stream = value });
        if (signal.aborted) return;
        this.message = 'Đang gửi yêu cầu đối sánh đến máy chủ…';
        const role = localStorage.getItem('role');
        const result = this.purpose === 'sample' ? await product.sample(role, descriptor, this.currentPassword) : await product.verify(role, descriptor, this.purpose, this.target);
        this.currentPassword = '';
        if (!signal.aborted) this.$emit('verified', result.data);
      } catch (error) { if (!signal.aborted) this.error = error.message || 'Không thể xác thực. Kiểm tra camera, mẫu đã đăng ký và kết nối máy chủ.'; }
      finally { this.stop(); this.busy = false; }
    }
  }
};
</script>
