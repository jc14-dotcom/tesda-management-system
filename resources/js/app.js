import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('sidebarLayout', () => ({
	desktopCollapsed: false,
	mobileOpen: false,
	init() {
		const stored = window.localStorage.getItem('sidebar-collapsed');

		if (stored === null) {
			this.desktopCollapsed = false;
			return;
		}

		this.desktopCollapsed = stored === 'true';
	},
	toggleDesktopSidebar() {
		this.desktopCollapsed = ! this.desktopCollapsed;
		window.localStorage.setItem('sidebar-collapsed', this.desktopCollapsed ? 'true' : 'false');
	},
	closeMobileSidebar() {
		this.mobileOpen = false;
	},
}));

Alpine.start();
