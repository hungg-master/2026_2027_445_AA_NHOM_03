<template>
  <div class="client-layout-wrapper">
    <!-- 1. TOP MENU KHÁCH HÀNG -->
    <TopKhachHang></TopKhachHang>

    <!-- 2. ROUTER CONTENT (Ở GIỮA) -->
    <main class="client-page-content">
      <slot>
        <router-view></router-view>
      </slot>
    </main>

    <!-- 3. FOOTER KHÁCH HÀNG -->
    <BotKhachHang></BotKhachHang>
  </div>
</template>

<script>
import TopKhachHang from "../components/TopKhachHang.vue";
import BotKhachHang from "../components/BotKhachHang.vue";

export default {
  name: "KhachHangLayout",
  components: {
    TopKhachHang,
    BotKhachHang
  }
};
</script>

<style scoped>
.client-layout-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f8fafc;
  color: #1e293b;
  width: 100%;
}

.client-page-content {
  flex: 1 0 auto;
  width: 100%;
  display: flex;
  flex-direction: column;
}
</style>
