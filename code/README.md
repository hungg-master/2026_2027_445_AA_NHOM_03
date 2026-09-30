# Chạy EduLink tại máy local

## Backend

Trong thư mục `be`, cài dependency bằng `composer install`, cấu hình `.env`, rồi chạy:

```sh
php artisan migrate
php artisan serve
```

Tạo admin bằng terminal (lệnh hỏi họ tên và mật khẩu tối thiểu 12 ký tự):

```sh
php artisan edulink:create-admin admin@example.com
```

API tạo admin mặc định đã được bỏ. Không có mật khẩu admin mặc định trong luồng tạo mới.

## Frontend

Trong thư mục `fe`, chạy `npm install`, rồi `npm run dev`.
Có thể sao chép `.env.example` thành `.env` để đặt `VITE_API_BASE_URL`.
Giá trị mặc định là `http://localhost:8000/api`.

## Kiểm tra

- Backend: `php artisan test` (database SQLite trong bộ nhớ).
- Frontend: `npm test` và `npm run build`.

## Phạm vi chức năng

- Yêu cầu học thử được lưu trong `trial_bookings`, trạng thái `pending`. Chưa có tự động ghép giáo viên hoặc gửi thông báo xác nhận.
- Ảnh Face ID được lưu trên disk `local` riêng tư. Cần chạy migration thêm `face_id_photo_path` cho học viên và giáo viên. Upload ảnh chưa phải xác thực khuôn mặt.
- Thanh toán và đánh giá buổi học chưa có backend xử lý; các màn hình này còn dữ liệu minh họa.
