# SmartTrial — Nền tảng đặt lịch học thử & tư vấn trực tuyến real-time

> Nền tảng đặt lịch học thử và tư vấn trực tuyến real-time với cơ chế xếp lịch thông minh (Face ID điểm danh AI, xếp lịch tương thích theo thời gian rảnh, thanh toán học phí VietQR tự động).

---

## 🌟 Các chức năng chính

1. **Đăng ký tài khoản học sinh / giảng viên (`/dang-ky`)**:
   - Giao diện 2 cột hiện đại, hỗ trợ đăng ký tài khoản nhanh, đăng ký qua Google & Facebook.
2. **Xếp lịch học thử thông minh (`/my-schedule`)**:
   - Bảng thời gian (Calendar Grid) linh hoạt cho phép học sinh chọn khung giờ rảnh.
   - Hệ thống gợi ý gia sư / giảng viên phù hợp nhất dựa trên các khung giờ đã chọn.
3. **Quản lý lớp học & buổi học (`/my-classes`)**:
   - Theo dõi các buổi học sắp diễn ra (Offline / Online room).
   - Thống kê chỉ số sĩ số học sinh, số buổi trong tuần và các thao tác quản trị nhanh.
4. **Xác thực danh tính Face ID (`/face-id`)**:
   - Hệ thống điểm danh tự động bằng AI và nhận diện khuôn mặt sinh trắc học trước khi vào phòng học.
   - Kiểm tra điều kiện thiết bị (Webcam, Mic, Internet Ping).
5. **Thanh toán học phí trực tuyến (`/thanh-toan`)**:
   - Tích hợp cổng thanh toán VietQR Napas 247 gạch nợ tự động, thẻ ngân hàng và ví điện tử.
   - Báo cáo chi tiết học phí, áp dụng học bổng khuyến khích và xuất hóa đơn điện tử e-Invoice.

---

## 🛠 Công nghệ sử dụng

- **Frontend**: Vue 3 (Options API), Vite, Vue Router 4, Axios
- **Giao diện**: Bootstrap 5, FontAwesome 6, Boxicons, Custom Scoped CSS
- **Kiến trúc Layout**: Dynamic layout (`blank-layout` & `default-layout`)

---

## 🚀 Cài đặt & Khởi chạy

```bash
# Cài đặt thư viện
npm install

# Chạy server phát triển
npm run dev

# Đóng gói sản phẩm (Build)
npm run build
```
