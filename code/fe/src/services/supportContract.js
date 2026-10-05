import { accepted, rolePrefix } from './productContract.js';
export function createSupportService(http) {
  return {
    tuition: () => accepted(http.get('/hoc-vien/hoc-phi')),
    payment: (id, key, outcome) => accepted(http.post(`/hoc-vien/hoc-phi/${id}/test-payment`, { idempotency_key: key, outcome })),
    transactions: () => accepted(http.get('/admin/thanh-toan')),
    settle: (id, key, reference) => accepted(http.post(`/admin/thanh-toan/${id}/settle-test`, { idempotency_key: key, reference })),
    banks: () => accepted(http.get('/giao-vien/ngan-hang')),
    bank: data => accepted(http.post('/giao-vien/ngan-hang', data)),
    reviews: role => accepted(http.get(`${rolePrefix(role)}/danh-gia`)),
    review: data => accepted(http.post('/hoc-vien/danh-gia', data)),
    finance: role => accepted(http.get(`${rolePrefix(role)}/thong-ke-tai-chinh`)),
    forgot: data => accepted(http.post('/auth/forgot-password', data)),
    reset: data => accepted(http.post('/auth/reset-password', data)),
    conversations: () => accepted(http.get('/chat/conversations')),
    contacts: () => accepted(http.get('/chat/contacts')),
    conversation: data => accepted(http.post('/chat/conversations', data)),
    messages: (id, afterId = 0) => accepted(http.get(`/chat/conversations/${id}/messages`, { params: { after_id: afterId } })),
    sendMessage: (id, body) => accepted(http.post(`/chat/conversations/${id}/messages`, { body })),
    assist: data => accepted(http.post('/ai/assist', data)),
    catalog: kind => accepted(http.get(`/admin/${kind}`)),
    saveCatalog: (kind, id, data) => accepted(id ? http.put(`/admin/${kind}/${id}`, data) : http.post(`/admin/${kind}`, data)),
    deleteCatalog: (kind, id) => accepted(http.delete(`/admin/${kind}/${id}`)),
  };
}
