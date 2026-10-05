<template>
  <main class="container py-4">
    <h1 class="h3">Tư vấn học viên — giáo viên</h1>
    <p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p>
    <div class="row g-3"><aside class="col-md-4"><div class="card p-3">
      <h3 class="h5">Hội thoại</h3><button v-for="row in conversations" :key="row.id" class="btn btn-outline-primary mb-2 text-start" @click="select(row)">{{ row.counterparty?.ho_ten || row.giao_vien?.ho_ten || row.hoc_vien?.ho_ten || row.contact_name || 'Hội thoại #' + row.id }}</button>
      <form class="d-grid gap-2 mt-3" @submit.prevent="create"><label>Liên hệ mới<select v-model="contactId" class="form-select" required><option value="">Chọn tài khoản</option><option v-for="row in contacts" :key="row.id" :value="row.id">{{ row.ho_ten }}</option></select></label><button class="btn btn-primary" :disabled="busy">Mở hội thoại</button></form>
    </div></aside><section class="col-md-8"><div class="card p-3">
      <p v-if="!selected">Chọn hội thoại để đọc và gửi tin nhắn.</p>
      <template v-else><div style="height:360px;overflow-y:auto;" ref="history"><p v-for="row in messages" :key="row.id" class="border-bottom pb-2"><strong>{{ row.sender_role === role ? 'Bạn' : 'Người tư vấn' }}</strong> · {{ row.created_at }}<br />{{ row.body }}</p><p v-if="!messages.length">Chưa có tin nhắn.</p></div><form class="d-flex gap-2 mt-3" @submit.prevent="send"><input v-model="body" class="form-control" placeholder="Tin nhắn" maxlength="2000" required /><button class="btn btn-primary" :disabled="busy || !body.trim()">{{ busy ? 'Đang gửi…' : 'Gửi' }}</button></form></template>
    </div></section></div>
  </main>
</template>
<script>
import support from '../../services/supportService';
export default {
  data: () => ({ role: localStorage.getItem('role'), conversations: [], contacts: [], contactId: '', selected: null, messages: [], readCursor: 0, body: '', error: '', busy: false, polling: null, stopped: false, generation: 0 }),
  mounted() { this.load(); },
  beforeUnmount() { this.stopped = true; this.generation++; clearTimeout(this.polling); },
  methods: {
    async load() { const results = await Promise.allSettled([support.conversations(), support.contacts()]); if (results[0].status === 'fulfilled') this.conversations = results[0].value.data || []; else this.error = results[0].reason.message; if (results[1].status === 'fulfilled') this.contacts = results[1].value.data || []; else this.error = results[1].reason.message; },
    select(row) { clearTimeout(this.polling); this.generation++; this.selected = row; this.messages = []; this.readCursor = 0; this.body = ''; this.error = ''; this.poll(this.generation); },
    mergeMessages(rows) {
      const byId = new Map(this.messages.map(row => [Number(row.id), row]));
      for (const row of rows) byId.set(Number(row.id), row);
      this.messages = [...byId.values()].sort((a, b) => Number(a.id) - Number(b.id));
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
