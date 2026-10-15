import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { createServer } from 'vite';
import { createSSRApp, h, markRaw } from 'vue';
import { renderToString } from 'vue/server-renderer';

async function options(dependencies = {}) {
  const text = await fs.readFile(new URL('../src/components/EduLink/PhongHopVideo.vue', import.meta.url), 'utf8');
  const script = text.match(/<script>([\s\S]*?)<\/script>/)[1].replace(/^import .+;?\r?\n/gm, '').replace('export default', 'return');
  return new Function('markRaw', 'FaceProof', 'product', 'http', 'accepted', 'attachTrack', 'disposeRoom', 'sendRoomMessage', script)(markRaw, {}, dependencies.product || {}, dependencies.http || {}, request => request, () => {}, async () => {}, async () => {});
}
function browser(t, role = 'hoc_vien') {
  const prior = globalThis.localStorage;
  const account = { id: 7, ho_ten: role === 'hoc_vien' ? 'Học viên Demo' : 'Giáo viên Toán 12A' };
  globalThis.localStorage = { getItem: key => key === 'role' ? role : key === 'user' ? JSON.stringify(account) : null };
  t.after(() => { globalThis.localStorage = prior; });
}
function instance(component) {
  const vm = { $route: { params: { id: '105' }, query: { session: '105' } }, $nextTick() {} };
  for (const [name, method] of Object.entries(component.methods)) vm[name] = method.bind(vm);
  Object.assign(vm, component.data.call(vm));
  for (const [name, compute] of Object.entries(component.computed || {})) Object.defineProperty(vm, name, { get: () => compute.call(vm) });
  return vm;
}
const person = (identity, name) => ({ sid: identity.replace(':', '-'), identity, name, isMicrophoneEnabled: false, isCameraEnabled: false, videoTrackPublications: new Map(), audioTrackPublications: new Map() });

test('rejoining waits for the same pending attendance leave before requesting a new token', async t => {
  browser(t);
  let releaseLeave;
  const leaveResponse = new Promise(resolve => { releaseLeave = resolve; });
  const calls = [];
  const vm = instance(await options({ http: { post() { calls.push('leave'); return leaveResponse; } }, product: { async roomToken() { calls.push('token'); throw new Error('test stops before SDK connection'); } } }));
  vm.joined = true; vm.state = 'disconnected';
  const firstLeave = vm.leaveAttendance();
  assert.equal(vm.leaveAttendance(), firstLeave, 'all callers must share the in-flight leave');
  const retry = vm.connect({ verification_id: 'proof' });
  await Promise.resolve();
  assert.deepEqual(calls, ['leave']);
  assert.equal(vm.joined, true);
  releaseLeave({}); await retry;
  assert.deepEqual(calls, ['leave', 'token']);
  assert.equal(vm.joined, false);
});

test('failed attendance leave blocks a new join until departure is recorded', async t => {
  browser(t);
  let requested = false;
  const vm = instance(await options({ http: { async post() { throw new Error('leave failed'); } }, product: { async roomToken() { requested = true; } } }));
  vm.joined = true; vm.state = 'disconnected';
  await vm.connect({ verification_id: 'proof' });
  assert.equal(requested, false);
  assert.equal(vm.joined, true);
  assert.equal(vm.state, 'failed');
});

test('unsubscribe reconciles after LiveKit has cleared its publication track', async t => {
  browser(t);
  const vm = instance(await options());
  const student = person('hoc_vien:7', 'Học viên Demo');
  const teacher = person('giao_vien:7', 'Giáo viên Toán 12A');
  const publication = { source: 'camera', trackSid: 'cam', isMuted: false, track: {} };
  teacher.videoTrackPublications.set('cam', publication);
  const handlers = new Map();
  const eventNames = ['TrackUnsubscribed', 'ParticipantConnected', 'ParticipantDisconnected', 'TrackPublished', 'TrackUnpublished', 'TrackSubscribed', 'LocalTrackPublished', 'LocalTrackUnpublished', 'TrackMuted', 'TrackUnmuted', 'ParticipantNameChanged', 'Reconnecting', 'Reconnected', 'AudioPlaybackStatusChanged', 'Disconnected', 'DataReceived'];
  vm.sdk = { RoomEvent: Object.fromEntries(eventNames.map(name => [name, name])) };
  vm.room = { state: 'connected', localParticipant: student, remoteParticipants: new Map([[teacher.identity, teacher]]), on(event, callback) { handlers.set(event, callback); } };
  vm.bindRoomEvents(vm.room); vm.syncParticipants();
  assert.equal(vm.participants.find(row => !row.isLocal).videoEnabled, true);
  handlers.get('TrackUnsubscribed')();
  publication.track = undefined;
  await Promise.resolve();
  assert.equal(vm.participants.find(row => !row.isLocal).videoEnabled, false);
  assert.equal(vm.participants.length, 2);
});

test('room keeps two named role-aware participants without any camera or microphone tracks', async t => {
  browser(t);
  const vm = instance(await options());
  const student = person('hoc_vien:7', 'Học viên Demo');
  const teacher = person('giao_vien:7', 'Giáo viên Toán 12A');
  vm.room = { state: 'connected', localParticipant: student, remoteParticipants: new Map([[teacher.identity, teacher]]) };
  vm.syncParticipants();
  assert.equal(vm.participants.length, 2);
  assert.deepEqual(vm.participants.map(row => [row.name, row.roleLabel]), [['Giáo viên Toán 12A', 'Giáo viên'], ['Học viên Demo', 'Học viên']]);
  assert.equal(vm.participants.find(row => row.isLocal).identity, 'hoc_vien:7');
  assert.ok(vm.participants.every(row => !row.videoEnabled && !row.audioEnabled));
  vm.room.remoteParticipants.clear(); vm.syncParticipants();
  assert.equal(vm.participants.length, 1, 'a departed participant must disappear even without a track event');
});

test('room updates camera placeholders without removing the connected participant', async t => {
  browser(t);
  const vm = instance(await options());
  const student = person('hoc_vien:7', 'Học viên Demo');
  const teacher = person('giao_vien:7', 'Giáo viên Toán 12A');
  teacher.isCameraEnabled = true;
  teacher.videoTrackPublications.set('camera', { source: 'camera', trackSid: 'camera', isMuted: false, track: {} });
  vm.room = { state: 'connected', localParticipant: student, remoteParticipants: new Map([[teacher.identity, teacher]]) };
  vm.syncParticipants();
  assert.equal(vm.participants.find(row => !row.isLocal).videoEnabled, true);
  teacher.isCameraEnabled = false; teacher.videoTrackPublications.clear();
  vm.syncParticipants();
  assert.equal(vm.participants.length, 2);
  assert.equal(vm.participants.find(row => !row.isLocal).videoEnabled, false);
  vm.room.state = 'disconnected'; vm.syncParticipants();
  assert.deepEqual(vm.participants, []);
});

test('room rendering distinguishes Face ID waiting from a connected two-person room with cameras off', { timeout: 30000 }, async t => {
  browser(t);
  const server = await createServer({ server: { middlewareMode: true, hmr: false }, optimizeDeps: { disabled: true }, appType: 'custom', logLevel: 'error' });
  t.after(() => server.close());
  const component = (await server.ssrLoadModule('/src/components/EduLink/PhongHopVideo.vue')).default;
  const render = async seed => {
    const app = createSSRApp({ ...component, data() { return { ...component.data.call(this), ...seed }; } });
    app.config.globalProperties.$route = { params: { id: '105' }, query: { session: '105' } };
    app.component('RouterLink', { props: ['to'], render() { return h('a', { href: this.to }, this.$slots.default?.()); } });
    const warnings = []; app.config.warnHandler = value => warnings.push(value);
    const html = await renderToString(app); assert.deepEqual(warnings, []); return html;
  };
  const waiting = await render({ state: 'verification' });
  assert.ok(waiting.includes('Chờ xác thực Face ID'));
  assert.ok(waiting.includes('Bạn là học viên'));
  assert.ok(waiting.includes('Học viên Demo'));
  assert.ok(!waiting.includes('Đang diễn ra'), 'the lobby must never claim that it is connected');
  assert.ok(!waiting.includes('aria-label="Ô người tham gia:'), 'waiting for verification must not fabricate a joined person');
  const vm = instance(await options());
  const student = person('hoc_vien:7', 'Học viên Demo');
  const teacher = person('giao_vien:7', 'Giáo viên Toán 12A');
  vm.room = { state: 'connected', localParticipant: student, remoteParticipants: new Map([[teacher.identity, teacher]]) };
  vm.syncParticipants();
  const connected = await render({ state: 'connected', participants: vm.participants });
  assert.equal((connected.match(/aria-label="Ô người tham gia:/g) || []).length, 2);
  assert.ok(connected.includes('Giáo viên Toán 12A'));
  assert.ok(connected.includes('Học viên Demo'));
  assert.ok(connected.includes('2 người trong phòng'));
  assert.ok(connected.includes('Đã kết nối phòng học'));
  assert.ok(connected.includes('Camera đang tắt'));
});
