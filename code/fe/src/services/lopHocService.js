/**
 * Service: Lớp học (Giáo viên + Public + Admin)
 *
 * Giáo viên:
 *  - GET    /api/giao-vien/lop-hoc
 *  - POST   /api/giao-vien/lop-hoc
 *  - GET    /api/giao-vien/lop-hoc/{id}
 *  - PUT    /api/giao-vien/lop-hoc/{id}
 *  - DELETE /api/giao-vien/lop-hoc/{id}
 *  - POST   /api/giao-vien/lop-hoc/{id}/duyet-hoc-vien
 *
 * Public:
 *  - GET /api/client/lop-hoc/data
 *  - GET /api/client/lop-hoc/{id}
 *  - GET /api/client/mon-hoc
 *  - GET /api/client/phong-hoc
 *
 * Admin:
 *  - GET  /api/admin/lop-hoc
 *  - POST /api/admin/lop-hoc/duyet
 */
import http from './http'

export const lopHocService = {
  // ============ GIÁO VIÊN ============
  getDanhSachLop() {
    return http.get('/giao-vien/lop-hoc')
  },

  getChiTietLop(id) {
    return http.get(`/giao-vien/lop-hoc/${id}`)
  },

  taoLop(data) {
    return http.post('/giao-vien/lop-hoc', data)
  },

  capNhatLop(id, data) {
    return http.put(`/giao-vien/lop-hoc/${id}`, data)
  },

  huyLop(id) {
    return http.delete(`/giao-vien/lop-hoc/${id}`)
  },

  duyetHocVien(idLopHoc, payload) {
    return http.post(`/giao-vien/lop-hoc/${idLopHoc}/duyet-hoc-vien`, payload)
  },

  // ============ PUBLIC ============
  getPublicLop(params = {}) {
    return http.get('/client/lop-hoc/data', { params })
  },

  getPublicChiTiet(id) {
    return http.get(`/client/lop-hoc/${id}`)
  },

  getMonHoc() {
    return http.get('/client/mon-hoc')
  },

  getPhongHoc() {
    return http.get('/client/phong-hoc')
  },

  // ============ ADMIN ============
  adminGetDanhSachLop(params = {}) {
    return http.get('/admin/lop-hoc', { params })
  },

  adminDuyetLop(payload) {
    return http.post('/admin/lop-hoc/duyet', payload)
  },

  adminThongKe() {
    return http.get('/admin/thong-ke')
  },
}
