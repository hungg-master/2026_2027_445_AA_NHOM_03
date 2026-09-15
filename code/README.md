# 🎓 EDU LINK — Hệ thống đặt lịch học thử & tư vấn trực tuyến

> **Nhóm 3** — Năm học 2026-2027
> **Backend**: Laravel 11 + Sanctum | **Frontend**: Vue 3 + Vite | **Database**: PostgreSQL/Supabase

---

## 📂 Cấu trúc dự án

```
code/
├── be/                              # Laravel Backend (Port 8000)
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── Admin/LopHocAdminController.php        ← MỚI
│   │   │   ├── Client/LopHocPublicController.php      ← MỚI
│   │   │   ├── GiaoVien/LopHocController.php          ← MỚI
│   │   │   ├── GiaoVien/DuyetHocVienController.php    ← MỚI
│   │   │   ├── HocVien/LichHocController.php          ← MỚI
│   │   │   ├── AdminController.php                    ← CÓ SẴN
│   │   │   ├── GiaoVienController.php                 ← CÓ SẴN
│   │   │   └── HocVienController.php                  ← CÓ SẴN
│   │   ├── Http/Requests/
│   │   │   ├── DangKyLop/DangKyLopRequest.php         ← MỚI
│   │   │   ├── DangKyLop/DuyetHocVienDangKyRequest.php ← MỚI
│   │   │   ├── LopHoc/TaoLopHocRequest.php            ← MỚI
│   │   │   ├── LopHoc/DuyetLopHocRequest.php          ← MỚI
│   │   │   └── ... (Các request khác có sẵn)
│   │   ├── Http/Middleware/{Admin,GiaoVien,HocVien}Middleware.php
│   │   └── Models/
│   │       ├── LopHoc.php           ← MỚI
│   │       ├── DangKyLop.php        ← MỚI
│   │       ├── MonHoc.php           ← MỚI
│   │       ├── PhongHoc.php         ← MỚI
│   │       ├── ThoiGianRanh.php     ← MỚI
│   │       ├── GiaoVien.php         ← CÓ SẴN
│   │       └── HocVien.php          ← CÓ SẴN
│   ├── database/
│   │   ├── migrations/
│   │   │   ├── 2026_09_15_100000_create_mon_hocs_table.php       ← MỚI
│   │   │   ├── 2026_09_15_100001_create_phong_hocs_table.php     ← MỚI
│   │   │   ├── 2026_09_15_100002_create_lop_hocs_table.php       ← MỚI
│   │   │   ├── 2026_09_15_100003_create_dang_ky_lops_table.php   ← MỚI
│   │   │   ├── 2026_09_15_100004_create_thoi_gian_ranhs_table.php ← MỚI
│   │   │   └── (Các migration có sẵn)
│   │   └── seeders/
│   │       ├── DatabaseSeeder.php     ← CẬP NHẬT
│   │       ├── AdminSeeder.php        ← MỚI
│   │       └── MonHocPhongHocSeeder.php ← MỚI
│   └── routes/api.php              ← CẬP NHẬT
│
├── fe/                              # Vue 3 Frontend (Vite dev: Port 5173)
│   └── src/
│       ├── services/                ← MỚI
│       │   ├── http.js                          ← Axios instance + interceptor
│       │   ├── lichDayService.js                ← API lịch dạy GV
│       │   ├── lichHocService.js                ← API lịch học + đăng ký HV
│       │   └── lopHocService.js                 ← API CRUD lớp + public
│       ├── components/EduLink/
│       │   ├── LichDayGiaoVien.vue    ← MỚI (route /giao-vien/lich-day)
│       │   ├── QuanLyLopHoc.vue       ← MỚI (route /giao-vien/quan-ly-lop)
│       │   ├── LichHocHocVien.vue     ← MỚI (route /hoc-vien/lich-hoc)
│       │   ├── LopCuaToi.vue          ← MỚI (route /hoc-vien/lop-cua-toi)
│       │   ├── DanhSachLopPublic.vue  ← MỚI (route /client/danh-sach-lop)
│       │   └── (Các component khác có sẵn)
│       └── router/index.js          ← CẬP NHẬT (thêm 5 routes mới)
│
└── PROMPT.md                       ← Chuẩn prompt dự án
└── README.md                       ← File này
```

---

## 🚀 CÀI ĐẶT & CHẠY

### Bước 1: Backend (Laravel)

```bash
cd be

# 1. Copy file env
cp .env.example .env

# 2. Cài đặt PHP packages
composer install

# 3. Tạo app key
php artisan key:generate

# 4. Cấu hình DB trong .env (mặc định SQLite, khuyến nghị chuyển sang MySQL/Postgres)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=edulink
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Chạy migration + seed
php artisan migrate --seed

# 6. Khởi động server (port 8000)
php artisan serve
```

**Tài khoản Admin mặc định:**
- Email: `admin@edulink.vn`
- Password: `admin123`

### Bước 2: Frontend (Vue 3)

```bash
cd fe

# 1. Cài đặt packages
npm install

# 2. Tạo file .env (nếu cần đổi API URL)
echo "VITE_API_BASE_URL=http://localhost:8000/api" > .env

# 3. Chạy dev server (port 5173)
npm run dev
```

Mở trình duyệt: `http://localhost:5173`

---

## 📋 API ENDPOINTS TỔNG QUAN

### 🔐 Admin (prefix: `/api/admin`)
| Method | URL | Mô tả |
|--------|-----|--------|
| POST | `/login` | Đăng nhập admin |
| GET | `/profile` | Lấy thông tin admin |
| GET | `/lop-hoc` | Danh sách tất cả lớp |
| GET | `/lop-hoc/{id}` | Chi tiết lớp |
| POST | `/lop-hoc/duyet` | Duyệt/hủy lớp |
| GET | `/thong-ke` | Thống kê tổng quan |

### 👨‍🏫 Giáo viên (prefix: `/api/giao-vien`)
| Method | URL | Mô tả |
|--------|-----|--------|
| POST | `/register` | Đăng ký (cần admin duyệt) |
| POST | `/login` | Đăng nhập |
| GET | `/lich-day` | Lịch dạy của tôi |
| GET | `/lop-hoc` | Danh sách lớp tôi dạy |
| POST | `/lop-hoc` | Tạo lớp mới |
| GET | `/lop-hoc/{id}` | Chi tiết 1 lớp |
| PUT | `/lop-hoc/{id}` | Cập nhật lớp |
| DELETE | `/lop-hoc/{id}` | Hủy lớp |
| POST | `/lop-hoc/{id}/duyet-hoc-vien` | Duyệt/từ chối HV |

### 🎓 Học viên (prefix: `/api/hoc-vien`)
| Method | URL | Mô tả |
|--------|-----|--------|
| POST | `/register` | Đăng ký |
| POST | `/login` | Đăng nhập |
| GET | `/lich-hoc` | Lịch học của tôi |
| GET | `/lop-hoc-cua-toi` | Danh sách lớp đã đăng ký |
| POST | `/dang-ky-lop-hoc` | Đăng ký lớp |
| DELETE | `/huy-dang-ky/{id}` | Hủy đăng ký |

### 🌐 Public (prefix: `/api/client`)
| Method | URL | Mô tả |
|--------|-----|--------|
| GET | `/lop-hoc/data` | Danh sách lớp đang mở |
| GET | `/lop-hoc/{id}` | Chi tiết lớp |
| GET | `/mon-hoc` | Danh sách môn học |
| GET | `/phong-hoc` | Danh sách phòng học |
| GET | `/giao-vien/data` | Danh sách GV đã duyệt |

---

## 🗺️ ROUTES FRONTEND

| URL | Component | Mô tả |
|-----|-----------|--------|
| `/` | LandingPage | Trang chủ |
| `/dang-ky` | DangKy/index | Đăng ký/Đăng nhập |
| `/giao-vien/lich-day` | LichDayGiaoVien | Lịch dạy (GV) |
| `/giao-vien/quan-ly-lop` | QuanLyLopHoc | Quản lý lớp (GV) |
| `/hoc-vien/lich-hoc` | LichHocHocVien | Lịch học (HV) |
| `/hoc-vien/lop-cua-toi` | LopCuaToi | Lớp của tôi (HV) |
| `/client/danh-sach-lop` | DanhSachLopPublic | Khám phá lớp học |
| `/ho-so-giang-vien` | TeacherDashboard | Hồ sơ GV |
| `/ho-so-hoc-vien` | StudentProfile | Hồ sơ HV |

---

## 🧪 TEST NHANH

### 1. Tạo dữ liệu mẫu

```bash
cd be
php artisan migrate --seed
```

Sẽ tạo:
- 1 Admin (`admin@edulink.vn / admin123`)
- 8 Môn học
- 4 Phòng học

### 2. Test API bằng cURL

```bash
# Đăng nhập admin
curl -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@edulink.vn","mat_khau":"admin123"}'

# Lấy danh sách lớp public
curl http://localhost:8000/api/client/lop-hoc/data

# Lấy danh sách môn học
curl http://localhost:8000/api/client/mon-hoc
```

### 3. Flow Test End-to-End

1. **GV đăng ký** → `/api/giao-vien/register`
2. **Admin duyệt GV** → `/api/admin/giao-vien/duyet` (body: id, trang_thai_duyet=da_duyet)
3. **GV tạo lớp** → `/api/giao-vien/lop-hoc` (Bearer token)
4. **HV đăng ký** → `/api/hoc-vien/dang-ky-lop-hoc`
5. **GV duyệt HV** → `/api/giao-vien/lop-hoc/{id}/duyet-hoc-vien`
6. **Xem lịch**:
   - GV: `/api/giao-vien/lich-day`
   - HV: `/api/hoc-vien/lich-hoc`

---

## 📐 KIẾN TRÚC & NGUYÊN TẮC

- **Clean Architecture**: Controller → Request → Model (không có Service layer riêng để đơn giản)
- **SOLID**: Mỗi Controller chỉ quản lý 1 resource
- **Validation**: Luôn ở FormRequest (không hard-code trong Controller)
- **Response thống nhất**: `{status: bool, message: string, data?: any}`
- **Error handling**: try-catch + Log::error() ở mọi method
- **JWT/Sanctum**: Token xác thực, không lưu password trong response

---

## 🎯 TÍNH NĂNG ĐÃ TRIỂN KHAI

| Tính năng | Backend | Frontend | Status |
|-----------|---------|----------|--------|
| ✅ Lịch dạy giáo viên (tuần/danh sách) | ✓ | ✓ | Done |
| ✅ Lịch học học viên (tuần/danh sách) | ✓ | ✓ | Done |
| ✅ Quản lý lớp học (CRUD) | ✓ | ✓ | Done |
| ✅ Duyệt/từ chối HV đăng ký | ✓ | ✓ | Done |
| ✅ Lớp của tôi (HV) + hủy đăng ký | ✓ | ✓ | Done |
| ✅ Khám phá lớp public + đăng ký | ✓ | ✓ | Done |
| ✅ Thống kê admin | ✓ | - | Done |
| ✅ Soft cancel (chuyển trạng thái) | ✓ | ✓ | Done |
| ✅ Validation si_so_toi_da theo loai_lop | ✓ | ✓ | Done |

---

## 🚧 TÍNH NĂNG TƯƠNG LAI (Chưa làm)

- [ ] Thông báo realtime (Supabase Realtime)
- [ ] Thanh toán học phí (Stripe/VNPay/Momo)
- [ ] Xác thực Face ID
- [ ] Chat giáo viên - học viên
- [ ] AI gợi ý ghép lịch (Groq)
- [ ] Đánh giá buổi học (đã có component, chưa nối API)

---

## 👥 NHÓM PHÁT TRIỂN

**Nhóm 3** - Lớp 445/AA - Năm học 2026-2027

---

## 📞 HỖ TRỢ

Liên hệ admin: `admin@edulink.vn`
