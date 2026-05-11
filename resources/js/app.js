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

Alpine.data('loadMoreList', ({ nextUrl, partialParam }) => ({
	nextUrl,
	partialParam,
	loading: false,
	async loadMore() {
		if (!this.nextUrl || this.loading) {
			return;
		}

		this.loading = true;

		try {
			const url = new URL(this.nextUrl, window.location.origin);
			url.searchParams.set(this.partialParam, '1');

			const response = await fetch(url.toString(), {
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
				},
			});

			if (!response.ok) {
				throw new Error('Failed to load more results.');
			}

			const payload = await response.json();
			if (payload?.html) {
				this.$refs.list.insertAdjacentHTML('beforeend', payload.html);
				if (window.Alpine?.initTree) {
					window.Alpine.initTree(this.$refs.list);
				}
			}
			this.nextUrl = payload?.nextUrl ?? null;
		} catch (error) {
			console.error(error);
		} finally {
			this.loading = false;
		}
	},
}));

Alpine.start();
