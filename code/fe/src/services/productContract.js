export const rolePrefix = role => {
  const prefix = { hoc_vien: '/hoc-vien', giao_vien: '/giao-vien', admin: '/admin' }[role];
  if (!prefix) throw new Error('Vui lòng đăng nhập đúng loại tài khoản.');
  return prefix;
};
export async function accepted(request) {
  const response = await request;
  if (!response?.status) throw new Error(response?.message || 'Máy chủ chưa xác nhận thao tác.');
  return response;
}
export function createProductService(http) {
  return {
    sample: (role, descriptor, currentPassword) => accepted(http.post(`${rolePrefix(role)}/face-id/sample`, { descriptor, ...(currentPassword ? { current_password: currentPassword } : {}) })),
    verify: (role, descriptor, purpose, target) => accepted(http.post(`${rolePrefix(role)}/face-id/verify`, { descriptor, purpose, ...target })),
    async enroll(id, proof) {
      if (!proof) throw new Error('Cần xác thực Face ID trước khi đăng ký.');
      return accepted(http.post('/hoc-vien/dang-ky-lop-hoc', { id_lop_hoc: id, verification_id: proof }));
    },
    async roomToken(id, proof) {
      if (!id || !proof) throw new Error('Cần chọn buổi học và xác thực Face ID trước khi vào phòng.');
      return accepted(http.post('/phong-hop/tao-token', { id_buoi_hoc: id, verification_id: proof }));
    },
    matching: params => accepted(http.get('/hoc-vien/goi-y-giao-vien', { params })),
    trialRequest: data => accepted(http.post('/hoc-thu', data)),
    trials: role => accepted(http.get(`${rolePrefix(role)}/hoc-thu`)),
    trialAction: (role, id, action) => accepted(http.post(`${rolePrefix(role)}/hoc-thu/${id}/${action}`)),
  };
}
