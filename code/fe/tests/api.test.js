import test from 'node:test';
import assert from 'node:assert/strict';
import { logout } from '../src/services/api.js';

function session(t, role = 'hoc_vien') {
  const values = new Map([['token', 'test-token'], ['role', role], ['user', '{}']]);
  const original = globalThis.localStorage;
  Object.defineProperty(globalThis, 'localStorage', {
    configurable: true,
    value: { getItem: key => values.get(key), removeItem: key => values.delete(key) },
  });
  t.after(() => Object.defineProperty(globalThis, 'localStorage', { configurable: true, value: original }));
  return values;
}

test('logout revokes the token for each role before clearing the session', async t => {
  for (const [role, prefix] of [['hoc_vien', 'hoc-vien'], ['giao_vien', 'giao-vien'], ['admin', 'admin']]) {
    const values = session(t, role);
    t.mock.method(globalThis, 'fetch', async (url, options) => {
      assert.ok(url.endsWith(`/${prefix}/logout`));
      assert.equal(options.method, 'POST');
      assert.equal(options.headers.Authorization, 'Bearer test-token');
      assert.equal(values.get('token'), 'test-token');
      return { ok: true, status: 200 };
    });
    await logout();
    assert.equal(values.size, 0);
    t.mock.restoreAll();
  }
});

test('logout clears a token that is already invalid', async t => {
  const values = session(t);
  t.mock.method(globalThis, 'fetch', async () => ({ ok: false, status: 401 }));
  await logout();
  assert.equal(values.size, 0);
});

test('logout keeps the session when token revocation fails', async t => {
  const values = session(t);
  t.mock.method(globalThis, 'fetch', async () => ({ ok: false, status: 500 }));
  await assert.rejects(logout());
  assert.equal(values.get('token'), 'test-token');
});

test('logout keeps the session on a network error', async t => {
  const values = session(t);
  t.mock.method(globalThis, 'fetch', async () => { throw new Error('offline'); });
  await assert.rejects(logout());
  assert.equal(values.get('token'), 'test-token');
});
