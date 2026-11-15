import Alpine from 'alpinejs';
import { initTheme } from './theme';
import { initNav } from './nav';
import { initReveal } from './reveal';
import { initScrub } from './scrub';
import { initToasts } from './toast';
import { initDialogs } from './dialog';
import { initTracking } from './track';

window.Alpine = Alpine;
Alpine.start();

initTheme();
initNav();
initReveal();
initScrub();
initToasts();
initDialogs();
initTracking();
