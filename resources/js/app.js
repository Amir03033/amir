import './bootstrap';
import '@fontsource-variable/inter';
import '@fontsource-variable/jetbrains-mono';
import Alpine from 'alpinejs';
import { initMotion } from './motion';
import { initTracking } from './track';

window.Alpine = Alpine;
Alpine.start();

initMotion();
initTracking();
