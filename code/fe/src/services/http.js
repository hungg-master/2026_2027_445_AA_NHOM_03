/**
 * =====================================================================
 * EDU LINK — HTTP SERVICE (Axios instance + Interceptors)
 * Service dùng chung cho toàn bộ app, tự động gắn Bearer token.
 * =====================================================================
 */
import axios from 'axios'

// Base URL: dev = http://localhost:8000/api, prod set qua VITE_API_BASE_URL
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

const http = axios.create({
  baseURL: API_BASE_URL,
  timeout: 15000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

// Interceptor: Tự động gắn Bearer Token (nếu có)
http.interceptors.request.use((config) => {
  const token = localStorage.getItem('edulink_token')
  const tokenType = localStorage.getItem('edulink_token_type') || 'giao_vien'
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Interceptor: Xử lý response thống nhất
http.interceptors.response.use(
  (response) => response.data,
  (error) => {
    if (error.response) {
      const { status, data } = error.response
      // Token hết hạn -> về trang đăng nhập
      if (status === 401) {
        localStorage.removeItem('edulink_token')
        localStorage.removeItem('edulink_user')
        localStorage.removeItem('edulink_token_type')
      }
      return Promise.reject(data || { status: false, message: 'Lỗi kết nối!' })
    }
    return Promise.reject({ status: false, message: 'Không thể kết nối server.' })
  }
)

export default http
export { API_BASE_URL }
