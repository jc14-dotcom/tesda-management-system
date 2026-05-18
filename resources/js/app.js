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

Alpine.data('liveSearch', () => ({
    async search(form) {
        const url = new URL(window.location.href);
        const formData = new FormData(form);
        formData.delete('_token');
        formData.delete('_method');
        url.search = new URLSearchParams(formData).toString();
        try {
            const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return form.submit();
            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const fresh   = doc.getElementById('live-search-results');
            const current = document.getElementById('live-search-results');
            if (fresh && current) {
                Alpine.destroyTree(current);
                current.replaceWith(fresh);
                Alpine.initTree(fresh);
            }
            history.pushState({}, '', url.toString());
        } catch {
            form.submit();
        }
    }
}));

Alpine.start();

// Initialize vanilla JS layout once — event delegation handles all Turbo navigations
initLayout();
