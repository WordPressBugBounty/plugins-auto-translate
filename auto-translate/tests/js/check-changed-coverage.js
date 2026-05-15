#!/usr/bin/env node

const fs = require('node:fs');
const path = require('node:path');
const { execSync } = require('node:child_process');

const COVERAGE_SUMMARY_PATH = path.join('coverage', 'js', 'coverage-summary.json');
const MIN_PERCENT = Number.parseFloat(process.env.WPAT_CHANGED_MIN_COVERAGE || '80');

function run(cmd) {
  return execSync(cmd, { encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] }).trim();
}

function getBaseRef() {
  if (process.env.WPAT_COVERAGE_BASE) {
    return process.env.WPAT_COVERAGE_BASE;
  }

  if (process.env.GITHUB_BASE_REF) {
    const remoteRef = `origin/${process.env.GITHUB_BASE_REF}`;
    try {
      return run(`git merge-base HEAD ${remoteRef}`);
    } catch {
      // Fall back below.
    }
  }

  try {
    return run('git rev-parse HEAD~1');
  } catch {
    return null;
  }
}

function getChangedFiles(baseRef) {
  if (!baseRef) {
    return [];
  }

  const output = run(`git diff --name-only --diff-filter=AMRT ${baseRef}...HEAD`);
  if (!output) {
    return [];
  }

  return output
    .split('\n')
    .map((file) => file.trim())
    .filter(Boolean)
    .filter((file) => file.startsWith('src/') && file.endsWith('.js'))
    .filter((file) => !file.startsWith('src/fontawesome/'))
    .filter((file) => !file.startsWith('src/admin/scripts/'))
    .filter((file) => ![
      'src/public/scripts/bannerGuard.js',
      'src/public/scripts/iframeStyles.js',
      'src/public/scripts/listenCookieChange.js',
      'src/global/scripts/custom.jquery.js',
    ].includes(file));
}

if (!fs.existsSync(COVERAGE_SUMMARY_PATH)) {
  console.error(`Coverage summary not found at ${COVERAGE_SUMMARY_PATH}. Run test:js:coverage first.`);
  process.exit(1);
}

const summary = JSON.parse(fs.readFileSync(COVERAGE_SUMMARY_PATH, 'utf8'));
const baseRef = getBaseRef();
const changedFiles = getChangedFiles(baseRef);

if (changedFiles.length === 0) {
  console.log('No changed instrumented JS files found.');
  process.exit(0);
}

const failures = [];
for (const file of changedFiles) {
  const absoluteFile = path.resolve(file);
  const entry = summary[file] || summary[absoluteFile];
  if (!entry) {
    failures.push(`${file}: missing coverage data`);
    continue;
  }

  const pct = entry.lines.pct;
  if (pct < MIN_PERCENT) {
    failures.push(`${file}: lines ${pct}% < ${MIN_PERCENT}%`);
  }
}

if (failures.length > 0) {
  console.error('Changed-file JS coverage check failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`Changed-file JS coverage check passed (${MIN_PERCENT}% min).`);
