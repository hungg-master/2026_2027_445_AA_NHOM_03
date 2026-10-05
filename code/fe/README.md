# SmartTrial — frontend

Vue 3, Vue Router, Vite và Axios. Hướng dẫn backend, biến môi trường và cấu hình dịch vụ nằm trong [README chung](../README.md).

Chạy tại thư mục `code/fe`:

```sh
npm ci
npm run dev
npm test
npm run build
```

`VITE_API_BASE_URL` mặc định là `http://localhost:8000/api`. Camera cần localhost hoặc HTTPS và quyền của trình duyệt. Mô hình Face ID và SDK video được tải khi sử dụng.

Giao diện dùng API cho hồ sơ, lịch rảnh, gợi ý giáo viên, học thử, đăng ký lớp, lịch buổi học, hoàn tất buổi học, đánh giá, tư vấn, AI và danh mục quản trị. Face ID gửi descriptor để máy chủ đối sánh và cấp bằng chứng có hạn theo thao tác; trình duyệt không giữ mẫu chuẩn. Luồng này chưa có kiểm tra chống giả mạo khuôn mặt.

Học phí hiện chạy ở chế độ TEST: yêu cầu thanh toán thành công chờ quản trị viên đối soát, chưa tự gạch nợ và chưa có VietQR/cổng thu tiền thật. Đăng nhập dùng tài khoản nội bộ; chưa có đăng nhập Google/Facebook. Tài khoản ngân hàng chỉ hiển thị số đã che.

Kiểm thử frontend bao gồm hành vi, hợp đồng API và kết xuất HTML các màn hình Vue. Kết xuất HTML không kiểm tra được camera, phát âm thanh, hai thiết bị video, email giao nhận hay AI thật; các phần này cần dịch vụ đã cấu hình và thử trực tiếp trên trình duyệt. Thiếu dịch vụ hoặc lỗi mạng được hiển thị thành lỗi, không chuyển thành kết quả thành công.
