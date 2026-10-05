export async function captureSingleFace({ detect, signal, frames = 4, timeout = 15000, now = Date.now, pause = () => new Promise(resolve => setTimeout(resolve, 180)), onProgress = () => {} }) {
  const start = now();
  let count = 0;
  while (now() - start < timeout) {
    if (signal?.aborted) throw new Error('Đã hủy quét Face ID.');
    const faces = await bounded(detect(), Math.max(1, timeout - (now() - start)), signal);
    if (signal?.aborted) throw new Error('Đã hủy quét Face ID.');
    if (faces.length > 1) throw new Error('Chỉ một khuôn mặt được phép xuất hiện trong khung hình.');
    if (faces.length === 1) {
      const descriptor = Array.from(faces[0].descriptor || []);
      if (descriptor.length !== 128 || !descriptor.every(Number.isFinite)) throw new Error('Dữ liệu Face ID không hợp lệ.');
      count++;
      onProgress(`Giữ khuôn mặt trong khung hình (${count}/${frames})…`);
      if (count >= frames) return descriptor;
    } else {
      count = 0;
      onProgress('Chưa thấy khuôn mặt. Vui lòng nhìn thẳng vào camera.');
    }
    await pause();
  }
  throw new Error('Hết thời gian nhận diện khuôn mặt. Hãy kiểm tra ánh sáng và quét lại.');
}

export function bounded(request, timeout = 15000, signal) {
  return new Promise((resolve, reject) => {
    const cancel = () => { clearTimeout(timer); reject(new Error('Đã hủy quét Face ID.')); };
    const timer = setTimeout(() => { signal?.removeEventListener?.('abort', cancel); reject(new Error('Hết thời gian xử lý Face ID. Vui lòng thử lại.')); }, timeout);
    if (signal?.aborted) { cancel(); return; }
    signal?.addEventListener?.('abort', cancel, { once: true });
    Promise.resolve(request).then(resolve, reject).finally(() => { clearTimeout(timer); signal?.removeEventListener?.('abort', cancel); });
  });
}

export function calendarSession(value) {
  const lesson = { ...(value.lop_hoc || value) };
  lesson.thoi_gian_bat_dau = value.thoi_gian_bat_dau || lesson.thoi_gian_bat_dau;
  lesson.thoi_gian_ket_thuc = value.thoi_gian_ket_thuc || lesson.thoi_gian_ket_thuc;
  return { ...lesson, ...value, lop_hoc: lesson };
}

export function availabilitySlots(intervals, timeKeys) {
  const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  const slots = new Set();
  for (const row of intervals) {
    const day = days[row.ngay_trong_tuan];
    if (!day) continue;
    for (const time of timeKeys) {
      const end = `${String(Number(time.slice(0,2)) + 1).padStart(2,'0')}:${time.slice(3,5)}`;
      if (time >= row.thoi_gian_bat_dau.slice(0,5) && end <= row.thoi_gian_ket_thuc.slice(0,5)) slots.add(`${day}_${time}`);
    }
  }
  return [...slots];
}

export async function sendRoomMessage(room, text) {
  if (!room || room.state !== 'connected') throw new Error('Chưa kết nối phòng học; tin nhắn chưa được gửi.');
  const clean = text.trim();
  if (!clean || clean.length > 2000) throw new Error('Tin nhắn phải có từ 1 đến 2000 ký tự.');
  await room.localParticipant.publishData(new TextEncoder().encode(JSON.stringify({ text: clean })), { reliable: true, topic: 'class-chat' });
  return { text: clean, status: 'sent', isLocal: true };
}
export function attachTrack(track, container) {
  const element = track.attach();
  container.appendChild(element);
  return element;
}
export async function disposeRoom(room, streams = [], attachments = []) {
  for (const stream of streams) stream?.getTracks().forEach(track => track.stop());
  for (const node of attachments) node?.remove();
  if (room) await room.disconnect();
}

export function dateParts(iso) {
  if (!iso) return null;
  const value = new Date(/[Zz]|[+-]\d\d:\d\d$/.test(iso) ? iso : `${String(iso).replace(' ', 'T')}+07:00`);
  if (Number.isNaN(value.getTime())) return null;
  const parts = Object.fromEntries(new Intl.DateTimeFormat('en-GB', { timeZone: 'Asia/Ho_Chi_Minh', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hourCycle: 'h23' }).formatToParts(value).map(p => [p.type, p.value]));
  return { year: +parts.year, month: +parts.month, day: +parts.day, hour: +parts.hour, minute: +parts.minute, dateKey: `${parts.year}-${parts.month}-${parts.day}`, timeStr: `${parts.hour}:${parts.minute}` };
}
