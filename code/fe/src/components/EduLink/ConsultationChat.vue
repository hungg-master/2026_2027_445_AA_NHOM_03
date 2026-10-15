<template>
  <main class="consultation-page" :class="isTeacher ? 'consult-teacher' : 'consult-student'">
    <header class="consult-page-header">
      <div class="consult-account" aria-label="Tài khoản đang sử dụng">
        <span class="consult-role-icon" aria-hidden="true"><i :class="isTeacher ? 'fa-solid fa-chalkboard-user' : 'fa-solid fa-user-graduate'"></i></span>
        <div><p class="consult-account-role">{{ isTeacher ? 'Bạn là giáo viên' : 'Bạn là học viên' }}</p><strong>{{ currentUserName }}</strong></div>
      </div>
      <div class="consult-intro">
        <h1>{{ isTeacher ? 'Trao đổi với học viên' : 'Tư vấn với giáo viên' }}</h1>
        <p>{{ isTeacher ? 'Giải đáp thắc mắc và trao đổi lịch học với học viên.' : 'Hỏi bài, trao đổi lịch học và nhận tư vấn từ giáo viên.' }}</p>
      </div>
    </header>

    <p v-if="error" class="alert alert-danger mt-3" role="alert">{{ error }}</p>

    <div class="consult-layout">
      <aside class="consult-sidebar" aria-label="Danh sách cuộc trò chuyện">
        <div class="consult-list-heading"><h2>{{ isTeacher ? 'Học viên của bạn' : 'Giáo viên của bạn' }}</h2><span>{{ conversations.length }}</span></div>
        <div class="consult-conversations">
          <button v-for="row in conversations" :key="row.id" type="button" class="consult-contact" :class="{ 'is-selected': selected?.id === row.id }" :aria-pressed="selected?.id === row.id" @click="select(row)">
            <span class="consult-avatar" aria-hidden="true">{{ initials(conversationName(row)) }}</span>
            <span class="consult-contact-info"><strong>{{ conversationName(row) }}</strong><small>{{ peerRoleLabel }}</small></span>
            <i v-if="selected?.id === row.id" class="fa-solid fa-chevron-right consult-contact-arrow" aria-hidden="true"></i>
          </button>
          <p v-if="!conversations.length" class="consult-list-empty">{{ isTeacher ? 'Chọn học viên bên dưới để bắt đầu trao đổi.' : 'Chọn giáo viên bên dưới để bắt đầu trao đổi.' }}</p>
        </div>
        <details class="consult-new-conversation" :open="!conversations.length">
          <summary>Cuộc trò chuyện mới</summary>
          <form @submit.prevent="create">
            <label for="consult-contact-picker">{{ isTeacher ? 'Chọn học viên' : 'Chọn giáo viên' }}</label>
            <select id="consult-contact-picker" v-model="contactId" class="form-select" required><option value="">{{ isTeacher ? 'Chọn học viên' : 'Chọn giáo viên' }}</option><option v-for="row in contacts" :key="row.id" :value="row.id">{{ row.ho_ten }}</option></select>
            <button class="consult-primary-button" :disabled="busy">Bắt đầu trò chuyện</button>
          </form>
        </details>
      </aside>

      <section class="consult-panel" aria-label="Nội dung cuộc trò chuyện">
        <template v-if="selected">
          <header class="consult-peer-header">
            <span class="consult-avatar consult-peer-avatar" aria-hidden="true">{{ initials(conversationName(selected)) }}</span>
            <div><p>Bạn đang trò chuyện với {{ isTeacher ? 'học viên' : 'giáo viên' }}</p><h2>{{ conversationName(selected) }}</h2></div>
          </header>
          <div ref="history" class="consult-history" role="log" aria-label="Lịch sử tin nhắn" aria-live="polite" aria-relevant="additions">
            <article v-for="row in messages" :key="row.id" class="consult-message" :class="isOwnMessage(row) ? 'is-own' : 'is-incoming'" :aria-label="isOwnMessage(row) ? 'Tin nhắn bạn đã gửi' : 'Tin nhắn bạn nhận được'">
              <div class="consult-message-sender">{{ isOwnMessage(row) ? currentUserName : conversationName(selected) }}<span>{{ isOwnMessage(row) ? 'Bạn' : peerRoleLabel }}</span></div>
              <div class="consult-message-bubble"><p>{{ row.body }}</p><time :datetime="row.created_at" :title="row.created_at">{{ messageTime(row.created_at) }}</time></div>
            </article>
            <div v-if="!messages.length" class="consult-history-empty"><i class="fa-regular fa-comments" aria-hidden="true"></i><h3>Bắt đầu cuộc trò chuyện</h3><p>Gửi lời chào đến {{ conversationName(selected) }}.</p></div>
          </div>
          <form class="consult-composer" @submit.prevent="send">
            <label for="consult-message-input" class="visually-hidden">Tin nhắn gửi {{ conversationName(selected) }}</label>
            <input id="consult-message-input" v-model="body" class="form-control" :placeholder="'Nhắn cho ' + conversationName(selected) + '…'" maxlength="2000" autocomplete="off" required />
            <button class="consult-primary-button" :disabled="busy || !body.trim()"><span>{{ busy ? 'Đang gửi…' : 'Gửi' }}</span><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
          </form>
        </template>
        <div v-else class="consult-unselected"><i class="fa-regular fa-comments" aria-hidden="true"></i><h2>{{ isTeacher ? 'Chọn một học viên để trao đổi' : 'Chọn một giáo viên để tư vấn' }}</h2><p>Chọn tên trong danh sách hoặc bắt đầu cuộc trò chuyện mới.</p></div>
      </section>
    </div>
  </main>
</template>
<script>
import support from '../../services/supportService';
export default {
  data() {
    const role = localStorage.getItem('role');
    let account = null;
    try { account = JSON.parse(localStorage.getItem('user') || localStorage.getItem('edulink_user') || 'null'); } catch { /* A missing cached profile must not prevent opening chat. */ }
    return { role, currentUserId: account?.id || null, currentUserName: account?.ho_ten || account?.name || (role === 'giao_vien' ? 'Tài khoản giáo viên' : 'Tài khoản học viên'), conversations: [], contacts: [], contactId: '', selected: null, messages: [], readCursor: 0, body: '', error: '', busy: false, polling: null, stopped: false, generation: 0 };
  },
  computed: {
    isTeacher() { return this.role === 'giao_vien'; },
    peerRoleLabel() { return this.isTeacher ? 'Học viên' : 'Giáo viên'; },
  },
  mounted() { this.load(); },
  beforeUnmount() { this.stopped = true; this.generation++; clearTimeout(this.polling); },
  methods: {
    conversationName(row) {
      const conversation = this.conversations.find(value => Number(value.id) === Number(row?.id)) || row;
      return conversation?.counterparty?.ho_ten || conversation?.giao_vien?.ho_ten || conversation?.hoc_vien?.ho_ten || conversation?.contact_name || (this.role === 'giao_vien' ? 'Học viên' : 'Giáo viên');
    },
    initials(name) { return String(name).trim().split(/\s+/).slice(-2).map(part => part[0] || '').join('').toLocaleUpperCase('vi-VN'); },
    isOwnMessage(row) { return row.sender_role === this.role && (!this.currentUserId || Number(row.sender_id) === Number(this.currentUserId)); },
    messageTime(value) {
      if (!value) return '';
      const text = String(value);
      const date = new Date(/[Zz]|[+-]\d\d:\d\d$/.test(text) ? text : text.replace(' ', 'T') + '+07:00');
      return Number.isNaN(date.getTime()) ? '' : date.toLocaleTimeString('vi-VN', { timeZone: 'Asia/Ho_Chi_Minh', hour: '2-digit', minute: '2-digit' });
    },
    async load() { const results = await Promise.allSettled([support.conversations(), support.contacts()]); if (results[0].status === 'fulfilled') this.conversations = results[0].value.data || []; else this.error = results[0].reason.message; if (results[1].status === 'fulfilled') this.contacts = results[1].value.data || []; else this.error = results[1].reason.message; },
    select(row) { clearTimeout(this.polling); this.generation++; this.selected = row; this.messages = []; this.readCursor = 0; this.body = ''; this.error = ''; this.poll(this.generation); },
    mergeMessages(rows) {
      const history = this.$refs?.history;
      const followLatest = !history || history.scrollHeight - history.scrollTop - history.clientHeight < 80;
      const byId = new Map(this.messages.map(row => [Number(row.id), row]));
      for (const row of rows) byId.set(Number(row.id), row);
      this.messages = [...byId.values()].sort((a, b) => Number(a.id) - Number(b.id));
      if (followLatest) this.$nextTick?.(() => { if (this.$refs?.history) this.$refs.history.scrollTop = this.$refs.history.scrollHeight; });
    },
    async poll(generation) {
      const id = this.selected?.id;
      if (!id || this.stopped || generation !== this.generation) return;
      try {
        while (!this.stopped && generation === this.generation) {
          const res = await support.messages(id, this.readCursor);
          if (generation !== this.generation || this.stopped) return;
          const rows = res.data || [];
          this.mergeMessages(rows);
          const nextCursor = Math.max(this.readCursor, ...rows.map(row => Number(row.id) || 0));
          const advanced = nextCursor > this.readCursor;
          this.readCursor = nextCursor;
          if (rows.length < 100 || !advanced) break;
        }
        this.error = '';
      } catch (error) {
        if (generation === this.generation) this.error = error.message;
      } finally {
        if (!this.stopped && generation === this.generation) this.polling = setTimeout(() => this.poll(generation), 4000);
      }
    },
    async create() { this.busy = true; this.error = ''; try { const payload = this.role === 'hoc_vien' ? { id_giao_vien: this.contactId } : { id_hoc_vien: this.contactId }; const res = await support.conversation(payload); await this.load(); this.select(res.data); } catch (error) { this.error = error.message; } finally { this.busy = false; } },
    async send() {
      if (!this.selected || this.busy) return;
      this.busy = true; this.error = '';
      const id = this.selected.id, generation = this.generation, body = this.body;
      try {
        const res = await support.sendMessage(id, body);
        if (this.selected?.id === id && this.generation === generation) {
          if (res.data) this.mergeMessages([res.data]);
          if (this.body === body) this.body = '';
        }
      } catch (error) {
        if (this.generation === generation) this.error = error.message || 'Tin nhắn chưa được gửi.';
      } finally { this.busy = false; }
    }
  }
};
</script>

<style scoped>
.consultation-page { --consult-accent: #2563eb; --consult-soft: #eff6ff; --consult-accent-border: #bfdbfe; width: 100%; min-width: 0; max-width: 1240px; margin: 0 auto; padding: 26px 24px 32px; color: #0f172a; }
.consult-teacher { --consult-accent: #0f766e; --consult-soft: #f0fdfa; --consult-accent-border: #99d5ca; }
.consult-page-header { display: flex; align-items: center; gap: 24px; padding: 20px 22px; background: var(--consult-soft); border: 1px solid var(--consult-accent-border); border-radius: 14px; }
.consult-account { display: flex; align-items: center; gap: 12px; min-width: 230px; max-width: 45%; padding-right: 24px; border-right: 1px solid var(--consult-accent-border); }
.consult-account strong { display: block; font-size: 15px; line-height: 1.4; overflow-wrap: anywhere; }
.consult-account-role { color: var(--consult-accent); font-size: 13px; font-weight: 700; margin: 0 0 3px; }
.consult-role-icon { display: grid; place-items: center; flex: 0 0 42px; height: 42px; border-radius: 12px; background: var(--consult-accent); color: #fff; font-size: 18px; }
.consult-intro h1 { font-size: 22px; font-weight: 750; line-height: 1.3; margin: 0 0 5px; }
.consult-intro p { font-size: 13px; line-height: 1.5; color: #475569; margin: 0; }
.consult-layout { display: grid; grid-template-columns: 230px minmax(0, 1fr); gap: 18px; margin-top: 20px; align-items: start; }
.consult-sidebar, .consult-panel { min-width: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; }
.consult-list-heading { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 18px 16px 12px; }
.consult-list-heading h2 { font-size: 15px; font-weight: 700; margin: 0; }
.consult-list-heading > span { display: grid; place-items: center; min-width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; font-size: 12px; color: #64748b; }
.consult-conversations { max-height: 370px; overflow-y: auto; padding: 0 8px 10px; }
.consult-contact { display: flex; align-items: center; gap: 10px; width: 100%; border: 1px solid transparent; border-radius: 10px; background: transparent; color: #334155; text-align: left; padding: 12px 8px; margin-bottom: 3px; }
.consult-contact:hover { background: #f8fafc; }
.consult-contact.is-selected { background: var(--consult-soft); border-color: var(--consult-accent-border); color: var(--consult-accent); }
.consult-avatar { display: grid; place-items: center; flex: 0 0 36px; height: 36px; border-radius: 50%; font-size: 12px; font-weight: 750; background: #eaf0f6; color: #475569; }
.is-selected .consult-avatar { background: var(--consult-accent); color: #fff; }
.consult-contact-info { flex: 1; min-width: 0; }
.consult-contact-info strong { display: block; font-size: 13px; font-weight: 650; line-height: 1.45; overflow-wrap: anywhere; }
.consult-contact-info small { display: block; font-size: 11px; color: #64748b; margin-top: 3px; }
.consult-contact-arrow { font-size: 10px; }
.consult-list-empty { margin: 8px; font-size: 13px; line-height: 1.6; color: #64748b; }
.consult-new-conversation { border-top: 1px solid #e2e8f0; padding: 15px 16px; }
.consult-new-conversation summary { font-size: 13px; font-weight: 650; cursor: pointer; color: #475569; }
.consult-new-conversation form { display: grid; gap: 10px; margin-top: 14px; }
.consult-new-conversation label { font-size: 12px; color: #64748b; }
.consult-new-conversation select { font-size: 13px; }
.consult-panel { display: flex; flex-direction: column; height: clamp(400px, 59vh, 600px); }
.consult-peer-header { display: flex; align-items: center; gap: 12px; flex-shrink: 0; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; }
.consult-peer-avatar { flex-basis: 40px; height: 40px; }
.consult-peer-header p { color: #64748b; font-size: 12px; margin: 0 0 3px; }
.consult-peer-header h2 { font-size: 16px; font-weight: 700; margin: 0; line-height: 1.4; overflow-wrap: anywhere; }
.consult-history { flex: 1; min-height: 0; overflow-y: auto; background: #f8fafc; padding: 22px 20px; }
.consult-message { display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 20px; }
.consult-message.is-own { align-items: flex-end; }
.consult-message-sender { font-size: 11px; font-weight: 650; color: #475569; margin: 0 2px 6px; max-width: 90%; }
.consult-message-sender span { display: inline-block; font-size: 10px; font-weight: 500; color: #64748b; margin-left: 7px; }
.consult-message-bubble { max-width: 88%; min-width: 95px; padding: 10px 13px 7px; border: 1px solid #e2e8f0; border-radius: 2px 13px 13px; background: #fff; }
.is-own .consult-message-bubble { border-color: var(--consult-accent); border-radius: 13px 2px 13px 13px; background: var(--consult-accent); color: #fff; }
.consult-message-bubble p { white-space: pre-wrap; overflow-wrap: anywhere; font-size: 14px; line-height: 1.6; margin: 0; }
.consult-message-bubble time { display: block; font-size: 10px; text-align: right; color: #64748b; margin-top: 4px; }
.is-own .consult-message-bubble time { color: #fff; opacity: .8; }
.consult-composer { display: flex; align-items: center; gap: 10px; flex-shrink: 0; border-top: 1px solid #e2e8f0; padding: 14px 16px; }
.consult-composer input { min-width: 0; font-size: 13px; padding: 12px; border-color: #dbe3ed; border-radius: 9px; }
.consult-primary-button { display: inline-flex; justify-content: center; align-items: center; gap: 9px; border: 1px solid var(--consult-accent); border-radius: 9px; padding: 11px 15px; background: var(--consult-accent); color: #fff; font-size: 13px; font-weight: 650; white-space: nowrap; }
.consult-primary-button:disabled { opacity: .5; cursor: not-allowed; }
.consult-primary-button:not(:disabled):hover { filter: brightness(.93); }
.consult-contact:focus-visible, .consult-primary-button:focus-visible, .consult-new-conversation summary:focus-visible { outline: 3px solid var(--consult-accent); outline-offset: 2px; }
.consult-composer input:focus, .consult-new-conversation select:focus { border-color: var(--consult-accent); box-shadow: 0 0 0 3px var(--consult-soft); }
.consult-history-empty, .consult-unselected { display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: #64748b; padding: 26px; min-height: 100%; }
.consult-unselected { flex: 1; }
.consult-history-empty > i, .consult-unselected > i { font-size: 30px; color: #94a3b8; margin-bottom: 14px; }
.consult-history-empty h3, .consult-unselected h2 { font-size: 17px; color: #334155; margin: 0 0 8px; }
.consult-history-empty p, .consult-unselected p { font-size: 13px; line-height: 1.6; margin: 0; }
@media (max-width: 950px) {
  .consultation-page { padding: 20px 18px 28px; }
  .consult-page-header { gap: 18px; padding: 18px; }
  .consult-account { min-width: 205px; padding-right: 18px; }
  .consult-intro h1 { font-size: 20px; }
  .consult-layout { grid-template-columns: 205px minmax(0, 1fr); gap: 14px; }
  .consult-peer-header { padding: 14px; }
  .consult-history { padding: 18px 14px; }
}
@media (max-width: 620px) {
  .consultation-page { padding: 16px 12px 24px; }
  .consult-page-header { flex-direction: column; align-items: stretch; gap: 13px; }
  .consult-account { max-width: none; border-right: 0; border-bottom: 1px solid var(--consult-accent-border); padding: 0 0 13px; }
  .consult-intro h1 { font-size: 19px; }
  .consult-layout { grid-template-columns: minmax(0, 1fr); }
  .consult-conversations { display: flex; overflow-x: auto; padding-bottom: 8px; }
  .consult-contact { flex: 0 0 210px; }
  .consult-panel { height: 490px; max-height: 75dvh; }
  .consult-composer { padding: 12px; gap: 7px; }
  .consult-message-bubble { max-width: 94%; }
}
</style>
