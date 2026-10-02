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

        <main class="flex-grow-1 position-relative p-2 p-md-4 mt-5 d-flex flex-column">
            <div id="video-grid" class="video-grid w-100 flex-grow-1"
                :style="{ paddingRight: isChatOpen ? '350px' : '0', transition: 'padding 0.3s ease' }">
                <div id="local-video-wrapper" class="video-wrapper shadow-lg">
                    <div class="w-100 h-100 bg-secondary position-relative">
                        <div v-if="!cameraReady" class="position-absolute top-50 start-50 translate-middle z-3">
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
                        style="width: 40px; height: 40px; flex-shrink: 0;" :disabled="!newMessage.trim()">
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
import axios from 'axios';
import { Room, RoomEvent, Track } from 'livekit-client';
import { markRaw } from 'vue';

export default {
    name: 'PhongHopVideo',
    data() {
        return {
            roomId: this.$route.params.id || this.$route.params.maPhong || 'PHONG-01',
            tenPhongHoc: '',
            phongHopId: null,
            room: null,
            cameraReady: false,
            isMicOn: true,
            isCameraOn: true,
            isSharingScreen: false,
            localStream: null,
            currentUserName: '',

            // State cho Cài đặt thiết bị
            showSettings: false,
            audioInputs: [],
            audioOutputs: [],
            selectedMic: '',
            selectedSpeaker: '',

            // State cho Chat
            isChatOpen: false,
            chatMessages: [],
            newMessage: '',

            // State cho danh sách người tham gia
            isParticipantsOpen: false,
            participants: []
        };
    },
    async mounted() {
        const currentUser = JSON.parse(
            localStorage.getItem('thong_tin_user') ||
            localStorage.getItem('user') ||
            localStorage.getItem('edulink_user') ||
            '{}'
        );
        this.currentUserName = currentUser?.ho_ten || currentUser?.name || currentUser?.ho_va_ten || 'Học viên';

        // Lấy thông tin phòng họp từ backend API
        try {
            const res = await axios.get(`/api/phong-hop/ma-phong?ma_phong=${encodeURIComponent(this.roomId)}`);
            if (res.data?.status && res.data?.data) {
                this.tenPhongHoc = res.data.data.ten_phong;
                this.phongHopId = res.data.data.id;

                // Ghi nhận điểm danh vào chi tiết phòng họp
                if (currentUser?.id) {
                    await axios.post('/api/chi-tiet-phong-hop/create', {
                        id_phong_hop: this.phongHopId,
                        id_nguoi_dung: currentUser.id,
                        xac_thuc_khuon_mat: 1,
                        is_active: 1
                    });
                }
            }
        } catch (e) {
            console.warn("Không thể tải chi tiết phòng học:", e);
        }

        // Khởi tạo danh sách người tham gia mặc định
        this.participants = [
            {
                sid: 'local-user',
                name: this.currentUserName,
                isLocal: true,
                audioEnabled: true,
                videoEnabled: true
            }
        ];

        const token = sessionStorage.getItem('livekit_token');
        const livekitUrl = import.meta.env.VITE_LIVEKIT_URL;

        // Nếu có cấu hình LiveKit đầy đủ thì kết nối LiveKit
        if (token && livekitUrl) {
            await this.initLiveKit(livekitUrl, token, currentUser);
        } else {
            // Chế độ phòng học trực tiếp qua WebRTC MediaStream cục bộ
            await this.initLocalCameraStream();
        }
    },
    methods: {
        async initLocalCameraStream() {
            try {
                this.localStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 1280 }, height: { ideal: 720 }, facingMode: 'user' },
                    audio: true
                });
                this.cameraReady = true;

                const videoElement = document.createElement('video');
                videoElement.srcObject = this.localStream;
                videoElement.autoplay = true;
                videoElement.playsInline = true;
                videoElement.muted = true;
                videoElement.style.width = '100%';
                videoElement.style.height = '100%';
                videoElement.style.objectFit = 'cover';
                videoElement.style.transform = 'scaleX(-1)';

                const localContainer = document.getElementById('local-video');
                if (localContainer) {
                    localContainer.innerHTML = '';
                    localContainer.appendChild(videoElement);
                }
            } catch (err) {
                console.error("Lỗi mở camera cục bộ:", err);
                this.cameraReady = true;
            }
        },

        async initLiveKit(livekitUrl, token, user) {
            try {
                this.room = markRaw(new Room({
                    adaptiveStream: true,
                    dynacast: true,
                }));

                // Lắng nghe người khác vào
                this.room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
                    if (track.kind === 'video') {
                        if (track.source === Track.Source.ScreenShare) {
                            this.attachRemoteVideo(track, participant, true);
                        } else {
                            this.attachRemoteVideo(track, participant, false);
                        }
                    } else if (track.kind === 'audio') {
                        track.attach();
                    }
                });

                this.room.on(RoomEvent.ParticipantConnected, () => {
                    this.syncParticipants();
                });

                this.room.on(RoomEvent.ParticipantDisconnected, () => {
                    this.syncParticipants();
                });

                this.room.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
                    track.detach();
                    const isScreenShare = track.source === Track.Source.ScreenShare;
                    const videoEl = document.getElementById(isScreenShare ? `screen-${participant.sid}` : `video-${participant.sid}`);
                    if (videoEl) videoEl.remove();

                    if (isScreenShare) {
                        const videoGrid = document.getElementById('video-grid');
                        if (videoGrid && !videoGrid.querySelector('.screen-share-element')) {
                            videoGrid.classList.remove('has-screen-share');
                        }
                        const ownerVideo = document.getElementById(`video-${participant.sid}`);
                        if (ownerVideo) ownerVideo.classList.remove('is-sharing-person');
                    }
                });

                // Lắng nghe tin nhắn chat từ DataChannel
                this.room.on(RoomEvent.DataReceived, (payload, participant, kind, topic) => {
                    const decoder = new TextDecoder();
                    const strData = decoder.decode(payload);
                    try {
                        const msgData = JSON.parse(strData);
                        this.chatMessages.push({
                            ...msgData,
                            isLocal: false
                        });
                        this.scrollToBottom();
                    } catch (e) {
                        console.error("Lỗi parse tin nhắn:", e);
                    }
                });

                // HIỆU ỨNG PHÁT SÁNG KHI ĐANG NÓI
                this.room.on(RoomEvent.ActiveSpeakersChanged, (speakers) => {
                    document.querySelectorAll('.video-wrapper, .video-container').forEach(el => el.classList.remove('speaking-border'));
                    speakers.forEach(speaker => {
                        const elId = speaker.isLocal ? 'local-video-wrapper' : `video-${speaker.sid}`;
                        const el = document.getElementById(elId);
                        if (el) el.classList.add('speaking-border');
                    });
                });

                await this.room.connect(livekitUrl, token);
                this.syncParticipants();

                // Lưu lịch sử tham gia phòng nếu có API
                const id_phong_that = sessionStorage.getItem('id_phong_hop');
                const apiUrl = import.meta.env.VITE_API_URL;
                if (user?.id && id_phong_that && apiUrl) {
                    const data = {
                        id_nguoi_dung: user.id,
                        id_phong_hop: id_phong_that,
                        xac_thuc_khuon_mat: 1,
                        is_vi_pham: 0,
                        is_nguoi_dung: 1,
                        is_active: 1,
                        trang_thai: 1
                    };
                    axios.post(`${apiUrl}/chi-tiet-phong-hop/create`, data).catch(() => {});
                }

                await this.room.localParticipant.enableCameraAndMicrophone();
                this.cameraReady = true;

                const localVideoTrack = this.room.localParticipant.getTrackPublication(Track.Source.Camera);
                if (localVideoTrack && localVideoTrack.videoTrack) {
                    const videoElement = localVideoTrack.videoTrack.attach();
                    videoElement.style.width = '100%';
                    videoElement.style.height = '100%';
                    videoElement.style.objectFit = 'cover';
                    videoElement.style.transform = 'scaleX(-1)';

                    const localContainer = document.getElementById('local-video');
                    if (localContainer) {
                        localContainer.innerHTML = '';
                        localContainer.appendChild(videoElement);
                    }
                }
            } catch (error) {
                console.error("Lỗi kết nối máy chủ LiveKit, chuyển sang chế độ camera cục bộ:", error);
                await this.initLocalCameraStream();
            }
        },

        syncParticipants() {
            if (!this.room) return;

            const localParticipant = this.room.localParticipant;
            const remoteParticipants = Array.from(this.room.remoteParticipants.values());

            this.participants = [
                {
                    sid: localParticipant.sid,
                    name: localParticipant.identity || this.currentUserName || 'Bạn',
                    isLocal: true,
                    audioEnabled: localParticipant.isMicrophoneEnabled,
                    videoEnabled: localParticipant.isCameraEnabled,
                },
                ...remoteParticipants.map(participant => ({
                    sid: participant.sid,
                    name: participant.identity || 'Khách',
                    isLocal: false,
                    audioEnabled: participant.isMicrophoneEnabled,
                    videoEnabled: participant.isCameraEnabled,
                }))
            ];
        },
        getParticipantInitial(participant) {
            const name = (participant.name || '').trim();
            return name ? name.charAt(0).toUpperCase() : '?';
        },
        attachRemoteVideo(track, participant, isScreenShare = false) {
            const videoGrid = document.getElementById('video-grid');
            if (!videoGrid) return;

            const wrapper = document.createElement('div');
            wrapper.id = isScreenShare ? `screen-${participant.sid}` : `video-${participant.sid}`;
            wrapper.className = 'video-container position-relative rounded-4 overflow-hidden shadow bg-secondary';

            if (isScreenShare) {
                wrapper.classList.add('screen-share-element');
                videoGrid.classList.add('has-screen-share');
                const ownerVideo = document.getElementById(`video-${participant.sid}`);
                if (ownerVideo) ownerVideo.classList.add('is-sharing-person');
            } else {
                if (videoGrid.classList.contains('has-screen-share')) {
                    const screenElement = document.getElementById(`screen-${participant.sid}`);
                    if (screenElement) wrapper.classList.add('is-sharing-person');
                }
            }

            const labelWrapper = document.createElement('div');
            labelWrapper.className = 'position-absolute bottom-0 start-0 p-2 z-3 d-flex align-items-center gap-2';

            const labelText = isScreenShare ? `${participant.identity} (Màn hình)` : participant.identity;
            labelWrapper.innerHTML = `<span class="badge bg-dark px-3 py-2 rounded-pill shadow border border-secondary">${labelText}</span>`;

            const videoElement = track.attach();
            videoElement.style.width = '100%';
            videoElement.style.height = '100%';
            videoElement.style.objectFit = isScreenShare ? 'contain' : 'cover';
            if (!isScreenShare) videoElement.style.transform = 'scaleX(-1)';

            wrapper.appendChild(videoElement);
            wrapper.appendChild(labelWrapper);
            videoGrid.appendChild(wrapper);
        },
        async toggleMic() {
            this.isMicOn = !this.isMicOn;
            if (this.room?.localParticipant) {
                await this.room.localParticipant.setMicrophoneEnabled(this.isMicOn);
                this.syncParticipants();
            } else if (this.localStream) {
                this.localStream.getAudioTracks().forEach(track => {
                    track.enabled = this.isMicOn;
                });
            }
        },
        async toggleCamera() {
            this.isCameraOn = !this.isCameraOn;
            if (this.room?.localParticipant) {
                await this.room.localParticipant.setCameraEnabled(this.isCameraOn);
                this.syncParticipants();
            } else if (this.localStream) {
                this.localStream.getVideoTracks().forEach(track => {
                    track.enabled = this.isCameraOn;
                });
            }
        },
        async toggleScreenShare() {
            try {
                this.isSharingScreen = !this.isSharingScreen;
                if (this.room?.localParticipant) {
                    await this.room.localParticipant.setScreenShareEnabled(this.isSharingScreen);
                } else if (navigator.mediaDevices.getDisplayMedia) {
                    if (this.isSharingScreen) {
                        const displayStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                        const videoEl = document.querySelector('#local-video video');
                        if (videoEl) videoEl.srcObject = displayStream;
                        displayStream.getVideoTracks()[0].onended = () => {
                            this.isSharingScreen = false;
                            if (videoEl && this.localStream) videoEl.srcObject = this.localStream;
                        };
                    } else {
                        const videoEl = document.querySelector('#local-video video');
                        if (videoEl && this.localStream) videoEl.srcObject = this.localStream;
                    }
                }
            } catch (error) {
                console.error("Lỗi chia sẻ màn hình:", error);
                this.isSharingScreen = false;
            }
        },
        toggleChat() {
            if (this.isParticipantsOpen) {
                this.isParticipantsOpen = false;
            }
            this.isChatOpen = !this.isChatOpen;
            if (this.isChatOpen) {
                this.scrollToBottom();
            }
        },
        toggleParticipants() {
            if (this.isChatOpen) {
                this.isChatOpen = false;
            }
            this.isParticipantsOpen = !this.isParticipantsOpen;
            if (this.isParticipantsOpen) {
                this.syncParticipants();
            }
        },
        async sendChatMessage() {
            if (!this.newMessage.trim()) return;
            const msgData = {
                text: this.newMessage.trim(),
                sender: this.currentUserName,
                timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                isLocal: true
            };

            this.chatMessages.push(msgData);

            if (this.room?.localParticipant) {
                try {
                    const strData = JSON.stringify(msgData);
                    const encoder = new TextEncoder();
                    await this.room.localParticipant.publishData(encoder.encode(strData), { reliable: true });
                } catch (err) {
                    console.error("Lỗi gửi tin nhắn:", err);
                }
            }

            this.newMessage = '';
            this.scrollToBottom();
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const chatBox = this.$refs.chatBox;
                if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
            });
        },
        async openSettings() {
            this.showSettings = true;
            try {
                await navigator.mediaDevices.getUserMedia({ audio: true });
                const devices = await navigator.mediaDevices.enumerateDevices();

                this.audioInputs = devices.filter(d => d.kind === 'audioinput');
                this.audioOutputs = devices.filter(d => d.kind === 'audiooutput');

                this.selectedMic = this.room?.getActiveDevice('audioinput') || this.audioInputs[0]?.deviceId || '';
                this.selectedSpeaker = this.room?.getActiveDevice('audiooutput') || this.audioOutputs[0]?.deviceId || '';
            } catch (err) {
                console.error("Không thể lấy danh sách thiết bị:", err);
            }
        },
        async switchDevice(kind, deviceId) {
            if (this.room) {
                await this.room.switchActiveDevice(kind, deviceId);
            }
        },
        async roiPhong() {
            // Ngắt kết nối phòng và dừng camera
            if (this.room) {
                this.room.disconnect();
            }
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => track.stop());
                this.localStream = null;
            }

            const currentUser = JSON.parse(
                localStorage.getItem('thong_tin_user') ||
                localStorage.getItem('user') ||
                localStorage.getItem('edulink_user') ||
                '{}'
            );
            if (currentUser?.id && this.phongHopId) {
                try {
                    await axios.post('/api/phong-hop/roi-phong', {
                        id_nguoi_dung: currentUser.id,
                        id_phong_hop: this.phongHopId
                    });
                } catch (e) {}
            }

            sessionStorage.removeItem('livekit_token');
            sessionStorage.removeItem('id_phong_hop');

            // Quay trở lại trang lịch học
            this.$router.push('/hoc-vien/lich-hoc');
        }
    },
    beforeUnmount() {
        if (this.room) {
            this.room.disconnect();
        }
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
            this.localStream = null;
        }
    }
}
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

.video-wrapper,
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

.user-label {
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
