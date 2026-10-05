<template>
  <header class="main-header">
    <div class="header-container">
      <!-- Logo -->
      <router-link to="/" class="brand-logo">
        <div class="logo-icon-wrap">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <span class="brand-name">Edu<span>Link</span></span>
      </router-link>

      <!-- Navigation Links -->
      <nav class="nav-links">
        <router-link v-if="userRole !== 'admin'" to="/my-schedule" class="nav-item">Lịch rảnh</router-link>
        <router-link v-if="userRole === 'hoc_vien'" to="/hoc-vien/dang-ky-lop" class="nav-item">Đăng ký lớp</router-link>
        <router-link v-if="userRole !== 'admin'" :to="!isLoggedIn ? '/client/danh-sach-lop' : userRole === 'giao_vien' ? '/giao-vien/quan-ly-lop' : '/hoc-vien/lop-cua-toi'" class="nav-item">Lớp học</router-link>
        <router-link v-if="userRole === 'hoc_vien'" to="/hoc-vien/lich-hoc" class="nav-item">Lịch học</router-link>
        <router-link v-else-if="userRole === 'giao_vien'" to="/giao-vien/lich-day" class="nav-item">Lịch dạy</router-link>
        
        <router-link v-if="isLoggedIn && userRole !== 'admin'" to="/hoc-thu-cua-toi" class="nav-item">Học thử</router-link><router-link v-if="isLoggedIn && userRole !== 'admin'" to="/tu-van" class="nav-item">Tư vấn</router-link><router-link v-if="isLoggedIn && userRole !== 'admin'" to="/tro-ly-ai" class="nav-item">Trợ lý AI</router-link><router-link v-if="userRole === 'admin'" to="/admin/danh-muc" class="nav-item">Danh mục</router-link><router-link v-if="userRole === 'admin'" to="/admin/thanh-toan" class="nav-item">Đối soát</router-link>
      </nav>

      <!-- Header Actions -->
      <div class="header-actions">
        <!-- Chưa đăng nhập -->
        <router-link v-if="!isLoggedIn" to="/dang-ky" class="btn btn-outline-primary btn-login">
          Đăng nhập / Đăng ký
        </router-link>

        <!-- Đã đăng nhập: Avatar tròn bo tròn -->
        <div v-else class="user-dropdown-wrapper" style="position: relative;">
          <button class="user-avatar-btn-circle" @click.stop="toggleUserDropdown" title="Tài khoản cá nhân">
            <img :src="userAvatar" alt="Avatar" class="avatar-img-circle" />
          </button>

          <!-- Dropdown Menu: Chỉ gồm Thông tin tài khoản, Lịch học, Đăng xuất -->
          <div v-if="showUserDropdown" class="user-dropdown-menu">
            <div class="dropdown-header">
              <strong>{{ userName }}</strong>
              <span class="d-block text-muted" style="font-size:11px">{{ userRole === 'admin' ? 'Quản trị viên' : userRole === 'giao_vien' ? 'Giáo viên' : 'Học viên' }}</span>
            </div>
            <div class="dropdown-divider"></div>
            <router-link :to="userRole === 'admin' ? '/admin/danh-muc' : '/client/profile'" class="dropdown-item" @click="showUserDropdown = false">
              <i class="fa-regular fa-user me-2"></i> {{ userRole === 'admin' ? 'Quản trị hệ thống' : 'Thông tin tài khoản' }}
            </router-link>
            <router-link v-if="userRole !== 'admin'" :to="userRole === 'giao_vien' ? '/giao-vien/lich-day' : '/hoc-vien/lich-hoc'" class="dropdown-item" @click="showUserDropdown = false">
              <i class="fa-regular fa-calendar-days me-2 text-primary"></i> Lịch học
            </router-link>
            <div class="dropdown-divider"></div>
            <a href="javascript:void(0)" @click="logout" class="dropdown-item text-danger">
              <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Đăng xuất
            </a>
          </div>
        </div>
      </div>
    </div>
    <p v-if="sessionError" class="alert alert-danger mb-0 rounded-0" role="alert">{{ sessionError }}</p>
  </header>
</template>

<script>
import { logout as logoutSession } from '../../services/api';

export default {
  name: "TopKhachHang",
  data() {
    return {
      isLoggedIn: false,
      showUserDropdown: false,
      defaultAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
      userAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
      userName: '',
      userRole: '',
      userData: null,
      sessionError: '',
    };
  },
  mounted() {
    this.dongBoNguoiDung();
    document.addEventListener("click", this.closeDropdownOnClickOutside);
    window.addEventListener("edulink:user-updated", this.handleUserUpdated);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.closeDropdownOnClickOutside);
    window.removeEventListener("edulink:user-updated", this.handleUserUpdated);
  },
  watch: {
    '$route'() {
      this.dongBoNguoiDung();
      this.showUserDropdown = false;
    }
  },
  methods: {
    handleUserUpdated(e) {
      if (e && e.detail) {
        this.userData = e.detail;
        this.userName = e.detail.ho_ten || e.detail.name || "Khách hàng";
        this.userAvatar = e.detail.avatar || e.detail.hinh_anh || this.defaultAvatar;
      } else {
        this.dongBoNguoiDung();
      }
    },
    dongBoNguoiDung() {
      const token = localStorage.getItem("token") || localStorage.getItem("edulink_token");
      if (token) {
        this.isLoggedIn = true;
        this.userRole = localStorage.getItem("role") || "";
        const userStr = localStorage.getItem("user") || localStorage.getItem("edulink_user");
        if (userStr) {
          try {
            this.userData = JSON.parse(userStr);
            this.userName = this.userData.ho_ten || this.userData.name || "Khách hàng";
            if (this.userData.avatar || this.userData.hinh_anh) {
              this.userAvatar = this.userData.avatar || this.userData.hinh_anh;
            } else {
              this.userAvatar = this.defaultAvatar;
            }
          } catch (e) {}
        }
      } else {
        this.isLoggedIn = false;
        this.userName = '';
        this.userRole = '';
        this.userData = null;
      }
    },
    toggleUserDropdown() {
      this.showUserDropdown = !this.showUserDropdown;
    },
    closeDropdownOnClickOutside(e) {
      const wrapper = this.$el?.querySelector('.user-dropdown-wrapper');
      if (wrapper && !wrapper.contains(e.target)) {
        this.showUserDropdown = false;
      }
    },
    async logout() {
      this.sessionError = '';
      try {
        await logoutSession();
      } catch (err) {
        this.sessionError = err.message || 'Chưa đăng xuất được. Vui lòng thử lại.';
        return;
      }
      localStorage.removeItem("token");
      localStorage.removeItem("edulink_token");
      localStorage.removeItem("role");
      localStorage.removeItem("user");
      localStorage.removeItem("edulink_user");
      this.isLoggedIn = false;
      this.showUserDropdown = false;
      this.$router.push("/");
    }
  }
};
</script>

<style scoped>
.main-header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.03);
  width: 100%;
}

.header-container {
  max-width: 1240px;
  margin: 0 auto;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  font-size: 22px;
  font-weight: 800;
  color: #0060d2;
  letter-spacing: -0.5px;
}

.logo-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, #0060d2 0%, #004fb0 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.brand-name span {
  color: #0f172a;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 24px;
}

.nav-item {
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  transition: color 0.15s ease;
}

.nav-item:hover,
.nav-item.router-link-active {
  color: #0060d2;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-login {
  border-radius: 20px;
  padding: 6px 18px;
  font-size: 13.5px;
  font-weight: 600;
  text-decoration: none;
}

.user-avatar-btn-circle {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  padding: 0;
  border: 2px solid #0060d2;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.user-avatar-btn-circle:hover {
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(0, 96, 210, 0.3);
}

.avatar-img-circle {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.user-dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 210px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  padding: 8px 0;
  z-index: 1050;
  animation: dropdownFadeIn 0.15s ease-out;
}

@keyframes dropdownFadeIn {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

.dropdown-header {
  padding: 8px 16px;
  color: #0f172a;
}

.dropdown-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 6px 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 9px 16px;
  color: #475569;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 500;
  transition: background 0.15s, color 0.15s;
  cursor: pointer;
}

.dropdown-item:hover {
  background: #f8fafc;
  color: #0060d2;
}

.dropdown-item.text-danger:hover {
  color: #ef4444;
}
</style>
