/**
 * Shared UI Alpine components used across multiple pages
 * (certificate list, document list, admin user detail).
 *
 * loadMoreList — appends paginated items via JSON API without a full page reload.
 * Now uses client-side rendering with Alpine templates for better performance.
 */
export function registerUiComponents(Alpine) {
    Alpine.data('loadMoreList', ({ nextUrl, partialParam }) => ({
        nextUrl,
        partialParam,
        items: [],
        loading: false,

        async loadMore() {
            if (!this.nextUrl || this.loading) return;

            this.loading = true;

            try {
                const url = new URL(this.nextUrl, window.location.origin);
                url.searchParams.set(this.partialParam, '1');

                const response = await fetch(url.toString(), {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load more results.');
                }

                const payload = await response.json();

                if (payload?.items && Array.isArray(payload.items)) {
                    this.items.push(...payload.items);
                }

                this.nextUrl = payload?.nextUrl ?? null;
            } catch (error) {
                console.error('Load more error:', error);
                window.dispatchEvent(new CustomEvent('show-toast', { 
                    detail: { 
                        type: 'error', 
                        title: 'Error', 
                        message: 'Failed to load more items.' 
                    } 
                }));
            } finally {
                this.loading = false;
            }
        },
    }));
}
