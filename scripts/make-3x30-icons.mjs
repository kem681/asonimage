// Genere les icones PNG de la PWA 3x30 sans dependance : fond foret, trois
// barres dorees (les trois axes) de hauteurs croissantes. Encodage PNG a la
// main (zlib de Node). Usage : node scripts/make-3x30-icons.mjs
import { deflateSync } from 'node:zlib';
import { writeFileSync, mkdirSync } from 'node:fs';

const FOREST = [0x3a, 0x4a, 0x3a];
const GOLD = [0xc4, 0xa3, 0x5a];
const SAND = [0xf5, 0xf0, 0xe8];

function crc32(buf) {
  let c, crc = 0xffffffff;
  for (let n = 0; n < buf.length; n++) {
    c = (crc ^ buf[n]) & 0xff;
    for (let k = 0; k < 8; k++) c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1;
    crc = (crc >>> 8) ^ c;
  }
  return (crc ^ 0xffffffff) >>> 0;
}

function chunk(type, data) {
  const len = Buffer.alloc(4); len.writeUInt32BE(data.length);
  const body = Buffer.concat([Buffer.from(type, 'ascii'), data]);
  const crc = Buffer.alloc(4); crc.writeUInt32BE(crc32(body));
  return Buffer.concat([len, body, crc]);
}

function png(size, paint) {
  const raw = Buffer.alloc((size * 3 + 1) * size);
  for (let y = 0; y < size; y++) {
    raw[y * (size * 3 + 1)] = 0;
    for (let x = 0; x < size; x++) {
      const [r, g, b] = paint(x, y, size);
      const i = y * (size * 3 + 1) + 1 + x * 3;
      raw[i] = r; raw[i + 1] = g; raw[i + 2] = b;
    }
  }
  const ihdr = Buffer.alloc(13);
  ihdr.writeUInt32BE(size, 0); ihdr.writeUInt32BE(size, 4);
  ihdr[8] = 8; ihdr[9] = 2; ihdr[10] = 0; ihdr[11] = 0; ihdr[12] = 0;
  return Buffer.concat([
    Buffer.from([0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a]),
    chunk('IHDR', ihdr),
    chunk('IDAT', deflateSync(raw)),
    chunk('IEND', Buffer.alloc(0)),
  ]);
}

// Trois barres verticales dorees, hauteurs 0.35 / 0.55 / 0.75 de la zone utile,
// sur un fond foret. `inset` reserve une marge (icone maskable).
function painter(inset) {
  return (x, y, size) => {
    const s = size * (1 - 2 * inset);
    const ox = size * inset, oy = size * inset;
    const u = (x - ox) / s, v = (y - oy) / s;
    if (u < 0 || u > 1 || v < 0 || v > 1) return FOREST;
    const bars = [[0.22, 0.35], [0.44, 0.55], [0.66, 0.75]];
    const w = 0.12, base = 0.82;
    for (const [bx, h] of bars) {
      if (u >= bx && u <= bx + w && v <= base && v >= base - h) return GOLD;
    }
    if (v >= base + 0.02 && v <= base + 0.045 && u >= 0.16 && u <= 0.84) return SAND;
    return FOREST;
  };
}

mkdirSync('public/pwa', { recursive: true });
writeFileSync('public/pwa/icon-192.png', png(192, painter(0.08)));
writeFileSync('public/pwa/icon-512.png', png(512, painter(0.08)));
writeFileSync('public/pwa/icon-180.png', png(180, painter(0.08)));
writeFileSync('public/pwa/icon-maskable-512.png', png(512, painter(0.2)));
console.log('Icônes 3x30 générées dans public/pwa/');
