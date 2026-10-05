import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import api from 'face-api.js';

test('the shipped face recognition model produces a finite descriptor from an image tensor', async () => {
  const manifest = JSON.parse(await fs.readFile(new URL('../public/model/face_recognition_model-weights_manifest.json', import.meta.url), 'utf8'));
  const bytes = await fs.readFile(new URL('../public/model/face_recognition_model.bin', import.meta.url));
  const weights = api.tf.io.decodeWeights(bytes.buffer.slice(bytes.byteOffset, bytes.byteOffset + bytes.byteLength), manifest.flatMap(group => group.weights));
  api.nets.faceRecognitionNet.loadFromWeightMap(weights);
  const image = api.tf.zeros([150, 150, 3]);
  try {
    const descriptor = await api.nets.faceRecognitionNet.computeFaceDescriptor(image);
    assert.equal(descriptor.length, 128);
    assert.ok(Array.from(descriptor).every(Number.isFinite));
  } finally {
    image.dispose();
    api.nets.faceRecognitionNet.dispose();
    Object.values(weights).forEach(weight => { if (!weight.isDisposed) weight.dispose(); });
  }
});
