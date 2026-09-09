import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save

const routes = [
  {
    path: "/",
    redirect: "/my-schedule",
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
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

export default router;
