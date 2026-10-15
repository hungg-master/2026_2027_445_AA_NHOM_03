import test from 'node:test';
import assert from 'node:assert/strict';
import { createServer } from 'vite';
import { createSSRApp } from 'vue';
import { renderToString } from 'vue/server-renderer';

test('chat identifies each signed-in role and names both sides even when account IDs are equal', { timeout: 30000 }, async t => {
  const oldStorage = globalThis.localStorage;
  t.after(() => { globalThis.localStorage = oldStorage; });
  const server = await createServer({ server: { middlewareMode: true, hmr: false }, optimizeDeps: { disabled: true }, appType: 'custom', logLevel: 'error' });
  t.after(() => server.close());
  const component = (await server.ssrLoadModule('/src/components/EduLink/ConsultationChat.vue')).default;
  for (const view of [
    { role: 'hoc_vien', own: 'Học viên Demo Bảo vệ', peerRole: 'giao_vien', peer: 'Giáo viên Toán 12A', identity: 'Bạn là học viên', heading: 'Tư vấn với giáo viên' },
    { role: 'giao_vien', own: 'Giáo viên Toán 12A', peerRole: 'hoc_vien', peer: 'Học viên Demo Bảo vệ', identity: 'Bạn là giáo viên', heading: 'Trao đổi với học viên' },
  ]) {
    globalThis.localStorage = { getItem: key => key === 'role' ? view.role : key === 'user' ? JSON.stringify({ id: 7, ho_ten: view.own }) : null };
    const conversation = { id: 1, counterparty: { id: 7, ho_ten: view.peer }, counterparty_role: view.peerRole };
    const app = createSSRApp({ ...component, data() {
      return { ...component.data.call(this), selected: conversation, conversations: [conversation], messages: [
        { id: 1, sender_role: view.role, sender_id: 7, body: 'Tin của tôi', created_at: '2026-10-15 20:00:00' },
        { id: 2, sender_role: view.peerRole, sender_id: 7, body: 'Tin từ bên kia', created_at: '2026-10-15 20:01:00' },
      ] };
    } });
    const warnings = []; app.config.warnHandler = message => warnings.push(message);
    const html = await renderToString(app);
    assert.ok(html.includes(view.identity), `${view.role}: signed-in role must be explicit`);
    assert.ok(html.includes(view.heading), `${view.role}: page purpose must reflect the role`);
    assert.ok(html.includes(view.own), `${view.role}: signed-in name must be visible`);
    assert.ok(html.includes(view.peer), `${view.role}: counterparty must be named`);
    assert.ok(html.includes('aria-label="Tin nhắn bạn đã gửi"'), 'own message must be identified independently of a matching numeric ID');
    assert.ok(html.includes('aria-label="Tin nhắn bạn nhận được"'), 'incoming message must be identified independently of a matching numeric ID');
    assert.ok(!html.includes('Người tư vấn'), 'ambiguous generic sender label must be replaced with names');
    assert.deepEqual(warnings, []);
  }
});
