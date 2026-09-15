import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save

const routes = [
  {
    path: "/",
    name: "trang-chu",
    component: () => import("../components/EduLink/LandingPage.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/home",
    redirect: "/"
  },
  {
    path: "/trang-chu",
    redirect: "/"
  },
  {
    path: "/landing-page",
    redirect: "/"
  },
  {
    path: "/dang-ky",
    name: "dang-ky",
    component: () => import("../components/DangKy/index.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/register",
    redirect: "/dang-ky"
  },
  {
    path: "/my-schedule",
    name: "my-schedule",
    component: () => import("../components/EduLink/MySchedule.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/schedule",
    redirect: "/my-schedule"
  },
  {
    path: "/my-classes",
    name: "my-classes",
    component: () => import("../components/EduLink/ClassManagement.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/classes",
    redirect: "/my-classes"
  },
  {
    path: "/face-id",
    name: "face-id",
    component: () => import("../components/EduLink/FaceIdVerification.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/xac-thuc-face-id",
    redirect: "/face-id"
  },
  {
    path: "/thanh-toan",
    name: "thanh-toan",
    component: () => import("../components/EduLink/ThanhToanHocPhi.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/hoc-phi",
    redirect: "/thanh-toan"
  },
  {
    path: "/giao-vien",
    name: "giao-vien",
    component: () => import("../components/EduLink/TeacherProfile.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/teacher-profile",
    redirect: "/giao-vien"
  },
  {
    path: "/hoc-vien",
    name: "hoc-vien",
    component: () => import("../components/EduLink/StudentProfile.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/student-profile",
    redirect: "/hoc-vien"
  },
  {
    path: "/ho-so-hoc-vien",
    redirect: "/hoc-vien"
  },
  {
    path: "/ho-so-giang-vien",
    name: "ho-so-giang-vien",
    component: () => import("../components/EduLink/TeacherDashboard.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/teacher-dashboard",
    redirect: "/ho-so-giang-vien"
  },
  {
    path: "/giao-vien-profile",
    redirect: "/ho-so-giang-vien"
  },
  {
    path: "/ho-so-giao-vien",
    redirect: "/ho-so-giang-vien"
  },
  {
    path: "/danh-gia",
    name: "danh-gia",
    component: () => import("../components/EduLink/LessonReview.vue"),
    meta: { layout: "blank" }
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
    meta: { layout: "blank" }
  },
  {
    path: "/giao-vien/quan-ly-lop",
    name: "giao-vien-quan-ly-lop",
    component: () => import("../components/EduLink/QuanLyLopHoc.vue"),
    meta: { layout: "blank" }
  },

  // Học viên
  {
    path: "/hoc-vien/lich-hoc",
    name: "hoc-vien-lich-hoc",
    component: () => import("../components/EduLink/LichHocHocVien.vue"),
    meta: { layout: "blank" }
  },
  {
    path: "/hoc-vien/lop-cua-toi",
    name: "hoc-vien-lop-cua-toi",
    component: () => import("../components/EduLink/LopCuaToi.vue"),
    meta: { layout: "blank" }
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

export default router;
