#!/usr/bin/env node
const { bundle } = require('lightningcss');
const fs = require('fs');

const SRC = 'src/css/main.css';
const OUT = 'public/style.css';
const WATCH  = process.argv.includes('--watch');
const MINIFY = process.argv.includes('--minify');

function build() {
  try {
    const { code } = bundle({ filename: SRC, minify: MINIFY });
    fs.writeFileSync(OUT, code);
    console.log(`[css] → ${OUT} (${code.length}b)`);
  } catch (e) {
    console.error('[css] error:', e.message);
  }
}

build();

if (WATCH) {
  fs.watch('src/css', { recursive: true }, (_, filename) => {
    if (filename?.endsWith('.css')) build();
  });
  console.log('[css] watching src/css...');
}
