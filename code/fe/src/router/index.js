import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save
import { logout } from '../services/api';

// Danh sách routes cần đăng nhập
const AUTH_REQUIRED_ROUTES = [
  "/my-schedule",
  "/my-classes",
  "/thanh-toan",
  "/hoc-phi",
  "/client/profile",
  "/profile",
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
  "/phong-hoc",
  "/phong-hop",
];

const routes = [
  { path: '/quen-mat-khau', component: () => import('../components/EduLink/PasswordReset.vue'), meta: { layout: 'blank', public: true } },
  { path: '/reset-password', component: () => import('../components/EduLink/PasswordReset.vue'), meta: { layout: 'blank', public: true } },
  { path: '/hoc-thu-cua-toi', component: () => import('../components/EduLink/TrialManagement.vue'), meta: { layout: 'client', requiresAuth: true } },
  { path: '/tu-van', component: () => import('../components/EduLink/ConsultationChat.vue'), meta: { layout: 'client', requiresAuth: true } },
  { path: '/tro-ly-ai', component: () => import('../components/EduLink/AiAssistant.vue'), meta: { layout: 'client', requiresAuth: true } },
  { path: '/admin/danh-muc', component: () => import('../components/EduLink/AdminCatalog.vue'), meta: { layout: 'client', requiresAuth: true, role: 'admin' } },
  { path: '/admin/thanh-toan', component: () => import('../components/EduLink/AdminPayments.vue'), meta: { layout: 'client', requiresAuth: true, role: 'admin' } },
  {
    path: '/logout',
    component: () => import('../components/EduLink/LandingPage.vue'),
    meta: { layout: 'blank' },
  },
  { path: '/face-id', redirect: '/client/profile#faceid' },
  {
    path: "/",
    name: "trang-chu",
    component: () => import("../components/EduLink/LandingPage.vue"),
    meta: { layout: "client" }
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
    component: () => import("../components/EduLink/TeacherDirectory.vue"),
    meta: { layout: "client", public: true }
  },
  { path: "/teacher-profile", redirect: "/giao-vien" },

  {
    path: "/dat-lich-hoc-thu",
    name: "dat-lich-hoc-thu",
    component: () => import("../components/EduLink/TrialBooking.vue"),
    meta: { layout: "client", public: true }
  },

  // ===== CẦN ĐĂNG NHẬP =====
  {
    path: "/my-schedule",
    name: "my-schedule",
    component: () => import("../components/EduLink/MySchedule.vue"),
    meta: { layout: "client", requiresAuth: true }
  },
  { path: "/schedule", redirect: "/my-schedule" },

  {
    path: "/my-classes",
    name: "my-classes",
    component: () => import("../components/EduLink/AccountClasses.vue"),
    meta: { layout: "client", requiresAuth: true }
  },
  { path: "/classes", redirect: "/my-classes" },

  {
    path: "/thanh-toan",
    name: "thanh-toan",
    component: () => import("../components/EduLink/ThanhToanHocPhi.vue"),
    meta: { layout: "client", requiresAuth: true }
  },
  { path: "/hoc-phi", redirect: "/thanh-toan" },

  {
    path: "/client/profile",
    name: "client-profile",
    component: () => import("../components/EduLink/ClientProfile.vue"),
    meta: { layout: "client", requiresAuth: true }
  },
  { path: "/profile",          redirect: "/client/profile" },
  { path: "/hoc-vien",         redirect: "/client/profile" },
  { path: "/student-profile",  redirect: "/client/profile" },
  { path: "/ho-so-hoc-vien",   redirect: "/client/profile" },

  {
    path: "/ho-so-giang-vien",
    name: "ho-so-giang-vien",
    component: () => import("../components/EduLink/ClientProfile.vue"),
    meta: { layout: "client", requiresAuth: true, role: "giao_vien" }
  },
  { path: "/teacher-dashboard", redirect: "/ho-so-giang-vien" },
  { path: "/giao-vien-profile", redirect: "/ho-so-giang-vien" },
  { path: "/ho-so-giao-vien",   redirect: "/ho-so-giang-vien" },

  {
    path: "/danh-gia",
    name: "danh-gia",
    component: () => import("../components/EduLink/LessonReview.vue"),
    meta: { layout: "client", requiresAuth: true }
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
    meta: { layout: "client", requiresAuth: true, role: "giao_vien" }
  },
  {
    path: "/giao-vien/quan-ly-lop",
    name: "giao-vien-quan-ly-lop",
    component: () => import("../components/EduLink/QuanLyLopHoc.vue"),
    meta: { layout: "client", requiresAuth: true, role: "giao_vien" }
  },

  // Học viên
  {
    path: "/hoc-vien/lich-hoc",
    name: "hoc-vien-lich-hoc",
    component: () => import("../components/EduLink/LichHocHocVien.vue"),
    meta: { layout: "client", requiresAuth: true, role: "hoc_vien" }
  },
  {
    path: "/hoc-vien/lop-cua-toi",
    name: "hoc-vien-lop-cua-toi",
    component: () => import("../components/EduLink/LopCuaToi.vue"),
    meta: { layout: "client", requiresAuth: true, role: "hoc_vien" }
  },

  {
    path: "/hoc-vien/dang-ky-lop",
    name: "hoc-vien-dang-ky-lop",
    component: () => import("../components/EduLink/DangKyLopHoc.vue"),
    meta: { layout: "client", requiresAuth: true, role: "hoc_vien" }
  },
  { path: "/dang-ky-lop", redirect: "/hoc-vien/dang-ky-lop" },

  // Public (khám phá lớp học)
  {
    path: "/client/danh-sach-lop",
    name: "client-danh-sach-lop",
    component: () => import("../components/EduLink/DangKyLopHoc.vue"),
    meta: { layout: "client" }
  },
  { path: "/client/lop-hoc", redirect: "/hoc-vien/dang-ky-lop" },
  {
    path: "/phong-hoc/:id",
    name: "phong-hoc",
    component: () => import("../components/EduLink/PhongHopVideo.vue"),
    meta: { layout: "blank", requiresAuth: true }
  },
  {
    path: "/phong-hop/:id",
    redirect: to => `/phong-hoc/${to.params.id}`
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

// ===== NAVIGATION GUARD: Chặn trang cần đăng nhập & phân quyền vai trò =====
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
  const userRole = localStorage.getItem("role") || "";

  if (requiresAuth && !token) {
    // Chưa đăng nhập → chuyển về trang đăng nhập
    next({ path: "/dang-ky", query: { redirect: to.fullPath } });
    return;
  }

  // Phân quyền theo vai trò (Role-based access control)
  const targetRecord = to.matched.find((record) => record.meta.role);
  if (targetRecord && token) {
    const requiredRole = targetRecord.meta.role;
    if (requiredRole && requiredRole !== userRole) {
      // Học viên không được vào trang của giáo viên
      if (userRole === 'hoc_vien') {
        next('/hoc-vien');
        return;
      }
      // Giáo viên không được vào trang của học viên
      if (userRole === 'giao_vien') {
        next('/ho-so-giang-vien');
        return;
      }
      next('/');
      return;
    }
  }

  next();
});

export default router;
