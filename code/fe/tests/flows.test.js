import test from 'node:test';
import assert from 'node:assert/strict';
import { captureSingleFace, calendarSession, availabilitySlots, sendRoomMessage, attachTrack, disposeRoom } from '../src/services/flowHelpers.js';
import { createProductService } from '../src/services/productContract.js';

const descriptor = Array(128).fill(0.1);
test('face capture refuses missing, multiple and malformed faces without verification', async () => {
  for (const detections of [[], [{ descriptor }, { descriptor }], [{ descriptor: [1] }]]) {
    let now = 0;
    await assert.rejects(captureSingleFace({ detect: async () => detections, now: () => now++, timeout: 2, pause: async () => {} }), /khuôn mặt|Face ID/);
  }
});
test('face capture surfaces detector failures and cancellation', async () => {
  await assert.rejects(captureSingleFace({ detect: async () => { throw new Error('model unavailable'); } }), /model unavailable/);
  await assert.rejects(captureSingleFace({ detect: async () => [{ descriptor }], signal: { aborted: true } }), /hủy/);
  assert.deepEqual(await captureSingleFace({ detect: async () => [{ descriptor }], frames: 2, pause: async () => {} }), descriptor);
});
test('a stalled face detector times out instead of leaving scanning pending forever', { timeout: 200 }, async () => {
  await assert.rejects(captureSingleFace({ detect: () => new Promise(() => {}), timeout: 10 }), /thời gian/);
});
test('cleared availability stays empty and merged intervals cover every displayed slot', () => {
  assert.deepEqual(availabilitySlots([], ['09:00']), []);
  assert.deepEqual(availabilitySlots([{ ngay_trong_tuan: 0, thoi_gian_bat_dau: '09:00:00', thoi_gian_ket_thuc: '11:00:00' }], ['09:00','10:00','11:00']), ['Sun_09:00','Sun_10:00']);
});
test('calendars retain the actual dated session instead of first class date', () => {
  const session = calendarSession({ id_buoi_hoc: 12, thoi_gian_bat_dau: '2026-10-11T09:00:00+07:00', thoi_gian_ket_thuc: '2026-10-11T10:00:00+07:00', lop_hoc: { id: 3, thoi_gian_bat_dau: '2026-10-04T09:00:00+07:00' } });
  assert.equal(session.id_buoi_hoc, 12);
  assert.equal(session.lop_hoc.id, 3);
  assert.equal(session.lop_hoc.thoi_gian_bat_dau, session.thoi_gian_bat_dau);
});
test('room chat never reports delivery when disconnected or publishing fails', async () => {
  await assert.rejects(sendRoomMessage(null, 'hello'), /kết nối/);
  await assert.rejects(sendRoomMessage({ state: 'connected', localParticipant: { publishData: async () => { throw new Error('network'); } } }, 'hello'), /network/);
  const sent = await sendRoomMessage({ state: 'connected', localParticipant: { publishData: async () => {} } }, 'hello');
  assert.equal(sent.text, 'hello');
  assert.equal(sent.status, 'sent');
});
test('remote audio attaches to DOM and disposal stops tracks and removes attachments', async () => {
  const children = [];
  const element = { remove() { children.splice(children.indexOf(this), 1); } };
  attachTrack({ attach: () => element }, { appendChild: node => children.push(node) });
  assert.equal(children.length, 1);
  let stopped = 0, disconnected = 0;
  await disposeRoom({ disconnect: async () => disconnected++ }, [{ getTracks: () => [{ stop: () => stopped++ }] }], [element]);
  assert.equal(children.length, 0); assert.equal(stopped, 1); assert.equal(disconnected, 1);
});
test('enrollment and room requests require server proof and preserve API failures', async () => {
  const calls = [];
  const service = createProductService({ post: async (url, data) => { calls.push({ url, data }); return { status: true, data: { token: 'signed', server_url: 'wss://room' } }; } });
  await assert.rejects(service.enroll(3), /xác thực/);
  await assert.rejects(service.roomToken(12), /xác thực/);
  await service.enroll(3, 'proof'); await service.roomToken(12, 'proof');
  assert.deepEqual(calls[0], { url: '/hoc-vien/dang-ky-lop-hoc', data: { id_lop_hoc: 3, verification_id: 'proof' } });
  assert.deepEqual(calls[1], { url: '/phong-hop/tao-token', data: { id_buoi_hoc: 12, verification_id: 'proof' } });
  const failed = createProductService({ post: async () => ({ status: false, message: 'Provider chưa cấu hình.' }) });
  await assert.rejects(failed.roomToken(12, 'proof'), /Provider/);
});
test('sample registration only submits descriptor without claimed actor ID', async () => {
  let submitted;
  const service = createProductService({ post: async (url, data) => { submitted = { url, data }; return { status: true, data: { has_face_id: true } }; } });
  await service.sample('giao_vien', descriptor);
  assert.deepEqual(submitted, { url: '/giao-vien/face-id/sample', data: { descriptor } });
});
