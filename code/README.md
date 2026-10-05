# SmartTrial — chạy và kiểm tra dự án

Backend Laravel 12/Sanctum; frontend Vue 3/Vite. PHP >=8.2, Composer, Node.js 22 và npm. Đợt sửa ngày 04/10/2026 được kiểm tra với PHP 8.4 và SQLite. Cài từ `composer.lock` và `package-lock.json`.

## Backend

Trong `code/be`, chạy `composer install`, sao chép `.env.example` thành `.env`, cấu hình database và tạo khóa:

```sh
php artisan key:generate
```

SQLite là cấu hình mẫu mặc định. Với database mới, tạo file trống `database/database.sqlite` nếu chưa tồn tại, rồi chạy:

```sh
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

Seeder mặc định chỉ tạo danh mục; không tạo tài khoản hoặc đặt lại mật khẩu admin. Tạo admin bằng lệnh tương tác đã có:

```sh
php artisan edulink:create-admin admin@example.com
```

Tên lệnh `edulink:create-admin` được giữ để tương thích. Lệnh hỏi họ tên và mật khẩu tối thiểu 12 ký tự. Nếu cần dữ liệu minh họa trên database phát triển riêng:

```sh
php artisan db:seed --class=DemoSeeder
```

DemoSeeder bị chặn trong production. Tài khoản demo chỉ phục vụ thử tại máy phát triển, không dùng khi triển khai.

Với database đã có dữ liệu: sao lưu và thử nâng cấp trên bản sao trước. Các migration trùng ngày 29/09 được giữ như mốc lịch sử không tạo/xóa lại bảng ngày 15/09. Hai migration ngày 04/10 thêm buổi học và các tính năng bổ sung; lớp cũ được chuyển thành một buổi thực tế theo thời gian đã lưu. Cờ điểm danh Face ID cũ do trình duyệt gửi không được coi là bằng chứng hợp lệ. Đã kiểm tra đường nâng cấp schema chuẩn trên SQLite; chưa áp dụng lên database đang sử dụng của nhóm. Không dùng `migrate:fresh` trên dữ liệu cần giữ. Giữ nguyên `APP_KEY` sau khi đã lưu dữ liệu mã hóa.

## Frontend

Trong `code/fe`, sao chép `.env.example` thành `.env` nếu cần đổi API:

```sh
npm ci
npm run dev
```

`VITE_API_BASE_URL` mặc định là `http://localhost:8000/api`. Dùng `http://localhost:5173` cho camera tại máy phát triển; khi triển khai cần HTTPS và quyền camera/micro của trình duyệt.

## Cấu hình dịch vụ

Chỉ lưu khóa bí mật trong `.env` backend:

| Dịch vụ | Biến/thiết lập | Hành vi hiện tại |
|---|---|---|
| Video | `LIVEKIT_URL`, `LIVEKIT_API_KEY`, `LIVEKIT_API_SECRET` | Server ký token theo người dùng và buổi học; thiếu cấu hình trả lỗi 503. |
| Email | Các biến `MAIL_*` | Mẫu dùng `log`, không gửi tới hộp thư thật. Cần cấu hình SMTP để thử giao nhận thực tế. |
| Reset | `FRONTEND_RESET_URL` | Trang đặt lại mật khẩu trên frontend; token có hạn, dùng một lần và gắn loại tài khoản. |
| AI | `AI_CHAT_URL`, `AI_API_KEY`, `AI_MODEL`, `AI_DAILY_LIMIT`, `AI_TIMEOUT_SECONDS` | Endpoint HTTPS tương thích chat completions; thiếu cấu hình trả lỗi, có giới hạn và timeout. |
| Thanh toán | `PAYMENTS_MODE=test` | Khoản thu và giao dịch TEST; admin đối soát theo quyền. Chưa có cổng thu tiền thật/VietQR tự động. |
| Face ID | `FACE_DISTANCE_THRESHOLD=0.5` | So khớp descriptor 128 số tại server, bằng chứng hạn 120 giây/dùng một lần. Cần hiệu chỉnh bằng dữ liệu thật; chưa có kiểm tra chống giả mạo khuôn mặt (liveness). |

Email nghiệp vụ gửi sau khi dữ liệu lưu thành công, có outbox và cơ chế nhận quyền gửi/chống gửi đồng thời. Lệnh nhắc lịch:

```sh
php artisan supporting:remind --hours=24
```

Cấu hình lịch chạy lệnh này theo môi trường triển khai. Có thể còn gửi lặp nếu SMTP đã nhận nhưng tiến trình chết trước khi lưu kết quả; cần nhà cung cấp có cơ chế chống lặp để giải quyết hoàn toàn.

## Luồng nghiệp vụ

- Lớp chính thức giữ tự xác nhận theo UC16 sau kiểm tra quyền, Face ID, sĩ số và lịch. Thanh toán là trạng thái riêng. Học thử có bước giáo viên/admin xác nhận và tạo buổi thực tế.
- Lịch học/lịch dạy/matching/phòng video dùng chung các bản ghi `buoi_hocs`. Chỉ lặp hàng tuần khi có ngày kết thúc rõ ràng.
- Giáo viên chưa được duyệt chỉ quản lý hồ sơ. Server không tin ID/cờ xác thực do trình duyệt tự gửi.
- Giáo viên hoàn tất buổi học sau giờ kết thúc. Offline ghi điểm danh do giáo viên xác nhận; online dùng bản ghi quyền vào phòng đã xác thực. Không gắn nhãn Face ID cho điểm danh offline.
- Thanh toán TEST, tài khoản ngân hàng riêng tư, đánh giá gắn buổi học, danh mục admin, tư vấn có lưu tin nhắn và AI đều có API tương ứng.

## Kiểm tra

Backend trong `code/be`:

```sh
php artisan test
php artisan route:list --path=api
```

Frontend trong `code/fe`:

```sh
npm test
npm run build
```

Test backend dùng database kiểm thử riêng, bao gồm migration, quyền giữa các vai trò, Face ID, đăng ký đồng thời, lịch/matching/học thử, tài chính/reset/chat/AI và email giả lập. Test frontend kiểm tra hành vi thành phần, hợp đồng API và kết xuất HTML. Workflow `.github/workflows/code-checks.yml` chạy hai nhóm kiểm tra khi được đưa lên GitHub.

Kết quả thực chạy và các phần chưa thử thực tế ghi riêng tại `BAO_CAO_HOAN_THIEN_CODE.md`. Kiểm thử tự động không thay thế thử camera, hai thiết bị video, SMTP, AI thật hoặc nâng cấp database triển khai.
