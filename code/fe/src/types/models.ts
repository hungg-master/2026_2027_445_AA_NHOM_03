/**
 * =====================================================================
 * EDU LINK / SMART TRIAL — DATA MODELS & TYPES
 * Chuẩn hóa theo bản vẽ ERD đã sửa lỗi (Đồ án Nhóm 3)
 * =====================================================================
 */

// ---------------------------------------------------------------------
// ENUMS & CONSTANT TYPES
// ---------------------------------------------------------------------

export type TrangThaiDuyet = 'cho_duyet' | 'da_duyet' | 'tu_choi';

export type LoaiLop = 'dai_tra' | 'kem';

export type HinhThucLop = 'online' | 'offline';

export type TrangThaiLopHoc = 'sap_mo' | 'dang_mo' | 'dang_hoc' | 'da_ket_thuc' | 'da_huy';

export type TrangThaiDangKy = 'cho_thanh_toan' | 'da_thanh_toan' | 'da_xac_nhan' | 'da_huy';

export type TrangThaiFaceId = 'cho_xac_thuc' | 'thanh_cong' | 'that_bai';

export type LoaiNguoiDungRanh = 'giao_vien' | 'hoc_vien';

export type LoaiThongBao = 'hoc_phi' | 'buoi_hoc';

export type TrangThaiThanhToan = 'cho_xu_ly' | 'thanh_cong' | 'that_bai' | 'hoan_tien';

export type PhuongThucThanhToan = 'stripe' | 'vnpay' | 'momo' | 'chuyen_khoan';

// ---------------------------------------------------------------------
// 1. ADMIN (Quản trị viên)
// ---------------------------------------------------------------------
export interface Admin {
  id: string; // UUID
  email: string;
  mat_khau?: string; // Ẩn khi query client
  ho_ten: string;
  so_dien_thoai?: string | null;
}

// ---------------------------------------------------------------------
// 2. PHÒNG HỌC (Dành cho lớp Offline)
// ---------------------------------------------------------------------
export interface PhongHoc {
  id: string; // UUID
  so_phong: string; // e.g. "P.302"
  dia_chi: string; // e.g. "Cơ sở 1 - 254 Nguyễn Văn Linh, Đà Nẵng"
  mo_ta?: string | null;
}

// ---------------------------------------------------------------------
// 3. MÔN HỌC
// ---------------------------------------------------------------------
export interface MonHoc {
  id: string; // UUID
  ten_mon_hoc: string; // e.g. "Toán học 12", "IELTS 6.5+"
  mo_ta?: string | null;
  lop?: string | null; // Cấp lớp / Trình độ: "Lớp 10", "Lớp 12", "Đại học"
  tinh_trang?: string | null; // "hoat_dong", "tam_ngung"
}

// ---------------------------------------------------------------------
// 4. HỌC VIÊN
// ---------------------------------------------------------------------
export interface HocVien {
  id: string; // UUID
  ho_ten: string;
  email: string;
  mat_khau?: string;
  so_dien_thoai?: string | null;
  ngay_sinh?: string | null; // ISO Date YYYY-MM-DD
  gioi_tinh?: 'nam' | 'nu' | 'khac' | string | null;
  dia_chi?: string | null;
}

// ---------------------------------------------------------------------
// 5. GIÁO VIÊN
// (ĐÃ SỬA: BỎ id_lop_hoc — 1 giáo viên phụ trách nhiều lớp)
// ---------------------------------------------------------------------
export interface GiaoVien {
  id: string; // UUID
  ho_ten: string;
  email: string;
  mat_khau?: string;
  so_dien_thoai?: string | null;
  ngay_sinh?: string | null;
  gioi_tinh?: 'nam' | 'nu' | 'khac' | string | null;
  so_nam_kinh_nghiem?: number | null;
  chuc_danh?: string | null; // e.g. "Thạc sĩ Toán học", "IELTS 8.5"
  giao_vien_da_duyet?: string | null; // UUID references Admin
  trang_thai_duyet: TrangThaiDuyet;

  // Quan hệ (optional khi populate / join)
  admin_duyet?: Admin;
  danh_sach_lop?: LopHoc[];
}

// ---------------------------------------------------------------------
// 6. LỚP HỌC
// (ĐÃ SỬA: id_giao_vien, id_mon_hoc chỉ là FK; BỎ id_hoc_vien)
// ---------------------------------------------------------------------
export interface LopHoc {
  id: string; // UUID (PK duy nhất)
  id_giao_vien: string; // UUID (FK -> giao_viens.id)
  id_mon_hoc: string; // UUID (FK -> mon_hocs.id)
  id_phong_hoc?: string | null; // UUID (FK -> phong_hocs.id, null khi online)
  loai_lop: LoaiLop; // 'dai_tra' (lớp lớn) | 'kem' (1 kèm 1 hoặc tối đa 5 người)
  hinh_thuc: HinhThucLop; // 'online' | 'offline'
  link_online?: string | null; // Link Google Meet / Zoom khi hinh_thuc = 'online'
  hoc_phi: number; // Đơn giá học phí (VND)
  si_so_toi_da: number; // Lớp kèm tối đa 5 người, đại trà 20-30 người
  thoi_gian_bat_dau: string; // ISO Timestamp
  thoi_gian_ket_thuc: string; // ISO Timestamp
  tinh_trang: TrangThaiLopHoc | string;

  // Quan hệ
  giao_vien?: GiaoVien;
  mon_hoc?: MonHoc;
  phong_hoc?: PhongHoc;
  danh_sach_dang_ky?: DangKyLop[];
}

// ---------------------------------------------------------------------
// 7. ĐĂNG KÝ LỚP HỌC
// ---------------------------------------------------------------------
export interface DangKyLop {
  id: string; // UUID (PK)
  id_lop_hoc: string; // UUID (FK -> lop_hocs.id)
  id_hoc_vien: string; // UUID (FK -> hoc_viens.id)
  ngay_dang_ky: string; // ISO Timestamp
  trang_thai: TrangThaiDangKy | string;

  // Quan hệ
  lop_hoc?: LopHoc;
  hoc_vien?: HocVien;
  thanh_toan?: ThanhToan;
  xac_thuc_face_id?: XacThucFaceId;
}

// ---------------------------------------------------------------------
// 8. XÁC THỰC FACE ID (Dành cho lớp Online / Điểm danh)
// ---------------------------------------------------------------------
export interface XacThucFaceId {
  id: string; // UUID (PK)
  id_dang_ky: string; // UUID (FK -> dang_ky_lops.id, UNIQUE)
  id_hoc_vien: string; // UUID (FK -> hoc_viens.id)
  thoi_gian_xac_thuc?: string | null; // ISO Timestamp
  trang_thai: TrangThaiFaceId | string;

  // Quan hệ
  dang_ky?: DangKyLop;
  hoc_vien?: HocVien;
}

// ---------------------------------------------------------------------
// 9. THỜI GIAN RẢNH (Phục vụ thuật toán gợi ý ghép lịch)
// (ĐÃ SỬA: Có loai_nguoi_dung và FK rõ ràng cho từng loại người dùng)
// ---------------------------------------------------------------------
export interface ThoiGianRanh {
  id: string; // UUID (PK)
  loai_nguoi_dung: LoaiNguoiDungRanh; // 'giao_vien' | 'hoc_vien'
  id_giao_vien?: string | null; // UUID (FK -> giao_viens.id, khi loai_nguoi_dung = 'giao_vien')
  id_hoc_vien?: string | null; // UUID (FK -> hoc_viens.id, khi loai_nguoi_dung = 'hoc_vien')
  ngay_trong_tuan: number; // 0: Chủ nhật, 1: Thứ 2, ..., 6: Thứ 7
  thoi_gian_bat_dau: string; // Format: "HH:mm:ss" ví dụ "18:00:00"
  thoi_gian_ket_thuc: string; // Format: "HH:mm:ss" ví dụ "20:00:00"
  trang_thai?: string | null; // 'hoat_dong', 'tam_dung'

  // Quan hệ
  giao_vien?: GiaoVien;
  hoc_vien?: HocVien;
}

// ---------------------------------------------------------------------
// 10. THÔNG BÁO
// ---------------------------------------------------------------------
export interface ThongBao {
  id: string; // UUID (PK)
  id_hoc_vien: string; // UUID (FK -> hoc_viens.id)
  loai_thong_bao: LoaiThongBao; // 'hoc_phi' | 'buoi_hoc'
  noi_dung: string;
  da_gui: boolean;
  thoi_gian_gui?: string | null; // ISO Timestamp

  // Quan hệ
  hoc_vien?: HocVien;
}

// ---------------------------------------------------------------------
// 11. THANH TOÁN HỌC PHÍ
// ---------------------------------------------------------------------
export interface ThanhToan {
  id: string; // UUID (PK)
  id_dang_ky: string; // UUID (FK -> dang_ky_lops.id)
  so_tien: number; // DECIMAL(12, 2)
  trang_thai: TrangThaiThanhToan;
  phuong_thuc?: PhuongThucThanhToan | string | null;
  ma_giao_dich?: string | null;
  thoi_gian_thanh_toan?: string | null; // ISO Timestamp

  // Quan hệ
  dang_ky?: DangKyLop;
}
