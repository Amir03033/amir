// Copies the whitelisted Lucide icons into resources/icons so the server (no node_modules) can inline them.
// Run with: node scripts/icons.mjs
import { copyFileSync, existsSync, mkdirSync, readdirSync, rmSync } from 'node:fs';

const icons = [
    'arrow-right', 'arrow-left', 'arrow-up-right', 'arrow-up', 'chevron-up', 'chevron-down',
    'sun', 'moon', 'monitor', 'menu', 'x', 'check', 'circle-alert', 'info', 'loader-circle',
    'mail', 'copy', 'send', 'search', 'terminal', 'plus', 'file-text', 'globe',
];

const src = 'node_modules/lucide-static/icons';
const out = 'resources/icons';
mkdirSync(out, { recursive: true });
readdirSync(out).filter((f) => f.endsWith('.svg')).forEach((f) => rmSync(`${out}/${f}`));

const missing = icons.filter((name) => !existsSync(`${src}/${name}.svg`));
if (missing.length) {
    console.error('Missing icons:', missing.join(', '));
    process.exit(1);
}
icons.forEach((name) => copyFileSync(`${src}/${name}.svg`, `${out}/${name}.svg`));
console.log(`Copied ${icons.length} icons to ${out}`);
