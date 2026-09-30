/**
 * Service: Lịch dạy giáo viên
 * Endpoint: /api/giao-vien/lich-day
 */
import http from './http'

export const lichDayService = {
  /**
   * Lấy lịch dạy của GV đang đăng nhập
   * @param {Object} params { from_date, to_date, tinh_trang, id_mon_hoc }
   */
  getLichDay(params = {}) {
    return http.get('/giao-vien/lich-day', { params })
  },
}
