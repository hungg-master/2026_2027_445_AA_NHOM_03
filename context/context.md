# Project: Nền tảng đặt lịch học thử và tư vấn trực tuyến real-time với cơ chế xếp lịch thông minh

## Techstack:
- Frontend/Backend: Next.js (App Router) + TypeScript
- Database: Supabase(Postgres)
- Face ID: 
- AI Support: Groq
- Authentication: Clerk
- Real-time chat: Supabase Realtime
- Payment: Stripe (test)
- Hosting: Vercel
- CSS: Tailwind

## Architecture:
- Clean architecture
- Không microservice
- AI hook từ API Groq
- Ưu tiên đơn giản, dễ làm

## Core Concept:
- Role: Giáo viên, Học sinh và Admin
- User chia thành 2 bên (học sinh và giáo viên)
- Giáo viên có thể quản lý lịch, lớp của mình sao cho phù hợp
- Giáo viên đăng ký tài khoản ngân hàng qua website
- Học sinh có thể tự tinh chỉnh lịch bản thân để dễ dàng tìm kiếm giáo viên phù hợp
- Hỗ trợ lớp đại trà và kèm cặp
- Giáo viên cần được xác nhận bởi admin
- Yêu cầu face-id để sử dụng các chức năng chính như đăng ký môn, đăng ký với tư cách giáo viên,...
- Hỗ trợ chức năng trao đổi riêng cho các học sinh, giáo viên.
- Hỗ trợ tự động thông báo các flag như chưa nộp học phí,...

## Feature:
### Đối với giáo viên:
- Tạo, chỉnh sửa, xóa lớp học
- Sắp xếp lịch học & làm việc
- Quản lý học viên trong lớp
- Nhắn tin với học viên, giáo viên và AI hỗ trợ của hệ thống
- Liên kết tài khoản ngân hàng với hệ thống
- Chỉnh sửa tài khoản, thông tin cá nhân
### Đối với học viên:
- Sắp xếp lịch học của bản thân
- Xem, sửa và ngừng đăng ký với những lớp học
- Xem trang giáo viên
- Tìm kiếm giáo viên theo môn học, lịch học
- Đăng ký môn học mới
- Nhắn tin với giáo viên, học viên và AI hỗ trợ của hệ thống
- Nhận thông báo về học phí, lịch học
- Chỉnh sửa tài khoản, thông tin cá nhân

## Constraints:
- Còn thiếu kiến thức nhiều
- Team chỉ có 4 người -> thiếu nhân lực
- Face-ID Validation là đề tài mới, chưa ai chạm vào
- Ưu tiên tồn tại những chức năng core trước như đăng ký môn, quản lý lớp,...

## Requirements:
- Bổ sung kiến thức thiếu gấp
- Không bị mất liên lạc
- Không magic code
- Không lạm dụng AI quá vào code
- API rõ ràng