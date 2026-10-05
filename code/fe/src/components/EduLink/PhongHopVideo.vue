<template>
    <div class="vh-100 d-flex flex-column bg-dark" style="background-color: #0f172a !important;">
        <header class="p-3 d-flex justify-content-between align-items-center position-absolute w-100 top-0 z-3"
            style="background: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, transparent 100%);">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-success rounded-circle" style="width: 10px; height: 10px; box-shadow: 0 0 10px #22c55e;">
                </div>
                <h6 class="mb-0 text-white fw-bold text-shadow">{{ tenPhongHoc ? tenPhongHoc + ' (' + roomId + ')' : 'Phòng học: ' + roomId }}</h6>
            </div>
            <div
                class="text-white bg-dark bg-opacity-50 px-3 py-1 rounded-pill border border-secondary small fw-medium backdrop-blur">
                <i class='bx bx-time-five me-1'></i> Đang diễn ra
            </div>
        </header>

        <div id="remote-audio" hidden></div>
        <div v-if="state === 'verification' || state === 'failed'" class="position-absolute top-50 start-50 translate-middle z-3" style="width:340px;max-width:95vw;">
          <FaceProof v-if="sessionId" purpose="room" :target="{ id_buoi_hoc: sessionId }" @verified="connect" @cancel="roiPhong" />
          <p v-else class="alert alert-danger">Chưa chọn buổi học. Hãy quay lại lịch học để chọn buổi.</p>
        </div>
        <div v-if="error || mediaError" class="alert alert-warning position-absolute top-0 start-50 translate-middle-x mt-5 z-3" role="alert">{{ error }} {{ mediaError }}</div>
        <button v-if="audioPlaybackBlocked && state === 'connected'" class="btn btn-warning position-absolute top-0 end-0 mt-5 z-3" @click="startAudio">Bật âm thanh phòng học</button>
        <main class="flex-grow-1 position-relative p-2 p-md-4 mt-5 d-flex flex-column">
            <div id="video-grid" class="video-grid w-100 flex-grow-1"
                :style="{ paddingRight: isChatOpen ? '350px' : '0', transition: 'padding 0.3s ease' }">
                <div id="local-video-wrapper" class="video-wrapper shadow-lg">
                    <div class="w-100 h-100 bg-secondary position-relative">
                        <div v-if="state === 'connecting'" class="position-absolute top-50 start-50 translate-middle z-3">
                            <span class="spinner-border text-light"></span>
                        </div>

                        <div id="local-video" class="w-100 h-100 position-absolute top-0 start-0"></div>
                    </div>

                    <div class="user-label">
                        <i class='bx bx-user-circle'></i> {{ currentUserName || 'Bạn' }}
                        <i v-if="!isMicOn" class='bx bx-microphone-off text-danger ms-2' title="Mic đang tắt"></i>
                        <i v-else class='bx bx-microphone text-success ms-2' title="Mic đang bật"></i>
                    </div>
                </div>
            </div>
        </main>

        <footer class="position-absolute bottom-0 w-100 mb-4 d-flex justify-content-center z-3">
            <div
                class="bg-dark bg-opacity-75 backdrop-blur px-4 py-3 rounded-pill shadow-lg border border-secondary d-flex gap-3 align-items-center">

                <button @click="toggleMic"
                    :class="['btn rounded-circle tool-btn shadow-sm', isMicOn ? 'btn-light' : 'btn-danger']"
                    :title="isMicOn ? 'Tắt Micro' : 'Bật Micro'">
                    <i :class="['fs-4', isMicOn ? 'bx bx-microphone' : 'bx bxs-microphone-off']"></i>
                </button>

                <button @click="toggleCamera"
                    :class="['btn rounded-circle tool-btn shadow-sm', isCameraOn ? 'btn-light' : 'btn-danger']"
                    :title="isCameraOn ? 'Tắt Camera' : 'Bật Camera'">
                    <i :class="['fs-4', isCameraOn ? 'bx bx-video' : 'bx bx-video-off']"></i>
                </button>

                <button @click="toggleScreenShare"
                    :class="['btn rounded-circle tool-btn shadow-sm', isSharingScreen ? 'btn-primary' : 'btn-light']"
                    :title="isSharingScreen ? 'Dừng chia sẻ màn hình' : 'Chia sẻ màn hình'">
                    <i :class="['fs-4', isSharingScreen ? 'bx bx-stop-circle' : 'bx bx-desktop']"></i>
                </button>

                <button @click="openSettings" class="btn btn-secondary rounded-circle tool-btn shadow-sm"
                    title="Cài đặt Âm thanh">
                    <i class='bx bx-slider-alt fs-4'></i>
                </button>

                <button @click="toggleChat"
                    :class="['btn rounded-circle tool-btn shadow-sm', isChatOpen ? 'btn-primary text-white' : 'btn-light']"
                    title="Mở Chat">
                    <i class='bx bx-message-rounded-dots fs-4'></i>
                </button>

                <button @click="toggleParticipants"
                    :class="['btn rounded-circle tool-btn shadow-sm', isParticipantsOpen ? 'btn-primary text-white' : 'btn-light']"
                    title="Danh sách người tham gia">
                    <i class='bx bx-group fs-4'></i>
                </button>

                <div class="vr bg-secondary mx-2"></div>

                <button @click="roiPhong"
                    class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2"
                    style="height: 50px;" title="Rời khỏi phòng học">
                    <i class='bx bx-phone-off fs-5'></i> Rời đi
                </button>
            </div>
        </footer>

        <div v-if="showSettings" class="modal-overlay d-flex justify-content-center align-items-center"
            style="z-index: 9999;">
            <div class="bg-dark border border-secondary rounded-4 p-4 shadow-lg text-white"
                style="width: 400px; max-width: 90%;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold"><i class='bx bx-cog text-primary me-2'></i> Cài đặt thiết bị</h5>
                    <button @click="showSettings = false" class="btn-close btn-close-white"></button>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold">CHỌN MICRO (ĐẦU VÀO)</label>
                    <select class="form-select bg-dark text-white border-secondary" v-model="selectedMic"
                        @change="switchDevice('audioinput', selectedMic)">
                        <option v-for="device in audioInputs" :key="device.deviceId" :value="device.deviceId">
                            {{ device.label || 'Micro mặc định' }}
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small fw-bold">CHỌN LOA (ĐẦU RA)</label>
                    <select class="form-select bg-dark text-white border-secondary" v-model="selectedSpeaker"
                        @change="switchDevice('audiooutput', selectedSpeaker)">
                        <option v-for="device in audioOutputs" :key="device.deviceId" :value="device.deviceId">
                            {{ device.label || 'Loa mặc định' }}
                        </option>
                    </select>
                </div>

                <button @click="showSettings = false" class="btn btn-primary w-100 rounded-pill fw-bold">Hoàn
                    tất</button>
            </div>
        </div>

        <!-- Chat Sidebar -->
        <aside v-if="isChatOpen" class="bg-dark border-start border-secondary d-flex flex-column z-3"
            style="width: 350px; position: absolute; right: 0; top: 0; bottom: 0; box-shadow: -5px 0 15px rgba(0,0,0,0.5);">
            <div
                class="p-3 border-bottom border-secondary d-flex justify-content-between align-items-center bg-dark text-white">
                <h6 class="mb-0 fw-bold"><i class='bx bx-message-rounded-dots me-2'></i> Trò chuyện trong phòng</h6>
                <button @click="toggleChat" class="btn-close btn-close-white"></button>
            </div>

            <div class="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-3" ref="chatBox">
                <div v-for="(msg, index) in chatMessages" :key="index"
                    :class="['d-flex flex-column', msg.isLocal ? 'align-items-end' : 'align-items-start']">
                    <span class="small text-secondary mb-1">{{ msg.sender }} <span style="font-size: 0.7rem;">{{
                        msg.timestamp }}</span></span>
                    <div :class="['px-3 py-2 rounded-4 text-white', msg.isLocal ? 'bg-primary' : 'bg-secondary']"
                        style="max-width: 85%; word-wrap: break-word;">
                        {{ msg.text }}
                    </div>
                </div>
            </div>

            <div class="p-3 border-top border-secondary">
                <form @submit.prevent="sendChatMessage" class="d-flex gap-2">
                    <input type="text" v-model="newMessage"
                        class="form-control bg-dark text-white border-secondary rounded-pill"
                        placeholder="Nhập tin nhắn..." autocomplete="off">
                    <button type="submit"
                        class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; flex-shrink: 0;" :disabled="!newMessage.trim() || sending || state !== 'connected'">
                        <i class='bx bx-send'></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Participants Sidebar -->
        <aside v-if="isParticipantsOpen" class="bg-dark border-start border-secondary d-flex flex-column z-3"
            style="width: 350px; position: absolute; right: 0; top: 0; bottom: 0; box-shadow: -5px 0 15px rgba(0,0,0,0.5);">
            <div
                class="p-3 border-bottom border-secondary d-flex justify-content-between align-items-center bg-dark text-white">
                <h6 class="mb-0 fw-bold text-white"><i class='bx bx-group me-2'></i> Danh sách người tham gia</h6>
                <button @click="toggleParticipants" class="btn-close btn-close-white"></button>
            </div>

            <div
                class="p-3 border-bottom border-secondary text-white small d-flex justify-content-between align-items-center">
                <span>Tổng số người tham gia</span>
                <span class="badge bg-primary rounded-pill">{{ participants.length }}</span>
            </div>

            <div class="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-2">
                <div v-for="participant in participants" :key="participant.sid"
                    class="participant-item d-flex align-items-center justify-content-between gap-3 p-3 rounded-4 border border-secondary bg-black bg-opacity-25">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="participant-avatar rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white fw-bold">
                            {{ getParticipantInitial(participant) }}
                        </div>
                        <div>
                            <div class="text-white fw-semibold">{{ participant.name }}</div>
                            <div class="small text-secondary">
                                <i v-if="participant.isLocal" class='bx bx-user-check me-1'></i>
                                <span v-if="participant.isLocal">Bạn</span>
                                <span v-else>Thành viên trong phòng</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i :class="participant.audioEnabled ? 'bx bx-microphone text-success' : 'bx bx-microphone-off text-danger'"
                            :title="participant.audioEnabled ? 'Mic đang bật' : 'Mic đang tắt'"></i>
                        <i :class="participant.videoEnabled ? 'bx bx-video text-success' : 'bx bx-video-off text-danger'"
                            :title="participant.videoEnabled ? 'Camera đang bật' : 'Camera đang tắt'"></i>
                    </div>
                </div>

                <div v-if="participants.length === 0" class="text-center text-secondary py-5">
                    <i class='bx bx-group fs-1 mb-2 d-block'></i>
                    Chưa có người tham gia nào.
                </div>
            </div>
        </aside>
    </div>
</template>

<script>
import { markRaw } from 'vue';
import FaceProof from './FaceProof.vue';
import product from '../../services/productService';
import http from '../../services/http';
import { accepted } from '../../services/productContract';
import { attachTrack, disposeRoom, sendRoomMessage } from '../../services/flowHelpers';

export default {
  name: 'PhongHopVideo',
  components: { FaceProof },
  data() {
    return {
      roomId: this.$route.params.id, sessionId: Number(this.$route.query.session || this.$route.params.id),
      tenPhongHoc: '', phongHopId: null, room: null, sdk: null,
      state: 'verification', error: '', mediaError: '', disposed: false, joined: false, leaving: false,
      cameraReady: false, isMicOn: false, isCameraOn: false, isSharingScreen: false,
      audioPlaybackBlocked: false,
      currentUserName: '', showSettings: false, audioInputs: [], audioOutputs: [], selectedMic: '', selectedSpeaker: '',
      isChatOpen: false, chatMessages: [], newMessage: '', sending: false,
      isParticipantsOpen: false, participants: [], attachments: [],
    };
  },
  computed: {
    connectionLabel() { return { verification: 'Chờ xác thực Face ID', connecting: 'Đang kết nối', connected: 'Đã kết nối phòng học', reconnecting: 'Đang kết nối lại', disconnected: 'Đã ngắt kết nối', failed: 'Kết nối thất bại' }[this.state]; }
  },
  beforeUnmount() { this.cleanup(); },
  methods: {
    async connect(proof) {
      if (this.state === 'connecting' || this.state === 'connected' || !proof?.verification_id) return;
      this.error = ''; this.state = 'connecting';
      try {
        const result = await product.roomToken(this.sessionId, proof.verification_id);
        if (this.disposed) return;
        const data = result.data;
        if (!data?.token || !data?.server_url) throw new Error('Máy chủ chưa cấp thông tin kết nối phòng học.');
        this.roomId = data.ma_phong; this.phongHopId = data.id_phong_hop;
        this.sdk = markRaw(await import('livekit-client'));
        if (this.disposed) return;
        const { Room, RoomEvent } = this.sdk;
        const room = markRaw(new Room({ adaptiveStream: true, dynacast: true }));
        this.room = room;
        room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => this.attachRemote(track, participant));
        room.on(RoomEvent.TrackUnsubscribed, track => track.detach().forEach(node => node.remove()));
        room.on(RoomEvent.ParticipantConnected, () => this.syncParticipants());
        room.on(RoomEvent.ParticipantDisconnected, participant => {
          document.querySelectorAll(`[data-participant="${CSS.escape(participant.sid)}"]`).forEach(node => node.remove());
          this.syncParticipants();
        });
        room.on(RoomEvent.TrackMuted, () => this.syncParticipants());
        room.on(RoomEvent.TrackUnmuted, () => this.syncParticipants());
        room.on(RoomEvent.Reconnecting, () => this.state = 'reconnecting');
        room.on(RoomEvent.Reconnected, () => this.state = 'connected');
        room.on(RoomEvent.AudioPlaybackStatusChanged, () => this.audioPlaybackBlocked = !room.canPlaybackAudio);
        room.on(RoomEvent.Disconnected, () => { this.state = 'disconnected'; this.cameraReady = false; this.isMicOn = false; this.isCameraOn = false; this.participants = []; });
        room.on(RoomEvent.DataReceived, (payload, participant, kind, topic) => {
          if (!participant || topic !== 'class-chat') return;
          try {
            const message = JSON.parse(new TextDecoder().decode(payload));
            if (typeof message.text !== 'string' || !message.text.trim() || message.text.length > 2000) return;
            this.chatMessages.push({ text: message.text, sender: participant.name || participant.identity, timestamp: new Date().toLocaleTimeString('vi-VN'), isLocal: false, status: 'received' });
            this.scrollChatToBottom();
          } catch { /* Discard non-chat data without granting any action. */ }
        });
        await room.connect(data.server_url, data.token);
        if (this.disposed) { await room.disconnect(); return; }
        await accepted(http.post('/chi-tiet-phong-hop/create', { id_buoi_hoc: this.sessionId, attendance_token: data.attendance_token }));
        this.joined = true;
        if (this.disposed) { await this.leaveAttendance(); await room.disconnect(); return; }
        this.state = 'connected'; this.currentUserName = room.localParticipant.name || room.localParticipant.identity;
        this.audioPlaybackBlocked = !room.canPlaybackAudio;
        this.syncParticipants();
        // Camera/mic errors do not claim the devices are ready.
        try { await room.localParticipant.setCameraEnabled(true); this.isCameraOn = true; this.attachLocal(); }
        catch (error) { this.mediaError = 'Không mở được camera. Cấp quyền camera rồi bật lại.'; }
        try { await room.localParticipant.setMicrophoneEnabled(true); this.isMicOn = true; }
        catch (error) { this.mediaError += ' Không mở được micro. Cấp quyền micro rồi bật lại.'; }
        this.syncParticipants();
      } catch (error) {
        this.error = error.message || 'Không thể kết nối phòng học. Kiểm tra mạng và cấu hình dịch vụ video.';
        this.state = 'failed';
        await disposeRoom(this.room, [], this.attachments); this.room = null;
      }
    },
    attachLocal() {
      const container = document.getElementById('local-video');
      if (!container || !this.room) return;
      container.replaceChildren();
      for (const pub of this.room.localParticipant.videoTrackPublications.values()) {
        if (!pub.track || pub.source !== this.sdk.Track.Source.Camera) continue;
        const node = attachTrack(pub.track, container); node.muted = true; node.style.cssText = 'width:100%;height:100%;object-fit:cover;'; this.attachments.push(node);
      }
      this.cameraReady = this.isCameraOn;
    },
    attachRemote(track, participant) {
      const container = document.getElementById(track.kind === 'audio' ? 'remote-audio' : 'video-grid');
      if (!container) return;
      if (track.kind === 'audio') {
        const node = attachTrack(track, container); node.dataset.participant = participant.sid; this.attachments.push(node);
      } else {
        const wrapper = document.createElement('div'); wrapper.className = 'video-wrapper shadow-lg'; wrapper.dataset.participant = participant.sid;
        const node = attachTrack(track, wrapper); node.style.cssText = 'width:100%;height:100%;object-fit:contain;';
        const label = document.createElement('div'); label.className = 'user-label'; label.textContent = participant.name || participant.identity;
        wrapper.appendChild(label); container.appendChild(wrapper); this.attachments.push(wrapper);
      }
      this.syncParticipants();
    },
    syncParticipants() {
      if (!this.room || this.room.state !== 'connected') { this.participants = []; return; }
      this.participants = [this.room.localParticipant, ...this.room.remoteParticipants.values()].map(participant => ({ sid: participant.sid, name: participant.name || participant.identity, isLocal: participant === this.room.localParticipant, audioEnabled: participant.isMicrophoneEnabled, videoEnabled: participant.isCameraEnabled }));
    },
    async toggleMic() {
      if (this.state !== 'connected') return;
      try { await this.room.localParticipant.setMicrophoneEnabled(!this.isMicOn); this.isMicOn = this.room.localParticipant.isMicrophoneEnabled; this.syncParticipants(); }
      catch { this.mediaError = 'Không thể bật/tắt micro. Kiểm tra quyền thiết bị.'; }
    },
    async startAudio() {
      try { await this.room.startAudio(); this.audioPlaybackBlocked = !this.room.canPlaybackAudio; }
      catch { this.mediaError = 'Trình duyệt chưa cho phát âm thanh. Hãy bật quyền âm thanh của trang.'; }
    },
    async toggleCamera() {
      if (this.state !== 'connected') return;
      try { await this.room.localParticipant.setCameraEnabled(!this.isCameraOn); this.isCameraOn = this.room.localParticipant.isCameraEnabled; this.attachLocal(); this.syncParticipants(); }
      catch { this.mediaError = 'Không thể bật/tắt camera. Kiểm tra quyền thiết bị.'; }
    },
    async toggleScreenShare() {
      if (this.state !== 'connected') return;
      try { await this.room.localParticipant.setScreenShareEnabled(!this.isSharingScreen); this.isSharingScreen = this.room.localParticipant.isScreenShareEnabled; }
      catch { this.mediaError = 'Không thể chia sẻ màn hình hoặc bạn đã hủy lựa chọn.'; }
    },
    async openSettings() {
      this.showSettings = true;
      try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        this.audioInputs = devices.filter(device => device.kind === 'audioinput'); this.audioOutputs = devices.filter(device => device.kind === 'audiooutput');
      } catch { this.mediaError = 'Không thể đọc danh sách thiết bị.'; }
    },
    async switchDevice(kind, id) {
      if (this.state !== 'connected') return;
      try { await this.room.switchActiveDevice(kind, id); }
      catch { this.mediaError = 'Trình duyệt hoặc thiết bị không hỗ trợ lựa chọn này.'; }
    },
    toggleChat() { this.isChatOpen = !this.isChatOpen; this.isParticipantsOpen = false; this.scrollChatToBottom(); },
    toggleParticipants() { this.isParticipantsOpen = !this.isParticipantsOpen; this.isChatOpen = false; },
    getParticipantInitial(participant) { return (participant.name || '?').slice(0,1).toUpperCase(); },
    scrollChatToBottom() { this.$nextTick(() => { if (this.$refs.chatBox) this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight; }); },
    async sendChatMessage() {
      if (this.sending) return;
      this.sending = true; this.error = '';
      try {
        const sent = await sendRoomMessage(this.room, this.newMessage);
        this.chatMessages.push({ ...sent, sender: this.currentUserName, timestamp: new Date().toLocaleTimeString('vi-VN') }); this.newMessage = ''; this.scrollChatToBottom();
      } catch (error) { this.error = error.message || 'Tin nhắn chưa được gửi. Vui lòng thử lại.'; }
      finally { this.sending = false; }
    },
    async cleanup() {
      this.disposed = true;
      await disposeRoom(this.room, [], this.attachments); this.room = null; this.attachments = [];
      await this.leaveAttendance();
    },
    async leaveAttendance() {
      if (!this.joined || this.leaving) return;
      this.leaving = true;
      try { await accepted(http.post('/phong-hop/roi-phong', { id_buoi_hoc: this.sessionId })); this.joined = false; }
      catch (error) { this.error = error.message || 'Đã ngắt video nhưng chưa ghi nhận rời phòng. Vui lòng thử lại.'; }
      finally { this.leaving = false; }
    },
    async roiPhong() { await this.cleanup(); if (this.joined) return; this.$router.push(localStorage.getItem('role') === 'giao_vien' ? '/giao-vien/lich-day' : '/hoc-vien/lich-hoc'); }
  }
};

</script>

<style scoped>
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
    align-content: center;
    justify-content: center;
    position: relative;
}

:deep(.video-wrapper),
:deep(.video-container) {
    position: relative;
    width: 100%;
    height: 100%;
    max-height: 80vh;
    border-radius: 16px;
    overflow: hidden;
    background-color: #1e293b;
    border: 3px solid transparent;
    aspect-ratio: 16 / 9;
    transition: all 0.3s ease;
}

:deep(.speaking-border) {
    border-color: #22c55e !important;
    box-shadow: 0 0 20px rgba(34, 197, 94, 0.6) !important;
    transform: scale(1.02);
}

:deep(.user-label) {
    position: absolute;
    bottom: 12px;
    left: 12px;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(4px);
    z-index: 10;
}

.backdrop-blur {
    backdrop-filter: blur(10px);
}

.tool-btn {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.tool-btn:hover {
    transform: scale(1.1);
}

.participant-item {
    min-height: 74px;
}

.participant-avatar {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
}

.text-shadow {
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
}
</style>

<style>
.video-grid.has-screen-share {
    position: absolute !important;
    top: 0;
    left: 0;
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    flex-wrap: wrap;
    align-content: flex-end;
    justify-content: flex-end;
    gap: 16px;
    padding-bottom: 90px;
    padding-right: 24px;
    padding-left: 24px;
    z-index: 1;
}

.video-grid.has-screen-share .screen-share-element {
    position: absolute !important;
    top: 0;
    left: 0;
    width: 100% !important;
    height: 100% !important;
    max-height: none !important;
    z-index: 1;
    border-radius: 8px;
    background-color: #000 !important;
}

.video-grid.has-screen-share .screen-share-element video {
    background-color: #000 !important;
}

.video-grid.has-screen-share .video-wrapper,
.video-grid.has-screen-share .video-container:not(.screen-share-element) {
    position: relative !important;
    z-index: 2 !important;
    width: 240px !important;
    height: 135px !important;
    max-height: none !important;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.8);
    border: 2px solid rgba(255, 255, 255, 0.15);
    transition: transform 0.3s ease;
}

.video-grid.has-screen-share .video-wrapper:hover,
.video-grid.has-screen-share .video-container:not(.screen-share-element):hover {
    transform: scale(1.05);
    z-index: 3 !important;
}

.video-grid.has-screen-share .is-sharing-person {
    border: 2px solid #3b82f6 !important;
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5) !important;
}

.video-grid.has-screen-share .user-label {
    font-size: 0.75rem !important;
    padding: 4px 10px !important;
    bottom: 8px !important;
    left: 8px !important;
}

.video-grid.has-screen-share .badge {
    font-size: 0.75rem !important;
}
</style>
