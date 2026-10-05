import { captureSingleFace, bounded } from './flowHelpers';
let models;
export async function captureFace(video, { signal, onProgress = () => {}, onStream = () => {} } = {}) {
  let stream;
  let active = true;
  try {
    onProgress('Đang tải mô hình Face ID…');
    if (!models) models = import('face-api.js').then(async api => {
      await Promise.all([api.nets.tinyFaceDetector.loadFromUri('/model'), api.nets.faceLandmark68Net.loadFromUri('/model'), api.nets.faceRecognitionNet.loadFromUri('/model')]);
      return api;
    }).catch(error => { models = null; throw error; });
    const api = await bounded(models, 20000, signal);
    if (signal?.aborted) throw new Error('Đã hủy quét Face ID.');
    onProgress('Đang mở camera…');
    stream = await bounded(navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 320, height: 320 }, audio: false }).then(value => {
      if (!active || signal?.aborted) value.getTracks().forEach(track => track.stop());
      return value;
    }), 15000, signal);
    onStream(stream);
    if (signal?.aborted) throw new Error('Đã hủy quét Face ID.');
    video.srcObject = stream;
    await bounded(video.play(), 10000, signal);
    return await captureSingleFace({ signal, onProgress, detect: () => api.detectAllFaces(video, new api.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })).withFaceLandmarks().withFaceDescriptors() });
  } finally {
    active = false;
    stream?.getTracks().forEach(track => track.stop());
    if (video) video.srcObject = null;
  }
}
