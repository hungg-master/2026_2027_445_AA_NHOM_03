<template><ClientProfile /></template>
<script>
import ClientProfile from './ClientProfile.vue';
export default { name: 'FaceIdVerification', components: { ClientProfile } };
</script>
<style scoped>
/* Toàn màn hình & Căn chỉnh chung */
.faceid-page {
  min-height: 100vh;
  background-color: #f8fafc;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  padding: 36px 20px 60px;
  display: flex;
  justify-content: center;
}

.faceid-container {
  width: 100%;
  max-width: 1200px;
}

/* ================= 1. HEADER ================= */
.page-header {
  margin-bottom: 28px;
}

.badge-ai-wrap {
  margin-bottom: 12px;
}

.badge-ai {
  display: inline-flex;
  align-items: center;
  font-size: 11.5px;
  font-weight: 700;
  color: #0060d2;
  background-color: #eff6ff;
  border: 1px solid #dbeafe;
  padding: 4px 12px;
  border-radius: 50px;
  letter-spacing: 0.5px;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.5px;
  margin-bottom: 8px;
}

.page-subtitle {
  font-size: 14.5px;
  color: #64748b;
  margin-bottom: 0;
  max-width: 780px;
  line-height: 1.5;
}

/* ================= 2. LAYOUT 2 CỘT ================= */
.faceid-layout {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}

/* --- CỘT TRÁI: CAMERA VIEW --- */
.camera-column {
  flex: 1 1 58%;
}

.camera-viewport {
  position: relative;
  width: 100%;
  height: 440px;
  border-radius: 20px;
  overflow: hidden;
  background-color: #0f172a;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.webcam-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Overlay trên camera */
.camera-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 18px 20px;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 30%, rgba(0, 0, 0, 0.3) 70%, rgba(0, 0, 0, 0.6) 100%);
}

.overlay-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 10;
}

.cam-status-pill,
.cam-resolution-pill {
  background-color: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(8px);
  color: #f8fafc;
  font-size: 12px;
  font-weight: 500;
  padding: 6px 14px;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.green-dot {
  width: 8px;
  height: 8px;
  background-color: #22c55e;
  border-radius: 50%;
  display: inline-block;
}

.status-pulse-dot {
  width: 8px;
  height: 8px;
  background-color: #22c55e;
  border-radius: 50%;
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.35);
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* Khung ngắm Face ID Reticle */
.face-target-reticle {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 250px;
  height: 290px;
  pointer-events: none;
}

/* 4 Góc nhắm mục tiêu xanh dương */
.reticle-corner {
  position: absolute;
  width: 32px;
  height: 32px;
  border-color: #0060d2;
  border-style: solid;
}

.corner-tl {
  top: 0;
  left: 0;
  border-width: 4px 0 0 4px;
  border-top-left-radius: 8px;
}

.corner-tr {
  top: 0;
  right: 0;
  border-width: 4px 4px 0 0;
  border-top-right-radius: 8px;
}

.corner-bl {
  bottom: 0;
  left: 0;
  border-width: 0 0 4px 4px;
  border-bottom-left-radius: 8px;
}

.corner-br {
  bottom: 0;
  right: 0;
  border-width: 0 4px 4px 0;
  border-bottom-right-radius: 8px;
}

/* Khung đứt nét */
.face-oval-dashed {
  position: absolute;
  inset: 12px;
  border: 2px dashed rgba(255, 255, 255, 0.4);
  border-radius: 24px;
}

/* Tia quét Laser */
.scanning-laser-beam {
  position: absolute;
  left: 10px;
  right: 10px;
  height: 2px;
  background: #38bdf8;
  box-shadow: 0 0 12px 3px rgba(56, 189, 248, 0.85);
  opacity: 0.9;
}

.scanning-laser-beam.animating {
  animation: scanLaser 2.2s ease-in-out infinite;
}

@keyframes scanLaser {
  0% { top: 15%; opacity: 0.7; }
  50% { top: 85%; opacity: 1; }
  100% { top: 15%; opacity: 0.7; }
}

/* Huy hiệu Khớp: kết quả Face ID (Hợp lệ) */
.match-result-pill {
  position: absolute;
  bottom: 64px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #ffffff;
  color: #0f172a;
  font-size: 13px;
  padding: 6px 18px;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
  z-index: 10;
}

/* Profile học viên góc dưới */
.student-profile-badge {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  background-color: rgba(15, 23, 42, 0.75);
  backdrop-filter: blur(10px);
  padding: 8px 16px;
  border-radius: 50px;
  max-width: fit-content;
  z-index: 10;
}

.student-avatar-circle {
  width: 32px;
  height: 32px;
  background-color: #0060d2;
  color: #ffffff;
  font-weight: 700;
  font-size: 12.5px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.student-name {
  color: #ffffff;
  font-weight: 700;
  font-size: 13.5px;
  line-height: 1.2;
}

.student-id {
  color: #94a3b8;
  font-size: 11px;
}

/* Thanh điều khiển Camera dưới video */
.camera-controls-bar {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 12px 20px;
  margin-top: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.device-toggles {
  display: flex;
  align-items: center;
  gap: 14px;
}

.control-circle-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.control-circle-btn.btn-active {
  color: #0f172a;
}

.control-circle-btn:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
}

/* Thước đo sóng âm (Audio Wave) */
.audio-wave-meter {
  display: flex;
  align-items: center;
  gap: 3px;
  height: 24px;
  padding: 0 8px;
}

.wave-bar {
  width: 3px;
  border-radius: 4px;
  background-color: #22c55e;
}

.bar-1 { height: 10px; animation: wave 1.2s ease-in-out infinite; }
.bar-2 { height: 16px; animation: wave 1.2s ease-in-out 0.2s infinite; }
.bar-3 { height: 22px; animation: wave 1.2s ease-in-out 0.4s infinite; }
.bar-4 { height: 14px; animation: wave 1.2s ease-in-out 0.1s infinite; }
.bar-5 { height: 8px;  animation: wave 1.2s ease-in-out 0.3s infinite; }

@keyframes wave {
  0%, 100% { transform: scaleY(0.5); }
  50% { transform: scaleY(1); }
}

.device-select-btn {
  font-size: 13px;
  color: #334155;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 8px 16px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  cursor: pointer;
  font-weight: 500;
}

/* --- CỘT PHẢI: INFO & CHECKLIST --- */
.info-column {
  flex: 1 1 42%;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* 1. Thẻ thông tin lớp */
.class-info-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 22px 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.class-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.class-type-badge {
  background-color: #ede9fe;
  color: #7c3aed;
  font-size: 12px;
  font-weight: 600;
  padding: 3px 12px;
  border-radius: 50px;
}

.room-status-badge {
  color: #16a34a;
  font-size: 12.5px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
}

.class-title {
  font-size: 21px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 4px;
}

.class-subject {
  font-size: 13.5px;
  color: #64748b;
  margin-bottom: 16px;
}

.class-meta-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.meta-row {
  display: flex;
  align-items: center;
  font-size: 13.5px;
}

.meta-icon {
  width: 20px;
  color: #64748b;
  font-size: 14px;
}

.meta-label {
  color: #64748b;
  width: 95px;
}

.meta-val {
  color: #1e293b;
}

.room-code {
  color: #0060d2;
  font-weight: 700;
  letter-spacing: 0.5px;
}

/* 2. Thẻ kiểm tra điều kiện */
.conditions-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 22px 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.conditions-title {
  font-size: 13px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
}

.checklist-items {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 16px;
}

.checklist-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 13.5px;
}

.checklist-item.highlight-green {
  background-color: #dcfce7;
}

.item-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.check-icon {
  font-size: 16px;
}

.conditions-tip {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.4;
  padding-top: 6px;
}

.tip-icon {
  margin-top: 2px;
  font-size: 13px;
  color: #94a3b8;
}

/* 3. Nút hành động */
.action-buttons-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.btn-enter-room {
  background-color: #0060d2;
  color: #ffffff;
  border: none;
  height: 48px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  transition: all 0.2s ease;
  box-shadow: 0 4px 14px rgba(0, 96, 210, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-enter-room:hover {
  background-color: #004fb0;
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(0, 96, 210, 0.35);
}

.secondary-actions-row {
  display: flex;
  gap: 12px;
}

.btn-secondary-action {
  flex: 1;
  height: 42px;
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: #334155;
  font-size: 13px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-secondary-action:hover {
  background-color: #e2e8f0;
  color: #0f172a;
}

.privacy-disclaimer {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11.5px;
  color: #64748b;
  line-height: 1.4;
  margin-top: 4px;
}

.privacy-icon {
  font-size: 12px;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 991px) {
  .faceid-layout {
    flex-direction: column;
  }
  .camera-column,
  .info-column {
    flex: 1 1 100%;
    width: 100%;
  }
}
</style>
