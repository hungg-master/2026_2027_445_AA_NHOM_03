/**
 * Service: Quản lý Thông tin tài khoản (Hồ sơ, Avatar, Face ID, Mật khẩu)
 * Hỗ trợ cả Học viên và Giáo viên
 */
import http from './http';

export const profileService = {
  /** Lấy thông tin cá nhân */
  getProfile(role = 'hoc_vien') {
    const prefix = role === 'giao_vien' ? '/giao-vien' : '/hoc-vien';
    return http.get(`${prefix}/profile/data`);
  },

  /** Cập nhật thông tin cá nhân & Avatar */
  updateProfile(role = 'hoc_vien', data = {}) {
    const prefix = role === 'giao_vien' ? '/giao-vien' : '/hoc-vien';
    return http.post(`${prefix}/profile/update`, data);
  },

  /** Đổi mật khẩu */
  changePassword(role = 'hoc_vien', data = {}) {
    const prefix = role === 'giao_vien' ? '/giao-vien' : '/hoc-vien';
    // Đảm bảo gửi cả re_password và password_confirmation
    const payload = {
      ...data,
      re_password: data.password_confirmation || data.re_password,
      password_confirmation: data.password_confirmation || data.re_password
    };
    return http.post(`${prefix}/profile/change-password`, payload);
  },

  /** Lưu vector sinh trắc học Face ID */
  saveFaceIdVector(role = 'hoc_vien', userId, vector) {
    const prefix = role === 'giao_vien' ? '/giao-vien' : '/hoc-vien';
    return http.post(`${prefix}/xac-thuc-khuon-mat`, {
      id: userId,
      du_lieu_khuon_mat: typeof vector === 'string' ? vector : JSON.stringify(vector)
    });
  },

  /** Tải lên ảnh Face ID trực tiếp qua FormData */
  uploadFaceIdPhoto(role = 'hoc_vien', formData) {
    const prefix = role === 'giao_vien' ? '/giao-vien' : '/hoc-vien';
    return http.post(`${prefix}/profile/face-id`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  }
};

export default profileService;
