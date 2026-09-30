export const API_BASE = (import.meta.env?.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(/\/$/, '');

export async function logout() {
  const token = localStorage.getItem('token');
  const role = localStorage.getItem('role');
  const prefix = { hoc_vien: 'hoc-vien', giao_vien: 'giao-vien', admin: 'admin' }[role];
  if (token) {
    if (!prefix) throw new Error('Không xác định được tài khoản để đăng xuất.');
    const response = await fetch(`${API_BASE}/${prefix}/logout`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    // A 401 means the token is already invalid.
    if (!response.ok && response.status !== 401) {
      throw new Error('Chưa đăng xuất được. Vui lòng thử lại.');
    }
  }
  for (const key of ['token', 'role', 'user']) localStorage.removeItem(key);
}
