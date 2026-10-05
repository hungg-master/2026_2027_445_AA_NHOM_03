import test from 'node:test';
import assert from 'node:assert/strict';
import { createSupportService } from '../src/services/supportContract.js';

test('test payment records the requested outcome and key without claiming paid status', async () => {
  let payload;
  const service = createSupportService({ post: async (url, data) => { payload = { url, data }; return { status: true, data: { status: 'awaiting_manual' } }; } });
  const result = await service.payment(8, 'stable-key', 'success');
  assert.equal(result.data.status, 'awaiting_manual');
  assert.deepEqual(payload, { url: '/hoc-vien/hoc-phi/8/test-payment', data: { idempotency_key: 'stable-key', outcome: 'success' } });
});
test('a failed message response stays a failure and does not manufacture a sent message', async () => {
  const service = createSupportService({ post: async () => ({ status: false, message: 'Không có quyền hội thoại.' }) });
  await assert.rejects(service.sendMessage(8, 'hello'), /Không có quyền/);
});
test('password reset sends selected account type and one-time token', async () => {
  let payload;
  const service = createSupportService({ post: async (url, data) => { payload = { url, data }; return { status: true }; } });
  await service.reset({ role: 'giao_vien', email: 'teacher@example.test', token: 'one-time', password: 'new-password', password_confirmation: 'new-password' });
  assert.equal(payload.url, '/auth/reset-password'); assert.equal(payload.data.role, 'giao_vien'); assert.equal(payload.data.token, 'one-time');
});
