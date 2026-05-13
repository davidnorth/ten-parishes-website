#!/usr/bin/env node
const { bundle } = require('lightningcss');
const fs = require('fs');
const path = require('path');

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
  // fs.watch uses inotify which doesn't fire for Docker bind-mount changes on Linux
  function getMtimes(dir) {
    const mtimes = {};
    function scan(d) {
      for (const f of fs.readdirSync(d)) {
        const full = path.join(d, f);
        const stat = fs.statSync(full);
        if (stat.isDirectory()) scan(full);
        else if (f.endsWith('.css')) mtimes[full] = stat.mtimeMs;
      }
    }
    scan(dir);
    return mtimes;
  }

  let prev = getMtimes('src/css');

  setInterval(() => {
    const curr = getMtimes('src/css');
    const changed = Object.keys(curr).some(f => curr[f] !== prev[f])
                 || Object.keys(prev).some(f => curr[f] === undefined);
    if (changed) {
      prev = curr;
      build();
    }
  }, 500);

  console.log('[css] watching src/css (polling)...');
}
