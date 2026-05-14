import '@hotwired/turbo';
import './bootstrap';
import 'flowbite';

import { initLayout } from './modules/layout.js';

import Alpine from 'alpinejs';

// Wire Alpine to Turbo Drive body swaps
document.addEventListener('turbo:before-render', () => Alpine.destroyTree(document.body));
document.addEventListener('turbo:render',        () => Alpine.initTree(document.body));

import { registerUiComponents } from './modules/ui.js';
import { registerProfileComponents } from './modules/profile.js';

window.Alpine = Alpine;

registerUiComponents(Alpine);
registerProfileComponents(Alpine);

Alpine.start();

// Initialize vanilla JS layout once — event delegation handles all Turbo navigations
initLayout();
