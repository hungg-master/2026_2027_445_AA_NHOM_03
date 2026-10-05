import test from 'node:test';
import assert from 'node:assert/strict';
import { createServer } from 'vite';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

test('active Vue views render empty and populated states without undefined bindings', { timeout: 60000 }, async t => {
  const oldStorage = globalThis.localStorage;
  globalThis.localStorage = { getItem: key => key === 'role' ? 'hoc_vien' : null };
  t.after(() => globalThis.localStorage = oldStorage);
  const server = await createServer({ server: { middlewareMode: true }, optimizeDeps: { disabled: true }, appType: 'custom', logLevel: 'error' });
  t.after(() => server.close());
  const session = { id: 3, id_buoi_hoc: 12, thoi_gian_bat_dau: '2026-10-04T09:00:00+07:00', thoi_gian_ket_thuc: '2026-10-04T10:00:00+07:00', mon_hoc: { ten_mon_hoc: 'Toán' }, hinh_thuc: 'offline', dang_ky_lops: [{ id: 1, id_hoc_vien: 4, trang_thai: 'da_xac_nhan', hoc_vien: { ho_ten: 'Người học' } }] };
  const cases = [
    ['LandingPage', { teachers: [{ id: 1, ho_ten: 'Giảng viên', chuc_danh: 'Giáo viên', so_nam_kinh_nghiem: 2 }] }],
    ['ClientProfile', { userRole: 'giao_vien' }], ['DangKyLopHoc', {}], ['MySchedule', { tutors: [{ id: 1, name: 'Giảng viên', slots: [{ start: session.thoi_gian_bat_dau, end: session.thoi_gian_ket_thuc }] }] }],
    ['LichHocHocVien', {}], ['LichDayGiaoVien', { showModal: true, selectedLop: session }], ['QuanLyLopHoc', { showFormModal: true }], ['LopCuaToi', {}],
    ['TrialBooking', { selectedStart: session.thoi_gian_bat_dau, selectedEnd: session.thoi_gian_ket_thuc }], ['TrialManagement', { rows: [{ id: 1, status: 'pending', subject: 'Toán' }] }],
    ['ThanhToanHocPhi', { rows: [{ id: 1, amount: 100000, currency: 'VND', status: 'pending', transactions: [] }] }], ['LessonReview', { sessions: [{ ...session, lop_hoc: session }] }],
    ['ConsultationChat', { selected: { id: 1 }, conversations: [{ id: 1, counterparty: { ho_ten: 'Giảng viên' } }], messages: [{ id: 1, sender_role: 'hoc_vien', body: 'Chào' }] }],
    ['AiAssistant', {}], ['AdminCatalog', { rows: [{ id: 1, ten_mon_hoc: 'Toán' }] }], ['AdminPayments', { rows: [{ id: 1, status: 'awaiting_manual' }] }], ['PasswordReset', {}], ['PhongHopVideo', {}], ['TeacherDirectory', {}],
  ];
  for (const [name, seed] of cases) {
    const component = (await server.ssrLoadModule(`/src/components/EduLink/${name}.vue`)).default;
    const renderable = { ...component, data() { return { ...(component.data?.call(this) || {}), ...seed }; } };
    const app = createSSRApp(renderable);
    app.config.globalProperties.$route = { path: '/', params: { id: '12' }, query: {}, hash: '' };
    app.config.globalProperties.$router = { push() {} };
    app.component('RouterLink', { props: ['to'], render() { return h('a', {}, this.$slots.default?.()); } });
    const warnings = []; app.config.warnHandler = message => warnings.push(message);
    const html = await renderToString(app);
    assert.ok(html.length > 50, `${name} should render usable markup`);
    assert.deepEqual(warnings, [], `${name} should not reference missing bindings`);
  }
  const header = (await server.ssrLoadModule('/src/layout/components/TopKhachHang.vue')).default;
  const headerApp = createSSRApp({ ...header, data() { return { ...header.data(), userRole: 'admin', isLoggedIn: true, showUserDropdown: true }; } });
  headerApp.component('RouterLink', { props: ['to'], render() { return h('a', { href: this.to }, this.$slots.default?.()); } });
  const headerHtml = await renderToString(headerApp);
  for (const path of ['/my-schedule', '/hoc-thu-cua-toi', '/tro-ly-ai', '/client/profile', '/hoc-vien/lich-hoc', '/hoc-vien/lop-cua-toi']) {
    assert.ok(!headerHtml.includes(`href="${path}"`), `admin header should not link to participant endpoint ${path}`);
  }
  assert.ok(headerHtml.includes('Quản trị viên')); assert.ok(headerHtml.includes('href="/admin/danh-muc"'));
});
