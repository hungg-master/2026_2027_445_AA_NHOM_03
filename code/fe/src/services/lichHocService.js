/**
 * Service: Lịch học + Lớp học của học viên
 * Endpoints:
 *  - GET  /api/hoc-vien/lich-hoc
 *  - GET  /api/hoc-vien/lop-hoc-cua-toi
 *  - POST /api/hoc-vien/dang-ky-lop-hoc
 *  - DELETE /api/hoc-vien/huy-dang-ky/{id}
 */
import http from './http'

export const lichHocService = {
  /** Lấy lịch học theo khoảng ngày */
  getLichHoc(params = {}) {
    return http.get('/hoc-vien/lich-hoc', { params })
  },

  /** Lấy danh sách lớp đã đăng ký */
  getLopHocCuaToi() {
    return http.get('/hoc-vien/lop-hoc-cua-toi')
  },

  /** Đăng ký lớp mới */
  dangKyLop(idLopHoc) {
    return http.post('/hoc-vien/dang-ky-lop-hoc', { id_lop_hoc: idLopHoc })
  },

  /** Hủy đăng ký */
  huyDangKy(id) {
    return http.delete(`/hoc-vien/huy-dang-ky/${id}`)
  },
}
