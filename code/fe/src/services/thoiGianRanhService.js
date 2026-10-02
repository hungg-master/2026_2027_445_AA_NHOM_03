/**
 * Service: Quản lý Lịch rảnh (Thời gian rảnh)
 * Hỗ trợ cả Học viên và Giáo viên
 */
import http from './http';

export const thoiGianRanhService = {
  /**
   * Lấy danh sách lịch rảnh
   * @param {string} role 'hoc_vien' | 'giao_vien'
   */
  getSchedule(role = 'hoc_vien') {
    const prefix = role === 'giao_vien' ? '/giao-vien' : '/hoc-vien';
    return http.get(`${prefix}/thoi-gian-ranh`);
  },

  /**
   * Cập nhật danh sách lịch rảnh
   * @param {string} role 'hoc_vien' | 'giao_vien'
   * @param {Array} schedules Danh sách khung giờ [{ ngay_trong_tuan, thoi_gian_bat_dau, thoi_gian_ket_thuc }]
   */
  updateSchedule(role = 'hoc_vien', schedules = []) {
    const prefix = role === 'giao_vien' ? '/giao-vien' : '/hoc-vien';
    return http.post(`${prefix}/thoi-gian-ranh`, { schedules });
  },
};

export default thoiGianRanhService;
