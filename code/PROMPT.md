# 🎯 PROMPT CHUẨN - TÍNH NĂNG LỊCH DẠY / LỊCH HỌC / QUẢN LÝ LỚP HỌC

> **Dự án**: Nền tảng đặt lịch học thử + tư vấn real-time với cơ chế xếp lịch thông minh
> **Ngày tạo**: 15/09/2026
> **Nhóm**: 3

---

## 1. BỐI CẢNH DỰ ÁN

- **Backend (BE)**: Laravel 11, Sanctum auth, routes tại `be/routes/api.php`, controllers tại `be/app/Http/Controllers/`
- **Frontend (FE)**: Vue 3 + Vue Router, components tại `fe/src/components/EduLink/`, models tại `fe/src/types/models.ts`
- **Database**: Postgres/Supabase (schema đã có 13 bảng chuẩn tại `context/database.sql`)
- **3 vai trò**: Admin, Giáo viên, Học viên

---

## 2. CÁC BẢNG DATABASE LIÊN QUAN (ĐÃ CÓ SẴN)

| Bảng | Trường quan trọng |
|------|------------------|
| `giao_viens` | id, ho_ten, email, trang_thai_duyet, giao_vien_da_duyet |
| `hoc_viens` | id, ho_ten, email |
| `mon_hocs` | id, ten_mon_hoc, lop |
| `phong_hocs` | id, so_phong, dia_chi |
| `lop_hocs` | id, id_giao_vien, id_mon_hoc, id_phong_hoc, loai_lop ('dai_tra'\|'kem'), hinh_thuc ('online'\|'offline'), link_online, hoc_phi, si_so_toi_da, **thoi_gian_bat_dau, thoi_gian_ket_thuc**, tinh_trang |
| `dang_ky_lops` | id, id_lop_hoc, id_hoc_vien, ngay_dang_ky, trang_thai |
| `thoi_gian_ranhs` | id, loai_nguoi_dung ('giao_vien'\|'hoc_vien'), ngay_trong_tuan (0-6), thoi_gian_bat_dau, thoi_gian_ket_thuc |

---

## 3. CHỨC NĂNG CẦN TRIỂN KHAI

### 3.1. LỊCH DẠY GIÁO VIÊN (Teacher Schedule)

Hiển thị tất cả buổi dạy của giáo viên đang đăng nhập, dựa trên `lop_hocs` mà họ phụ trách.

**Yêu cầu chi tiết:**
- Lấy từ bảng `lop_hocs` WHERE `id_giao_vien = {current_teacher_id}`
- Lọc theo: khoảng ngày (từ ngày → đến ngày), trạng thái lớp, môn học
- Mỗi buổi dạy hiển thị:
  - Tên lớp (từ `mon_hocs.ten_mon_hoc`)
  - Thời gian: `thoi_gian_bat_dau` → `thoi_gian_ket_thuc`
  - Hình thức: online/offline
  - Nếu offline: số phòng + địa chỉ (JOIN `phong_hocs`)
  - Nếu online: `link_online`
  - Sĩ số hiện tại / tối đa (đếm từ `dang_ky_lops` WHERE trang_thai='da_duyet')
- Hai view: **Tuần** (mặc định) và **Tháng**
- Click vào buổi → popup chi tiết + danh sách học viên trong lớp

### 3.2. LỊCH HỌC HỌC VIÊN (Student Schedule)

Hiển thị tất cả buổi học mà học viên đã đăng ký thành công.

**Yêu cầu chi tiết:**
- Lấy từ `dang_ky_lops` WHERE `id_hoc_vien = {current_student_id}` AND `trang_thai = 'da_duyet'`
- JOIN với `lop_hocs` để lấy thời gian
- JOIN với `giao_viens` để lấy tên GV
- JOIN với `mon_hocs` để lấy tên môn
- Mỗi buổi hiển thị: tên môn, tên GV, thời gian, online link hoặc phòng học
- Trạng thái buổi học (so với thời điểm hiện tại): **đã học** / **đang diễn ra** / **sắp tới**
- Hai view: **Tuần** / **Tháng** / **Danh sách**

### 3.3. QUẢN LÝ LỚP HỌC (Class Management)

#### a. Giáo viên:
- **Tạo lớp mới**: chọn môn học, thời gian bắt đầu/kết thúc, hình thức, phòng/link online, sĩ số tối đa, học phí, loại lớp (đại trà/kèm)
- **Sửa lớp**: chỉnh sửa các trường trên (chỉ khi chưa có HV đăng ký)
- **Xóa lớp**: chuyển `tinh_trang = 'da_huy'`
- **Xem danh sách HV đã đăng ký**: từ `dang_ky_lops`
- **Duyệt/Từ chối HV**: cập nhật `dang_ky_lops.trang_thai`

#### b. Học viên:
- **Xem lớp đã đăng ký**: lấy `dang_ky_lops` của mình
- **Xem trạng thái đăng ký**: chờ duyệt / đã duyệt / bị từ chối / đã hủy
- **Đăng ký lớp mới**: thêm record `dang_ky_lops` với `trang_thai = 'cho_thanh_toan'`
- **Hủy đăng ký**: cập nhật `trang_thai = 'da_huy'`

#### c. Admin:
- **Xem tất cả lớp**: lấy tất cả `lop_hocs`
- **Duyệt/Từ chối lớp mới tạo**: cập nhật `tinh_trang`
- **Thống kê**: tổng số lớp, số học viên, lớp đang hoạt động

---

## 4. API ENDPOINTS (RESTful, prefix `/api`)

### Giáo viên (middleware: `auth:sanctum` + `giao_vien`)

```
GET    /api/giao-vien/lich-day                       — Lịch dạy của tôi (query: from_date, to_date)
GET    /api/giao-vien/lop-hoc                        — Danh sách lớp tôi đang dạy
GET    /api/giao-vien/lop-hoc/{id}                   — Chi tiết 1 lớp + DS học viên
POST   /api/giao-vien/lop-hoc                        — Tạo lớp mới
PUT    /api/giao-vien/lop-hoc/{id}                   — Cập nhật lớp
DELETE /api/giao-vien/lop-hoc/{id}                   — Hủy lớp
POST   /api/giao-vien/lop-hoc/{id}/duyet-hoc-vien    — Duyệt HV (body: id_dang_ky, hanh_dong)
```

### Học viên (middleware: `auth:sanctum` + `hoc_vien`)

```
GET    /api/hoc-vien/lich-hoc                        — Lịch học của tôi (query: from_date, to_date)
GET    /api/hoc-vien/lop-hoc-cua-toi                 — DS lớp đã đăng ký
POST   /api/hoc-vien/dang-ky-lop-hoc/{id_lop_hoc}    — Đăng ký lớp
DELETE /api/hoc-vien/huy-dang-ky/{id_dang_ky}        — Hủy đăng ký
```

### Admin (middleware: `auth:sanctum` + `admin`)

```
GET    /api/admin/lop-hoc                            — Tất cả lớp
GET    /api/admin/lop-hoc/{id}                       — Chi tiết
POST   /api/admin/lop-hoc/duyet                      — Duyệt/Từ chối lớp
GET    /api/admin/thong-ke                           — Thống kê tổng quan
```

### Public (FE dùng cho trang chủ)

```
GET    /api/client/giao-vien/data                    — DS GV đã duyệt (đã có)
GET    /api/client/lop-hoc/data                      — DS lớp đang mở (mới)
GET    /api/client/lop-hoc/{id}                      — Chi tiết lớp public
```

---

## 5. CẤU TRÚC BACKEND CẦN TẠO

```
be/app/Http/Controllers/
├── GiaoVien/
│   ├── LichDayController.php          ← MỚI
│   ├── LopHocController.php           ← MỚI
│   ├── DangKyLopController.php        ← MỚI
│   ├── HocVienController.php          ← CÓ SẴN
│   └── GiaoVienController.php         ← CÓ SẴN
├── HocVien/
│   ├── LichHocController.php          ← MỚI
│   └── DangKyLopController.php        ← MỚI
└── Admin/
    └── LopHocController.php           ← MỚI

be/app/Models/
├── LopHoc.php                         ← MỚI
├── DangKyLop.php                      ← MỚI
├── MonHoc.php                         ← MỚI
├── PhongHoc.php                       ← MỚI
├── ThoiGianRanh.php                   ← MỚI
├── GiaoVien.php                       ← CÓ SẴN
└── HocVien.php                        ← CÓ SẴN
```

### Quy ước response thống nhất:

```json
{
  "status": true,
  "message": "Thành công",
  "data": { ... }
}

// Lỗi:
{
  "status": false,
  "message": "Mô tả lỗi"
}
```

### Validation rules mẫu (Request classes):

- Tạo lớp: `thoi_gian_bat_dau` < `thoi_gian_ket_thuc`
- `si_so_toi_da` tùy `loai_lop`: đại trà ≤ 30, kèm ≤ 5
- `hoc_phi` ≥ 0
- Nếu `hinh_thuc = 'offline'` → bắt buộc có `id_phong_hoc`
- Nếu `hinh_thuc = 'online'` → bắt buộc có `link_online` (URL hợp lệ)

---

## 6. CẤU TRÚC FRONTEND CẦN TẠO

```
fe/src/
├── components/EduLink/
│   ├── LichDayGiaoVien.vue            ← MỚI (route: /giao-vien/lich-day)
│   ├── LichHocHocVien.vue             ← MỚI (route: /hoc-vien/lich-hoc)
│   ├── QuanLyLopHoc.vue               ← MỚI (route: /quan-ly-lop)
│   ├── ChiTietLopHoc.vue              ← MỚI (route: /lop-hoc/{id})
│   ├── MySchedule.vue                 ← CÓ SẴN (mở rộng)
│   └── ClassManagement.vue            ← CÓ SẴN (mở rộng)
├── services/
│   ├── lichDayService.js              ← MỚI (axios gọi API lịch dạy)
│   ├── lichHocService.js              ← MỚI
│   └── lopHocService.js               ← MỚI
└── router/index.js                   ← THÊM route mới
```

---

## 7. UI/UX SPECIFICATIONS

### Trang Lịch Dạy / Lịch Học (chung)

- **Header cố định**: Logo, menu nav, avatar user
- **Bộ lọc trên cùng**: chọn chế độ Tuần/Tháng/Danh sách, datepicker khoảng ngày, select lọc môn học
- **Grid lịch**: 7 cột (T2-CN) × N hàng (slot 30 phút/lần)
- **Mỗi ô buổi học**: card màu (xanh dương = online, xanh lá = offline), tên lớp + giờ
- **Click card** → modal chi tiết: thông tin lớp, GV/HV, nút "Vào lớp" (nếu sắp tới ≤ 15 phút)
- **Responsive**: stack dọc trên mobile

### Trang Quản Lý Lớp (Giáo viên)

- **Header**: nút "Tạo lớp mới" (mở modal form)
- **Table lớp học**: Tên | Môn | Sĩ số | Ngày bắt đầu | Trạng thái | Thao tác
- **Modal tạo/sửa**: form các trường + chọn lịch học cố định (gợi ý lấy từ `thoi_gian_ranhs` của GV)
- **Tab "Học viên"**: bảng DS HV trong lớp, có nút Duyệt/Từ chối

### Trang Lớp Của Tôi (Học viên)

- **2 tab**: "Đang học" / "Đã đăng ký"
- **Card mỗi lớp**: tên môn, tên GV, lịch học, trạng thái thanh toán
- **Nút "Đăng ký lớp mới"**: link sang trang duyệt lớp public

---

## 8. ACCEPTANCE CRITERIA (Tiêu chí nghiệm thu)

### Backend:

1. ✅ Tất cả endpoint trả về JSON đúng chuẩn `{status, message, data}`
2. ✅ Validation đầy đủ, trả lỗi 422 với chi tiết
3. ✅ Middleware Sanctum xác thực cho routes cần đăng nhập
4. ✅ Không trả về `mat_khau` trong response
5. ✅ Có try-catch ở mọi controller method, log lỗi
6. ✅ Có rate limiting cho đăng ký
7. ✅ API test được bằng Postman/Insomnia

### Frontend:

1. ✅ Calendar hiển thị đúng các buổi học/dạy theo tuần/tháng
2. ✅ Click buổi → mở modal chi tiết
3. ✅ Form tạo/sửa lớp validate đầy đủ (frontend + backend)
4. ✅ Loading state và error toast khi gọi API thất bại
5. ✅ UI responsive ở 3 breakpoint (mobile/tablet/desktop)
6. ✅ Hiển thị trạng thái rõ ràng (chờ duyệt/đã duyệt/đã hủy)
7. ✅ Code theo SOLID, có comment tiếng Việt giải thích logic

### Tích hợp:

1. ✅ Database migrations chạy thành công, có seed data mẫu
2. ✅ Học viên đăng ký → Giáo viên duyệt → Hiển thị trong lịch cả 2 bên
3. ✅ Tuân thủ context.md: KHÔNG dùng AI quá vào code, code phải rõ ràng

---

## 9. RÀNG BUỘC TỪ CONTEXT.MD

- Ưu tiên **đơn giản, dễ làm** (team 4 người)
- Không microservice
- Không magic code, dễ đọc
- API rõ ràng, có documentation
- Tuân thủ Clean Architecture (Controller → Service → Model)
- Validation kỹ ở backend (giảm tải frontend)

---

## 10. ENUMS & CONSTANTS (Tham chiếu)

```typescript
// Từ fe/src/types/models.ts
export type TrangThaiDuyet = 'cho_duyet' | 'da_duyet' | 'tu_choi';
export type LoaiLop = 'dai_tra' | 'kem';
export type HinhThucLop = 'online' | 'offline';
export type TrangThaiLopHoc = 'sap_mo' | 'dang_mo' | 'dang_hoc' | 'da_ket_thuc' | 'da_huy';
export type TrangThaiDangKy = 'cho_thanh_toan' | 'da_thanh_toan' | 'da_xac_nhan' | 'da_huy';
```

---

## 11. CHECKLIST TRIỂN KHAI

### Phase 1: Backend Foundation
- [ ] Tạo Models (LopHoc, DangKyLop, MonHoc, PhongHoc, ThoiGianRanh)
- [ ] Tạo Migrations (nếu chưa có trong DB)
- [ ] Tạo Seeders (dữ liệu mẫu)

### Phase 2: Backend APIs
- [ ] Controllers cho Giáo viên (LichDay, LopHoc)
- [ ] Controllers cho Học viên (LichHoc, DangKyLop)
- [ ] Controllers cho Admin (LopHoc, ThongKe)
- [ ] Request Validation classes
- [ ] Middleware phân quyền

### Phase 3: Frontend Services
- [ ] Tạo services (axios gọi API)
- [ ] Cấu hình interceptor + token

### Phase 4: Frontend UI
- [ ] Component LichDayGiaoVien
- [ ] Component LichHocHocVien
- [ ] Component QuanLyLopHoc + Modal form
- [ ] Component ChiTietLopHoc
- [ ] Cập nhật Router

### Phase 5: Tích hợp & Test
- [ ] End-to-end test (HV đăng ký → GV duyệt → hiển thị lịch)
- [ ] Responsive test
- [ ] Edge case: trùng lịch, hết sĩ số, hủy lớp khi đã có HV

---

## 12. TÀI LIỆU THAM KHẢO

- `context/context.md` — Tổng quan dự án & Tech stack
- `context/database.sql` — Schema database chuẩn 13 bảng
- `fe/src/types/models.ts` — TypeScript models chuẩn
- `be/routes/api.php` — Routes hiện tại
- `be/app/Http/Controllers/*` — Pattern controller hiện tại

---

**📌 Lưu ý**: Prompt này đã được chuẩn hóa dựa trên:
- Cấu trúc code Laravel + Vue 3 thực tế của dự án
- Schema database Supabase chuẩn 13 bảng
- Yêu cầu nghiệp vụ từ `context.md`
- Quy ước response `{status, message, data}` của team

> ✅ **Sẵn sàng để đưa vào AI Agent triển khai từng Phase**
