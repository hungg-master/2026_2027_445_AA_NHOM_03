import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save
import { logout } from '../services/api';

// Danh sách routes cần đăng nhập
const AUTH_REQUIRED_ROUTES = [
  "/my-schedule",
  "/my-classes",
  "/thanh-toan",
  "/hoc-phi",
  "/hoc-vien",
  "/student-profile",
  "/ho-so-hoc-vien",
  "/ho-so-giang-vien",
  "/teacher-dashboard",
  "/giao-vien-profile",
  "/ho-so-giao-vien",
  "/danh-gia",
  "/danh-gia-buoi-hoc",
  "/review",
];

const routes = [
  {
    path: '/logout',
    component: () => import('../components/EduLink/LandingPage.vue'),
    meta: { layout: 'blank' },
  },
  { path: '/face-id', redirect: '/hoc-vien#face-id-section' },
  {
    path: "/",
    name: "trang-chu",
    component: () => import("../components/EduLink/LandingPage.vue"),
    meta: { layout: "blank" }
  },
  { path: "/home",        redirect: "/" },
  { path: "/trang-chu",  redirect: "/" },
  { path: "/landing-page", redirect: "/" },

  // ===== CÔNG KHAI (không cần đăng nhập) =====
  {
    path: "/dang-ky",
    name: "dang-ky",
    component: () => import("../components/DangKy/index.vue"),
    meta: { layout: "blank", public: true }
  },
  { path: "/register",   redirect: "/dang-ky" },
  { path: "/login",      redirect: "/dang-ky" },
  { path: "/dang-nhap",  redirect: "/dang-ky" },

  {
    path: "/giao-vien",
    name: "giao-vien",
    component: () => import("../components/EduLink/TeacherProfile.vue"),
    meta: { layout: "blank", public: true }
  },
  { path: "/teacher-profile", redirect: "/giao-vien" },

  {
    path: "/dat-lich-hoc-thu",
    name: "dat-lich-hoc-thu",
    component: () => import("../components/EduLink/TrialBooking.vue"),
    meta: { layout: "blank", public: true }
  },

  // ===== CẦN ĐĂNG NHẬP =====
  {
    path: "/my-schedule",
    name: "my-schedule",
    component: () => import("../components/EduLink/MySchedule.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  { path: "/schedule", redirect: "/my-schedule" },

  {
    path: "/my-classes",
    name: "my-classes",
    component: () => import("../components/EduLink/ClassManagement.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  { path: "/classes", redirect: "/my-classes" },

  {
    path: "/thanh-toan",
    name: "thanh-toan",
    component: () => import("../components/EduLink/ThanhToanHocPhi.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  { path: "/hoc-phi", redirect: "/thanh-toan" },

  {
    path: "/hoc-vien",
    name: "hoc-vien",
    component: () => import("../components/EduLink/StudentProfile.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  { path: "/student-profile",  redirect: "/hoc-vien" },
  { path: "/ho-so-hoc-vien",   redirect: "/hoc-vien" },

  {
    path: "/ho-so-giang-vien",
    name: "ho-so-giang-vien",
    component: () => import("../components/EduLink/TeacherDashboard.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  { path: "/teacher-dashboard", redirect: "/ho-so-giang-vien" },
  { path: "/giao-vien-profile", redirect: "/ho-so-giang-vien" },
  { path: "/ho-so-giao-vien",   redirect: "/ho-so-giang-vien" },

  {
    path: "/danh-gia",
    name: "danh-gia",
    component: () => import("../components/EduLink/LessonReview.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  {
    path: "/review",
    redirect: "/danh-gia"
  },
  {
    path: "/danh-gia-buoi-hoc",
    redirect: "/danh-gia"
  },

  // ============ TÍNH NĂNG MỚI: LỊCH & LỚP HỌC ============
  // Giáo viên
  {
    path: "/giao-vien/lich-day",
    name: "giao-vien-lich-day",
    component: () => import("../components/EduLink/LichDayGiaoVien.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  {
    path: "/giao-vien/quan-ly-lop",
    name: "giao-vien-quan-ly-lop",
    component: () => import("../components/EduLink/QuanLyLopHoc.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },

  // Học viên
  {
    path: "/hoc-vien/lich-hoc",
    name: "hoc-vien-lich-hoc",
    component: () => import("../components/EduLink/LichHocHocVien.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  {
    path: "/hoc-vien/lop-cua-toi",
    name: "hoc-vien-lop-cua-toi",
    component: () => import("../components/EduLink/LopCuaToi.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },

  // Public (khám phá lớp học)
  {
    path: "/client/danh-sach-lop",
    name: "client-danh-sach-lop",
    component: () => import("../components/EduLink/DanhSachLopPublic.vue"),
    meta: { layout: "blank" }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

// ===== NAVIGATION GUARD: Chặn trang cần đăng nhập =====
router.beforeEach(async (to, from, next) => {
  if (to.path === '/logout') {
    try {
      await logout();
      next('/');
    } catch (error) {
      alert(error.message || 'Không thể kết nối máy chủ để đăng xuất.');
      next(false);
    }
    return;
  }
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
  const token = localStorage.getItem("token");

  if (requiresAuth && !token) {
    // Chưa đăng nhập → chuyển về trang đăng nhập, lưu lại trang muốn vào
    next({ path: "/dang-ky", query: { redirect: to.fullPath } });
  } else {
    next();
  }
});

export default router;
