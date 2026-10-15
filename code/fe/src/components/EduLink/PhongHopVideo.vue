<template>
  <div class="lesson-room">
    <header class="lesson-header">
      <div class="lesson-heading"><h1>{{ tenPhongHoc || 'Buổi học ' + sessionId }}</h1><p>{{ currentUserName }} <span class="lesson-role" :class="currentUserRole">{{ currentUserRole === 'giao_vien' ? 'Bạn là giáo viên' : currentUserRole === 'hoc_vien' ? 'Bạn là học viên' : 'Tài khoản tham gia' }}</span></p></div>
      <div class="lesson-status"><span :class="['lesson-connection', { 'is-connected': state === 'connected' }]" role="status">{{ connectionLabel }}</span><span v-if="state === 'connected'">{{ participants.length }} người trong phòng</span></div>
    </header>

    <p v-if="error || mediaError" class="lesson-notice" role="alert">{{ error }} {{ mediaError }}</p>
    <button v-if="audioPlaybackBlocked && state === 'connected'" class="lesson-audio-notice" @click="startAudio">Bật âm thanh phòng học</button>
    <div id="remote-audio" hidden></div>

    <main class="lesson-stage" :class="{ 'has-sidebar': isChatOpen || isParticipantsOpen }">
      <section v-if="needsVerification" class="lesson-lobby" aria-label="Xác thực trước khi vào lớp">
        <div class="lesson-lobby-intro"><span class="lesson-step">Trước khi vào lớp</span><h2>Xác thực khuôn mặt của bạn</h2><p>Bạn chưa vào phòng. Quét Face ID để xác thực, sau đó hệ thống mới kết nối buổi học.</p></div>
        <FaceProof v-if="sessionId" purpose="room" :target="{ id_buoi_hoc: sessionId }" @verified="connect" @cancel="roiPhong" />
        <p v-else role="alert">Chưa chọn buổi học. Hãy quay lại lịch học để chọn buổi.</p>
      </section>
      <div v-else-if="state === 'connecting'" class="lesson-connecting" role="status"><span class="spinner-border" aria-hidden="true"></span><h2>Đang kết nối buổi học</h2><p>Face ID đã được tiếp nhận. Chờ máy chủ xác nhận kết nối phòng.</p></div>
      <div v-else class="lesson-grid">
        <article v-for="share in screenShares" :key="share.key" class="lesson-share"><div :id="'lesson-share-' + share.key" class="lesson-video-host"></div><p>{{ share.name }} đang chia sẻ màn hình</p></article>
        <article v-for="participant in participants" :key="participant.identity" class="lesson-person" :class="participant.role" :aria-label="'Ô người tham gia: ' + participant.name + ' — ' + participant.roleLabel">
          <div :id="'lesson-camera-' + participant.sid" class="lesson-video-host"></div>
          <div v-if="!participant.videoEnabled" class="lesson-camera-off"><span class="lesson-initials">{{ getParticipantInitial(participant) }}</span><h2>{{ participant.name }}</h2><span class="lesson-role" :class="participant.role">{{ participant.roleLabel }}{{ participant.isLocal ? ' · Bạn' : '' }}</span><p><i class="bx bx-video-off" aria-hidden="true"></i> {{ participant.cameraEnabled ? 'Đang nhận hình ảnh…' : 'Camera đang tắt' }}</p></div>
          <div class="lesson-nameplate"><div><strong>{{ participant.name }}</strong><span>{{ participant.roleLabel }}{{ participant.isLocal ? ' · Bạn' : '' }}</span></div><i :class="participant.audioEnabled ? 'bx bx-microphone' : 'bx bx-microphone-off is-muted'" :title="participant.audioEnabled ? 'Micro đang bật' : 'Micro đang tắt'"></i></div>
        </article>
        <p v-if="!participants.length" class="lesson-empty">Chưa có kết nối với người tham gia nào.</p>
      </div>
    </main>

    <footer class="lesson-footer">
      <div class="lesson-toolbar">
        <button @click="toggleMic" :disabled="!canUseRoom" :class="{ 'is-off': !isMicOn }" :aria-label="isMicOn ? 'Tắt Micro' : 'Bật Micro'" :title="isMicOn ? 'Tắt Micro' : 'Bật Micro'"><i :class="isMicOn ? 'bx bx-microphone' : 'bx bx-microphone-off'" aria-hidden="true"></i></button>
        <button @click="toggleCamera" :disabled="!canUseRoom" :class="{ 'is-off': !isCameraOn }" :aria-label="isCameraOn ? 'Tắt Camera' : 'Bật Camera'" :title="isCameraOn ? 'Tắt Camera' : 'Bật Camera'"><i :class="isCameraOn ? 'bx bx-video' : 'bx bx-video-off'" aria-hidden="true"></i></button>
        <button @click="toggleScreenShare" :disabled="!canUseRoom" :class="{ 'is-active': isSharingScreen }" :aria-label="isSharingScreen ? 'Dừng chia sẻ màn hình' : 'Chia sẻ màn hình'" title="Chia sẻ màn hình"><i class="bx bx-desktop" aria-hidden="true"></i></button>
        <button @click="openSettings" :disabled="!canUseRoom" aria-label="Cài đặt thiết bị" title="Cài đặt thiết bị"><i class="bx bx-slider-alt" aria-hidden="true"></i></button>
        <button @click="toggleChat" :disabled="!canUseRoom" :class="{ 'is-active': isChatOpen }" aria-label="Mở Chat" title="Mở Chat"><i class="bx bx-message-rounded-dots" aria-hidden="true"></i></button>
        <button @click="toggleParticipants" :disabled="!canUseRoom" :class="{ 'is-active': isParticipantsOpen }" aria-label="Danh sách người tham gia" title="Danh sách người tham gia"><i class="bx bx-group" aria-hidden="true"></i><span v-if="canUseRoom">{{ participants.length }}</span></button>
        <button @click="roiPhong" class="lesson-leave"><i class="bx bx-phone-off" aria-hidden="true"></i><span>{{ canUseRoom ? 'Rời phòng' : 'Quay lại' }}</span></button>
      </div>
    </footer>

    <aside v-if="isChatOpen" class="lesson-sidebar" aria-label="Chat trong phòng học">
      <header><h2>Trò chuyện trong phòng</h2><button @click="toggleChat" aria-label="Đóng chat">×</button></header>
      <div ref="chatBox" class="lesson-messages" role="log" aria-live="polite"><div v-for="(msg, index) in chatMessages" :key="index" class="lesson-message" :class="{ 'is-own': msg.isLocal }"><span>{{ msg.sender }} · {{ msg.timestamp }}</span><p>{{ msg.text }}</p></div><p v-if="!chatMessages.length" class="lesson-sidebar-empty">Gửi tin nhắn cho những người đang ở cùng phòng.</p></div>
      <form @submit.prevent="sendChatMessage" class="lesson-compose"><label for="lesson-message" class="visually-hidden">Tin nhắn trong phòng</label><input id="lesson-message" v-model="newMessage" placeholder="Nhập tin nhắn…" maxlength="2000" autocomplete="off" /><button :disabled="!newMessage.trim() || sending || !canUseRoom">{{ sending ? 'Đang gửi…' : 'Gửi' }}</button></form>
    </aside>

    <aside v-if="isParticipantsOpen" class="lesson-sidebar" aria-label="Người đang kết nối">
      <header><h2>Người đang kết nối ({{ participants.length }})</h2><button @click="toggleParticipants" aria-label="Đóng danh sách">×</button></header>
      <div class="lesson-participant-list"><div v-for="participant in participants" :key="participant.identity" class="lesson-participant-row"><span class="lesson-list-initials">{{ getParticipantInitial(participant) }}</span><div><strong>{{ participant.name }}</strong><p>{{ participant.roleLabel }}{{ participant.isLocal ? ' · Bạn' : '' }}</p></div><i :class="participant.audioEnabled ? 'bx bx-microphone' : 'bx bx-microphone-off is-muted'"></i></div><p v-if="!participants.length" class="lesson-sidebar-empty">Chưa có người tham gia kết nối.</p></div>
    </aside>

    <div v-if="showSettings" class="lesson-settings-overlay" @click.self="showSettings = false">
      <section class="lesson-settings" role="dialog" aria-modal="true" aria-labelledby="lesson-settings-title"><header><h2 id="lesson-settings-title">Cài đặt thiết bị</h2><button @click="showSettings = false" aria-label="Đóng cài đặt">×</button></header><label>Micro<select v-model="selectedMic" @change="switchDevice('audioinput', selectedMic)"><option v-for="device in audioInputs" :key="device.deviceId" :value="device.deviceId">{{ device.label || 'Micro mặc định' }}</option></select></label><label>Loa<select v-model="selectedSpeaker" @change="switchDevice('audiooutput', selectedSpeaker)"><option v-for="device in audioOutputs" :key="device.deviceId" :value="device.deviceId">{{ device.label || 'Loa mặc định' }}</option></select></label><button class="lesson-settings-done" @click="showSettings = false">Hoàn tất</button></section>
    </div>
  </div>
</template>

<script>
import { markRaw } from 'vue';
import FaceProof from './FaceProof.vue';
import product from '../../services/productService';
import http from '../../services/http';
import { accepted } from '../../services/productContract';
import { attachTrack, sendRoomMessage } from '../../services/flowHelpers';

export default {
  name: 'PhongHopVideo',
  components: { FaceProof },
  data() {
    let account = null;
    try { account = JSON.parse(localStorage.getItem('user') || localStorage.getItem('edulink_user') || 'null'); } catch { /* The lobby can still identify the role without a cached profile. */ }
    const currentUserRole = localStorage.getItem('role') || '';
    return {
      roomId: this.$route.params.id, sessionId: Number(this.$route.query.session || this.$route.params.id),
      tenPhongHoc: '', phongHopId: null, room: null, sdk: null,
      state: 'verification', error: '', mediaError: '', disposed: false, joined: false, leaving: false, leavePromise: null,
      cameraReady: false, isMicOn: false, isCameraOn: false, isSharingScreen: false, audioPlaybackBlocked: false,
      currentUserRole, currentUserName: account?.ho_ten || account?.name || (currentUserRole === 'giao_vien' ? 'Tài khoản giáo viên' : 'Tài khoản học viên'),
      showSettings: false, audioInputs: [], audioOutputs: [], selectedMic: '', selectedSpeaker: '',
      isChatOpen: false, chatMessages: [], newMessage: '', sending: false,
      isParticipantsOpen: false, participants: [], screenShares: [], mediaAttachments: markRaw(new Map()),
    };
  },
  computed: {
    connectionLabel() { return { verification: 'Chờ xác thực Face ID', connecting: 'Đang kết nối', connected: 'Đã kết nối phòng học', reconnecting: 'Đang kết nối lại', disconnected: 'Đã ngắt kết nối', failed: 'Kết nối thất bại' }[this.state] || 'Chưa kết nối'; },
    needsVerification() { return ['verification', 'failed', 'disconnected'].includes(this.state); },
    canUseRoom() { return this.state === 'connected'; },
  },
  beforeUnmount() { this.cleanup(); },
  methods: {
    roleLabel(role) { return { hoc_vien: 'Học viên', giao_vien: 'Giáo viên' }[role] || 'Thành viên'; },
    roleFromIdentity(identity) { const role = String(identity || '').split(':')[0]; return ['hoc_vien', 'giao_vien'].includes(role) ? role : ''; },
    bindRoomEvents(room) {
      const { RoomEvent } = this.sdk;
      for (const event of [RoomEvent.ParticipantConnected, RoomEvent.ParticipantDisconnected, RoomEvent.TrackPublished, RoomEvent.TrackUnpublished, RoomEvent.TrackSubscribed, RoomEvent.TrackUnsubscribed, RoomEvent.LocalTrackPublished, RoomEvent.LocalTrackUnpublished, RoomEvent.TrackMuted, RoomEvent.TrackUnmuted, RoomEvent.ParticipantNameChanged]) {
        room.on(event, () => { queueMicrotask(() => { if (!this.disposed && this.room === room) this.syncParticipants(); }); });
      }
      room.on(RoomEvent.Reconnecting, () => { if (!this.disposed) this.state = 'reconnecting'; });
      room.on(RoomEvent.Reconnected, () => { if (!this.disposed) { this.state = 'connected'; this.syncParticipants(); } });
      room.on(RoomEvent.AudioPlaybackStatusChanged, () => { if (!this.disposed) this.audioPlaybackBlocked = !room.canPlaybackAudio; });
      room.on(RoomEvent.Disconnected, () => {
        if (this.disposed) return;
        this.state = 'disconnected'; this.isMicOn = false; this.isCameraOn = false; this.cameraReady = false;
        this.participants = []; this.screenShares = []; this.clearMedia(); this.leaveAttendance();
      });
      room.on(RoomEvent.DataReceived, (payload, participant, kind, topic) => {
        if (!participant || topic !== 'class-chat' || this.disposed) return;
        try {
          const message = JSON.parse(new TextDecoder().decode(payload));
          if (typeof message.text !== 'string' || !message.text.trim() || message.text.length > 2000) return;
          this.chatMessages.push({ text: message.text, sender: participant.name || this.roleLabel(this.roleFromIdentity(participant.identity)), timestamp: new Date().toLocaleTimeString('vi-VN'), isLocal: false, status: 'received' });
          this.scrollChatToBottom();
        } catch { /* Ignore payloads outside the room-chat contract. */ }
      });
    },
    async connect(proof) {
      if (this.disposed || this.state === 'connecting' || this.state === 'connected' || !proof?.verification_id) return;
      this.error = ''; this.mediaError = ''; this.state = 'connecting';
      try {
        if (this.room) { await this.room.disconnect(); this.room = null; this.clearMedia(); }
        this.state = 'connecting';
        await this.leaveAttendance();
        if (this.disposed) return;
        if (this.joined) throw new Error('Chưa ghi nhận được lần rời phòng trước. Hãy thử lại trước khi vào lại lớp.');
        const result = await product.roomToken(this.sessionId, proof.verification_id);
        if (this.disposed) return;
        const data = result.data;
        if (!data?.token || !data?.server_url) throw new Error('Máy chủ chưa cấp thông tin kết nối phòng học.');
        this.roomId = data.ma_phong; this.phongHopId = data.id_phong_hop; this.tenPhongHoc = data.ten_phong || '';
        this.sdk = markRaw({ ...(await import('livekit-client')) });
        if (this.disposed) return;
        const room = markRaw(new this.sdk.Room({ adaptiveStream: true, dynacast: true }));
        this.room = room; this.bindRoomEvents(room);
        await room.connect(data.server_url, data.token);
        if (this.disposed) { await room.disconnect(); return; }
        await accepted(http.post('/chi-tiet-phong-hop/create', { id_buoi_hoc: this.sessionId, attendance_token: data.attendance_token }));
        this.joined = true;
        if (this.disposed) { await this.leaveAttendance(); await room.disconnect(); return; }
        this.currentUserName = room.localParticipant.name || this.currentUserName;
        this.currentUserRole = this.roleFromIdentity(room.localParticipant.identity) || this.currentUserRole;
        this.state = 'connected'; this.audioPlaybackBlocked = !room.canPlaybackAudio; this.syncParticipants();
        try { await room.localParticipant.setCameraEnabled(true); }
        catch { this.mediaError = 'Không mở được camera. Bạn vẫn ở trong phòng và có thể bật camera khi đã cấp quyền.'; }
        try { await room.localParticipant.setMicrophoneEnabled(true); }
        catch { this.mediaError += ' Không mở được micro. Kiểm tra quyền rồi bật lại.'; }
        this.syncParticipants();
      } catch (error) {
        this.error = error.message || 'Không thể kết nối phòng học. Kiểm tra mạng và dịch vụ video.';
        const room = this.room; this.room = null;
        if (room) await room.disconnect();
        this.state = 'failed'; this.participants = []; this.screenShares = []; this.clearMedia();
      }
    },
    syncParticipants() {
      if (!this.room || this.room.state !== 'connected') { this.participants = []; this.screenShares = []; return; }
      const people = [this.room.localParticipant, ...this.room.remoteParticipants.values()];
      this.participants = people.map(participant => {
        const isLocal = participant === this.room.localParticipant;
        const role = this.roleFromIdentity(participant.identity) || (isLocal ? this.currentUserRole : '');
        const camera = [...participant.videoTrackPublications.values()].find(pub => pub.source === 'camera');
        return { sid: participant.sid, identity: participant.identity, name: participant.name || (isLocal ? this.currentUserName : this.roleLabel(role)), role, roleLabel: this.roleLabel(role), isLocal,
          audioEnabled: participant.isMicrophoneEnabled, cameraEnabled: participant.isCameraEnabled, videoEnabled: !!(camera?.track && !camera.isMuted) };
      }).sort((a, b) => (a.role === 'giao_vien' ? 0 : 1) - (b.role === 'giao_vien' ? 0 : 1) || a.name.localeCompare(b.name, 'vi'));
      this.screenShares = people.flatMap(participant => [...participant.videoTrackPublications.values()].filter(pub => pub.source === 'screen_share' && pub.track && !pub.isMuted).map(pub => ({ key: pub.trackSid, name: participant.name || 'Thành viên' })));
      this.isCameraOn = this.room.localParticipant.isCameraEnabled; this.cameraReady = this.isCameraOn;
      this.isMicOn = this.room.localParticipant.isMicrophoneEnabled; this.isSharingScreen = this.room.localParticipant.isScreenShareEnabled;
      this.$nextTick(() => this.attachPublishedTracks());
    },
    attachPublishedTracks() {
      if (!this.room || this.disposed || this.room.state !== 'connected') return;
      const desired = new Set();
      for (const participant of [this.room.localParticipant, ...this.room.remoteParticipants.values()]) {
        const local = participant === this.room.localParticipant;
        for (const pub of [...participant.videoTrackPublications.values(), ...participant.audioTrackPublications.values()]) {
          if (!pub.track || pub.isMuted || (pub.track.kind === 'audio' && local)) continue;
          const id = pub.track.kind === 'audio' ? 'remote-audio' : pub.source === 'screen_share' ? 'lesson-share-' + pub.trackSid : pub.source === 'camera' ? 'lesson-camera-' + participant.sid : null;
          const container = id ? document.getElementById(id) : null;
          if (!container) continue;
          const key = participant.identity + ':' + pub.trackSid;
          desired.add(key);
          const existing = this.mediaAttachments.get(key);
          if (existing?.track === pub.track && existing.node.parentElement === container) continue;
          if (existing) { existing.track.detach(existing.node); existing.node.remove(); }
          const node = attachTrack(pub.track, container);
          if (local) node.muted = true;
          node.style.cssText = 'width:100%;height:100%;object-fit:' + (pub.source === 'screen_share' ? 'contain' : 'cover') + ';';
          this.mediaAttachments.set(key, { track: pub.track, node });
        }
      }
      for (const [key, entry] of this.mediaAttachments) {
        if (desired.has(key)) continue;
        entry.track.detach(entry.node); entry.node.remove(); this.mediaAttachments.delete(key);
      }
    },
    clearMedia() { for (const entry of this.mediaAttachments.values()) { entry.track.detach(entry.node); entry.node.remove(); } this.mediaAttachments.clear(); },
    async toggleMic() { if (!this.canUseRoom) return; try { await this.room.localParticipant.setMicrophoneEnabled(!this.isMicOn); this.syncParticipants(); } catch { this.mediaError = 'Không thể bật/tắt micro. Kiểm tra quyền thiết bị.'; } },
    async toggleCamera() { if (!this.canUseRoom) return; try { await this.room.localParticipant.setCameraEnabled(!this.isCameraOn); this.syncParticipants(); } catch { this.mediaError = 'Không thể bật/tắt camera. Kiểm tra quyền thiết bị.'; } },
    async toggleScreenShare() { if (!this.canUseRoom) return; try { await this.room.localParticipant.setScreenShareEnabled(!this.isSharingScreen); this.syncParticipants(); } catch { this.mediaError = 'Không thể chia sẻ màn hình hoặc bạn đã hủy lựa chọn.'; } },
    async startAudio() { try { await this.room.startAudio(); this.audioPlaybackBlocked = !this.room.canPlaybackAudio; } catch { this.mediaError = 'Trình duyệt chưa cho phát âm thanh. Kiểm tra quyền âm thanh của trang.'; } },
    async openSettings() {
      this.showSettings = true;
      try { const devices = await navigator.mediaDevices.enumerateDevices(); this.audioInputs = devices.filter(device => device.kind === 'audioinput'); this.audioOutputs = devices.filter(device => device.kind === 'audiooutput'); }
      catch { this.mediaError = 'Không thể đọc danh sách thiết bị.'; }
    },
    async switchDevice(kind, id) { if (!this.canUseRoom) return; try { await this.room.switchActiveDevice(kind, id); } catch { this.mediaError = 'Trình duyệt hoặc thiết bị không hỗ trợ lựa chọn này.'; } },
    toggleChat() { this.isChatOpen = !this.isChatOpen; this.isParticipantsOpen = false; this.scrollChatToBottom(); },
    toggleParticipants() { this.isParticipantsOpen = !this.isParticipantsOpen; this.isChatOpen = false; },
    getParticipantInitial(participant) { return String(participant.name || '?').trim().split(/\s+/).slice(-2).map(part => part[0]).join('').toLocaleUpperCase('vi-VN'); },
    scrollChatToBottom() { this.$nextTick(() => { if (this.$refs.chatBox) this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight; }); },
    async sendChatMessage() {
      if (this.sending) return;
      this.sending = true; this.error = '';
      try { const sent = await sendRoomMessage(this.room, this.newMessage); this.chatMessages.push({ ...sent, sender: this.currentUserName, timestamp: new Date().toLocaleTimeString('vi-VN') }); this.newMessage = ''; this.scrollChatToBottom(); }
      catch (error) { this.error = error.message || 'Tin nhắn chưa được gửi. Vui lòng thử lại.'; }
      finally { this.sending = false; }
    },
    async cleanup() {
      this.disposed = true;
      const room = this.room; this.room = null; this.clearMedia();
      if (room) await room.disconnect();
      this.participants = []; this.screenShares = []; await this.leaveAttendance();
    },
    leaveAttendance() {
      if (this.leavePromise) return this.leavePromise;
      if (!this.joined) return Promise.resolve();
      this.leaving = true;
      this.leavePromise = Promise.resolve().then(async () => {
        try { await accepted(http.post('/phong-hop/roi-phong', { id_buoi_hoc: this.sessionId })); this.joined = false; }
        catch (error) { this.error = error.message || 'Đã ngắt video nhưng chưa ghi nhận rời phòng. Vui lòng thử lại.'; }
        finally { this.leaving = false; this.leavePromise = null; }
      });
      return this.leavePromise;
    },
    async roiPhong() { await this.cleanup(); if (this.joined) return; this.$router.push(this.currentUserRole === 'giao_vien' ? '/giao-vien/lich-day' : '/hoc-vien/lich-hoc'); },
  },
};
</script>

<style scoped>
.lesson-room { position: relative; display: flex; flex-direction: column; width: 100%; height: 100dvh; min-height: 560px; background: #0f172a; color: #e2e8f0; overflow: hidden; }
.lesson-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; padding: 18px 24px; background: #111c30; border-bottom: 1px solid #29364b; z-index: 2; }
.lesson-heading h1 { margin: 0 0 7px; font-size: 18px; font-weight: 700; color: #fff; }
.lesson-heading p { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; font-size: 13px; margin: 0; }
.lesson-role { display: inline-block; font-size: 11px; font-weight: 650; border-radius: 5px; padding: 3px 7px; background: #334155; color: #e2e8f0; }
.lesson-role.hoc_vien { background: #1e3a5f; color: #bfdbfe; }
.lesson-role.giao_vien { background: #134e4a; color: #99f6e4; }
.lesson-status { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; font-size: 12px; color: #94a3b8; }
.lesson-connection { padding: 6px 10px; border-radius: 7px; border: 1px solid #6b5727; background: #332b1a; color: #fde68a; font-weight: 600; }
.lesson-connection.is-connected { border-color: #226551; background: #143a30; color: #a7f3d0; }
.lesson-notice, .lesson-audio-notice { flex-shrink: 0; margin: 10px 20px 0; border: 1px solid #80632d; background: #3b301b; color: #fde9b0; padding: 11px 15px; border-radius: 8px; font-size: 13px; }
.lesson-stage { flex: 1; min-height: 0; overflow-y: auto; padding: 22px; }
.lesson-stage.has-sidebar { margin-right: 340px; }
.lesson-lobby { width: min(100%, 410px); margin: 0 auto; padding: 8px 0 16px; }
.lesson-lobby-intro { text-align: center; margin-bottom: 18px; }
.lesson-step { display: block; color: #93c5fd; font-size: 12px; margin-bottom: 9px; }
.lesson-lobby-intro h2 { font-size: 21px; font-weight: 700; color: #fff; margin: 0 0 10px; }
.lesson-lobby-intro p { margin: 0; font-size: 13px; line-height: 1.7; color: #cbd5e1; }
.lesson-lobby :deep(.face-proof) { color: #0f172a; border: 0; border-radius: 12px; }
.lesson-lobby :deep(.face-proof video) { max-width: 100%; }
.lesson-connecting, .lesson-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100%; text-align: center; color: #cbd5e1; }
.lesson-connecting h2 { font-size: 20px; color: #fff; margin: 20px 0 10px; }
.lesson-connecting p { font-size: 13px; }
.lesson-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 275px), 1fr)); align-content: center; gap: 18px; min-height: 100%; }
.lesson-person, .lesson-share { position: relative; box-sizing: border-box; width: 100%; min-width: 0; min-height: 300px; aspect-ratio: 16/10; background: #1c2940; border: 1px solid #3a4b65; border-radius: 14px; overflow: hidden; }
.lesson-person.giao_vien { border-color: #306e68; }
.lesson-person.hoc_vien { border-color: #365f96; }
.lesson-video-host { position: absolute; inset: 0; }
.lesson-camera-off { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px 16px 72px; text-align: center; }
.lesson-initials { display: grid; place-items: center; width: 72px; height: 72px; border-radius: 50%; background: #2c466a; color: #dbeafe; font-size: 24px; font-weight: 700; margin-bottom: 14px; }
.giao_vien .lesson-initials { background: #1d5753; color: #ccfbf1; }
.lesson-camera-off h2 { font-size: 17px; font-weight: 700; color: #fff; margin: 0 0 8px; overflow-wrap: anywhere; }
.lesson-camera-off p { color: #a8b7cc; font-size: 12px; margin: 12px 0 0; }
.lesson-nameplate { position: absolute; inset: auto 0 0; display: flex; align-items: center; justify-content: space-between; gap: 10px; background: #111c30eb; padding: 12px 15px; color: #fff; }
.lesson-nameplate strong { display: block; font-size: 13px; font-weight: 650; overflow-wrap: anywhere; }
.lesson-nameplate span { display: block; font-size: 11px; color: #b4c3d7; margin-top: 3px; }
.lesson-nameplate > i { font-size: 20px; }
.is-muted { color: #fda4af; }
.lesson-share { grid-column: 1/-1; aspect-ratio: 16/9; background: #000; }
.lesson-share > p { position: absolute; left: 12px; bottom: 8px; background: #111c30e8; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
.lesson-footer { flex-shrink: 0; display: flex; justify-content: center; padding: 15px 14px 20px; background: #0f172a; z-index: 3; }
.lesson-toolbar { display: flex; align-items: center; gap: 9px; border: 1px solid #334155; background: #172337; border-radius: 16px; padding: 10px; }
.lesson-toolbar button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 44px; min-width: 44px; border: 0; border-radius: 10px; background: #34445c; color: #fff; font-size: 20px; }
.lesson-toolbar button:disabled { opacity: .35; cursor: not-allowed; }
.lesson-toolbar button.is-off { background: #6b2c3a; }
.lesson-toolbar button.is-active { background: #2563eb; }
.lesson-toolbar button.lesson-leave { padding: 0 14px; margin-left: 7px; background: #dc3655; font-size: 13px; font-weight: 650; }
.lesson-toolbar button > span { font-size: 12px; }
.lesson-toolbar button:focus-visible, .lesson-sidebar button:focus-visible { outline: 3px solid #93c5fd; outline-offset: 2px; }
.lesson-sidebar { position: absolute; right: 0; top: 95px; bottom: 100px; width: 340px; max-width: 94vw; display: flex; flex-direction: column; z-index: 4; background: #162238; border: 1px solid #334155; border-radius: 12px 0 0 12px; }
.lesson-sidebar > header, .lesson-settings > header { display: flex; justify-content: space-between; align-items: center; padding: 16px; gap: 10px; border-bottom: 1px solid #334155; }
.lesson-sidebar h2, .lesson-settings h2 { margin: 0; color: #fff; font-size: 15px; font-weight: 650; }
.lesson-sidebar header button, .lesson-settings header button { border: 0; background: transparent; color: #cbd5e1; font-size: 24px; }
.lesson-messages, .lesson-participant-list { flex: 1; min-height: 0; overflow: auto; padding: 16px; }
.lesson-message { display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 18px; }
.lesson-message.is-own { align-items: flex-end; }
.lesson-message > span { color: #cbd5e1; font-size: 11px; margin-bottom: 5px; }
.lesson-message > p { margin: 0; padding: 9px 12px; background: #34445c; color: #fff; font-size: 13px; line-height: 1.6; border-radius: 10px; max-width: 95%; overflow-wrap: anywhere; white-space: pre-wrap; }
.lesson-message.is-own > p { background: #2358b5; }
.lesson-compose { display: flex; gap: 7px; border-top: 1px solid #334155; padding: 13px; }
.lesson-compose input { min-width: 0; flex: 1; background: #0f172a; border: 1px solid #475569; color: #fff; padding: 10px; border-radius: 7px; font-size: 13px; }
.lesson-compose button, .lesson-settings-done { background: #2563eb; color: #fff; border: 0; border-radius: 7px; padding: 9px 12px; font-size: 13px; }
.lesson-compose button:disabled { opacity: .45; }
.lesson-sidebar-empty { color: #b4c3d7; font-size: 13px; line-height: 1.6; }
.lesson-participant-row { display: flex; align-items: center; gap: 10px; margin-bottom: 17px; }
.lesson-participant-row > div { flex: 1; min-width: 0; }
.lesson-participant-row strong { font-size: 13px; color: #fff; overflow-wrap: anywhere; }
.lesson-participant-row p { margin: 3px 0 0; font-size: 11px; color: #b4c3d7; }
.lesson-list-initials { display: grid; place-items: center; width: 38px; height: 38px; border-radius: 50%; background: #34445c; font-size: 12px; }
.lesson-settings-overlay { position: fixed; inset: 0; display: grid; place-items: center; padding: 16px; background: #000a; z-index: 20; }
.lesson-settings { width: min(400px, 100%); border-radius: 12px; background: #162238; border: 1px solid #475569; padding: 8px 16px 20px; }
.lesson-settings > header { padding: 10px 0 16px; }
.lesson-settings label { display: block; margin: 18px 0; font-size: 13px; color: #e2e8f0; }
.lesson-settings select { display: block; width: 100%; margin-top: 8px; padding: 9px; border-radius: 6px; background: #0f172a; color: #fff; border: 1px solid #475569; }
.lesson-settings-done { width: 100%; }
@media (max-width: 700px) { .lesson-header { padding: 14px; } .lesson-stage { padding: 16px; } .lesson-stage.has-sidebar { margin-right: 0; } .lesson-status { align-items: flex-start; } .lesson-sidebar { top: 115px; } .lesson-toolbar { gap: 6px; padding: 8px; } .lesson-toolbar button { min-width: 36px; height: 40px; font-size: 18px; } .lesson-toolbar button.lesson-leave { margin-left: 0; padding: 0 9px; } .lesson-footer { padding: 10px 8px 15px; } }
@media (max-width: 390px) { .lesson-toolbar { flex-wrap: wrap; justify-content: center; } .lesson-room { min-height: 620px; } }
</style>
