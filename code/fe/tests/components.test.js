import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { availabilitySlots, dateParts } from '../src/services/flowHelpers.js';

// Exercise the actual Vue option methods without requiring browser/media providers.
async function options(name, dependencies = {}, directory = 'components/EduLink') {
  const text = await fs.readFile(new URL(`../src/${directory}/${name}.vue`, import.meta.url), 'utf8');
  const script = text.match(/<script>([\s\S]*?)<\/script>/)[1].replace(/^import .+;?\r?\n/gm, '').replace('export default', 'return');
  return new Function(...Object.keys(dependencies), script)(...Object.values(dependencies));
}
function instance(component) {
  const value = {};
  for (const [key, method] of Object.entries(component.methods || {})) value[key] = method.bind(value);
  Object.assign(value, component.data.call(value));
  return value;
}
function browser(t) {
  const prior = globalThis.localStorage;
  globalThis.localStorage = { getItem: key => key === 'role' ? 'hoc_vien' : key === 'token' ? 'token' : null, setItem() {}, removeItem() {} };
  t.after(() => globalThis.localStorage = prior);
}
test('availability component clears previous-account values even when server reports empty', async t => {
  browser(t);
  const component = await options('MySchedule', { thoiGianRanhService: { getSchedule: async () => ({ status: true, data: [] }) }, product: {}, lopHocService: {}, availabilitySlots });
  const vm = instance(component); vm.selectedSlots = ['Mon_09:00'];
  await vm.loadSavedSchedule(); assert.deepEqual(vm.selectedSlots, []); assert.equal(vm.loading, false);
});
test('early morning availability can be selected, saved and restored from the server', async t => {
  browser(t);
  let saved = [];
  const component = await options('MySchedule', {
    thoiGianRanhService: {
      updateSchedule: async (role, schedules) => { saved = schedules; return { status: true }; },
      getSchedule: async () => ({ status: true, data: saved }),
    },
    product: {}, lopHocService: {}, availabilitySlots,
  });
  const vm = instance(component);
  vm.showToast = () => {};
  vm.activeShiftFilter = 'morning';
  const morningSlots = component.computed.visibleTimeSlots.call(vm);
  assert.ok(morningSlots.some(slot => slot.key === '07:00'), '07:00–08:00 must be selectable in the morning grid');
  vm.toggleSlot('Tue', '07:00');
  await vm.saveSchedule();
  assert.deepEqual(saved, [{ ngay_trong_tuan: 2, thoi_gian_bat_dau: '07:00:00', thoi_gian_ket_thuc: '08:00:00' }]);
  vm.selectedSlots = [];
  await vm.loadSavedSchedule();
  assert.deepEqual(vm.selectedSlots, ['Tue_07:00']);
  assert.equal(vm.hasUnsavedChanges, false);
});

test('schedule network failure keeps unsaved edit and never reports saved', async t => {
  browser(t);
  const component = await options('MySchedule', { thoiGianRanhService: { updateSchedule: async () => { throw new Error('offline'); } }, product: {}, lopHocService: {}, availabilitySlots });
  const vm = instance(component); vm.selectedSlots = ['Mon_09:00']; vm.hasUnsavedChanges = true; vm.showToast = (type, message) => vm.notice = { type, message };
  await vm.saveSchedule(); assert.equal(vm.hasUnsavedChanges, true); assert.equal(vm.notice.type, 'error'); assert.equal(vm.lastSavedTime, null);
});
test('cancelled calendar sessions never join a room even if their date is current', async () => {
  const vm = instance(await options('LichHocHocVien', { lichHocService: {}, calendarSession: value => value, dateParts }));
  const now = Date.now();
  const session = { trang_thai: 'cancelled', lop_hoc: { thoi_gian_bat_dau: new Date(now - 60000).toISOString(), thoi_gian_ket_thuc: new Date(now + 60000).toISOString() } };
  assert.equal(vm.canJoinSession(session), false);
});
test('missing session timing cannot enable room entry', async () => {
  const vm = instance(await options('LichHocHocVien', { lichHocService: {}, calendarSession: value => value, dateParts }));
  assert.equal(vm.canJoinSession({ id_buoi_hoc: 12, lop_hoc: {} }), false);
});
test('calendar network failure displays an error and clears stale sessions', async () => {
  const vm = instance(await options('LichHocHocVien', { lichHocService: { getLichHoc: async () => { throw new Error('offline'); } }, calendarSession: value => value, dateParts }));
  vm.dsLichHoc = [{ id_buoi_hoc: 12 }]; vm.filteredLichHoc = vm.dsLichHoc;
  await vm.loadData();
  assert.equal(vm.error, 'offline'); assert.deepEqual(vm.filteredLichHoc, []);
});
test('public enrollment navigates to proof-enabled registration instead of sending an unverified request', async t => {
  browser(t);
  let request = false;
  const vm = instance(await options('DanhSachLopPublic', { lopHocService: {}, lichHocService: { dangKyLop: async () => { request = true; return { status: true }; } } }));
  vm.$router = { push: route => vm.route = route }; vm.loadData = async () => {}; globalThis.alert = () => {};
  await vm.dangKy({ id: 7 });
  assert.equal(request, false); assert.equal(vm.route.path, '/hoc-vien/dang-ky-lop');
});
test('teacher completion posts actual session and selected offline attendance before reloading', async () => {
  let submitted;
  const vm = instance(await options('LichDayGiaoVien', { lichDayService: {}, lopHocService: {}, calendarSession: value => value, dateParts, http: { post: async (url, data) => { submitted = { url, data }; return { status: true, message: 'Đã hoàn thành.' }; } }, accepted: async request => request }));
  vm.selectedLop = { id_buoi_hoc: 12, hinh_thuc: 'offline', session_status: 'scheduled', trang_thai_buoi: 'da_hoc', thoi_gian_ket_thuc: new Date(Date.now() - 10000).toISOString() };
  vm.attendedStudentIds = [4,5]; vm.loadData = async () => {};
  assert.equal(typeof vm.completeSession, 'function');
  await vm.completeSession();
  assert.deepEqual(submitted, { url: '/giao-vien/buoi-hoc/12/complete', data: { attended_student_ids: [4,5] } });
});
test('teacher detail uses class ID while preserving the dated session ID', async () => {
  let requested;
  const vm = instance(await options('LichDayGiaoVien', { lichDayService: {}, lopHocService: { getChiTietLop: async id => { requested = id; return { status: true, data: { id, mon_hoc: { ten_mon_hoc: 'Toán' } } }; } }, calendarSession: value => value, dateParts, http: {}, accepted: async request => request }));
  await vm.openLopDetail({ id: 12, id_buoi_hoc: 12, lop_hoc: { id: 3 }, thoi_gian_bat_dau: '2026-10-04T09:00:00+07:00' });
  assert.equal(requested, 3); assert.equal(vm.selectedLop.id_buoi_hoc, 12);
});
test('tutor search navigates without manufacturing a result count', async t => {
  browser(t);
  let alerts = 0;
  const prior = globalThis.alert; globalThis.alert = () => alerts++; t.after(() => globalThis.alert = prior);
  const vm = instance(await options('LandingPage', { API_BASE: 'http://localhost/api', logoutSession: async () => {}, http: {}, accepted: value => value }));
  vm.$router = { push: value => vm.route = value };
  vm.handleSearchTutor();
  assert.equal(alerts, 0); assert.equal(vm.route, '/my-schedule');
});
test('teacher profile retains professional fields and never copies canonical face vectors', async t => {
  browser(t);
  const vm = instance(await options('ClientProfile', { FaceProof: {}, TeacherBank: {}, FinanceSummary: {}, profileService: {} }));
  vm.applyUserData({ id: 3, ho_ten: 'Giảng viên', chuc_danh: 'Thạc sĩ', so_nam_kinh_nghiem: 4, mo_ta: 'Giới thiệu', has_face_id: true, du_lieu_khuon_mat: '[private]' });
  assert.equal(vm.profileForm.chuc_danh, 'Thạc sĩ'); assert.equal(vm.profileForm.so_nam_kinh_nghiem, 4); assert.equal(vm.profileForm.mo_ta, 'Giới thiệu');
  assert.equal(vm.profile.du_lieu_khuon_mat, undefined);
});
test('review selection contains server-completed sessions, excluding elapsed scheduled sessions', async () => {
  const component = await options('LessonReview', { support: { reviews: async () => ({ status: true, data: [] }) }, lichHocService: { getLichHoc: async () => ({ status: true, data: [{ id_buoi_hoc: 1, trang_thai: 'scheduled', trang_thai_buoi: 'da_hoc' }, { id_buoi_hoc: 2, trang_thai: 'completed', trang_thai_buoi: 'da_hoc' }] }) } });
  const vm = { role: 'hoc_vien', sessions: [], reviews: [] };
  await component.methods.load.call(vm);
  assert.deepEqual(vm.sessions.map(row => row.id_buoi_hoc), [2]);
});

test('header keeps the signed-in session visible when server logout fails', async t => {
  browser(t);
  let removed = 0;
  globalThis.localStorage.removeItem = () => removed++;
  const vm = instance(await options('TopKhachHang', { logoutSession: async () => { throw new Error('offline'); } }, 'layout/components'));
  vm.isLoggedIn = true; vm.$router = { push: route => vm.route = route };
  await vm.logout();
  assert.equal(vm.isLoggedIn, true); assert.equal(removed, 0); assert.equal(vm.route, undefined); assert.equal(vm.sessionError, 'offline');
});

test('chat fetch cursor does not skip unread messages when a newer outgoing message returns', async t => {
  browser(t);
  const reads = [];
  const vm = instance(await options('ConsultationChat', { support: {
    sendMessage: async () => ({ data: { id: 102, body: 'own reply' } }),
    messages: async (id, after) => { reads.push(after); return { data: after === 100 ? [{ id: 101, body: 'unread reply' }, { id: 102, body: 'own reply' }] : [] }; },
  } }));
  vm.selected = { id: 1 }; vm.messages = [{ id: 100 }]; vm.readCursor = 100; vm.body = 'own reply';
  t.after(() => { vm.stopped = true; clearTimeout(vm.polling); });
  await vm.send(); await vm.poll(vm.generation);
  assert.deepEqual(reads, [100]); assert.deepEqual(vm.messages.map(row => row.id), [100, 101, 102]); assert.equal(vm.readCursor, 102);
});

test('chat fetches all 100-row pages using only fetched IDs and merges messages once in order', async t => {
  browser(t);
  const reads = [];
  const vm = instance(await options('ConsultationChat', { support: {
    messages: async (id, after) => { reads.push(after); return { data: after === 0 ? Array.from({ length: 100 }, (_, i) => ({ id: i + 1 })) : after === 100 ? [{ id: 101 }, { id: 102 }] : [] }; },
  } }));
  vm.selected = { id: 1 }; vm.messages = [{ id: 102 }];
  t.after(() => { vm.stopped = true; clearTimeout(vm.polling); });
  await vm.poll(vm.generation);
  assert.deepEqual(reads, [0, 100]); assert.equal(vm.readCursor, 102);
  assert.deepEqual(vm.messages.map(row => row.id), Array.from({ length: 102 }, (_, i) => i + 1));
});
