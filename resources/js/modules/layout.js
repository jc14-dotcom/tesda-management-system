/**
 * Vanilla JS layout module.
 *
 * Replaces Alpine sidebarLayout() and notifications() — every handler uses
 * event delegation on document/window so it works across all Turbo Drive
 * navigations without re-initialization after each body swap.
 *
 * Call initLayout() once on first page load.
 */
export function initLayout() {
    initSidebarEvents();
    initDropdownEvents();
    initNotificationEvents();
    initNotificationPanel();
    initFileInputEvents();
    initConfirmModal();
}

/**
 * Global confirmation modal helper.
 * Usage from JS:  window.showConfirm({ title, message, confirmText, onConfirm })
 * Usage from HTML: add data-confirm-message="..." (and optionally data-confirm-title,
 *                  data-confirm-text, data-confirm-form) to any button/link.
 */
window.showConfirm = ({ title = 'Are you sure?', message = '', confirmText = 'Confirm', onConfirm = null } = {}) => {
    window.dispatchEvent(new CustomEvent('show-confirm', { detail: { title, message, confirmText, onConfirm } }));
};

// ── Confirmation modal ────────────────────────────────────────────────────────

function initConfirmModal() {
    let _onConfirm = null;

    // Programmatic trigger
    window.addEventListener('show-confirm', (e) => {
        const { title = 'Are you sure?', message = '', confirmText = 'Confirm', onConfirm = null } = e.detail ?? {};
        _onConfirm = onConfirm;

        const modal      = document.getElementById('confirm-modal');
        const titleEl    = document.getElementById('confirm-modal-title');
        const msgEl      = document.getElementById('confirm-modal-message');
        const confirmBtn = document.getElementById('confirm-modal-confirm');

        if (titleEl)    titleEl.textContent    = title;
        if (msgEl)      msgEl.textContent      = message;
        if (confirmBtn) confirmBtn.textContent = confirmText;

        modal?.classList.remove('hidden');
        document.body.classList.add('overflow-y-hidden');
    });

    // Event delegation: data-confirm-message attribute on any element
    document.addEventListener('click', (e) => {
        const el = e.target.closest('[data-confirm-message]');
        if (el) {
            e.preventDefault();
            const formId = el.dataset.confirmForm;
            const href   = el.getAttribute('href');
            window.showConfirm({
                title:       el.dataset.confirmTitle   || 'Are you sure?',
                message:     el.dataset.confirmMessage || '',
                confirmText: el.dataset.confirmText    || 'Confirm',
                onConfirm: () => {
                    if (formId) {
                        document.getElementById(formId)?.submit();
                    } else if (href && href !== '#') {
                        window.location.href = href;
                    }
                },
            });
        }
    });

    // Confirm / cancel / backdrop / Escape
    document.addEventListener('click', (e) => {
        if (e.target.closest('#confirm-modal-confirm')) {
            closeConfirmModal();
            _onConfirm?.();
            _onConfirm = null;
            return;
        }
        if (e.target.closest('#confirm-modal-cancel') || e.target.id === 'confirm-modal-backdrop') {
            closeConfirmModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeConfirmModal();
    });
}

function closeConfirmModal() {
    const modal = document.getElementById('confirm-modal');
    if (!modal || modal.classList.contains('hidden')) return;
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-y-hidden');
}

// ── Sidebar ────────────────────────────────────────────────────────────────────

function initSidebarEvents() {
    const COLLAPSED_KEY = 'sidebar-collapsed';

    document.addEventListener('click', (e) => {
        if (e.target.closest('#sidebar-desktop-toggle')) {
            const collapsed = document.documentElement.classList.toggle('sidebar-collapsed');
            localStorage.setItem(COLLAPSED_KEY, collapsed ? 'true' : 'false');
            return;
        }

        if (e.target.closest('#sidebar-mobile-toggle')) {
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const btn     = document.getElementById('sidebar-mobile-toggle');
            if (!sidebar) return;
            const open = sidebar.classList.toggle('mobile-open');
            overlay?.classList.toggle('hidden', !open);
            btn?.querySelector('.mobile-icon-menu')?.classList.toggle('hidden', open);
            btn?.querySelector('.mobile-icon-close')?.classList.toggle('hidden', !open);
            return;
        }

        if (e.target.closest('#sidebar-overlay')) {
            closeMobileSidebar();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeMobileSidebar();
    });
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const btn     = document.getElementById('sidebar-mobile-toggle');
    if (!sidebar) return;
    sidebar.classList.remove('mobile-open');
    overlay?.classList.add('hidden');
    btn?.querySelector('.mobile-icon-menu')?.classList.remove('hidden');
    btn?.querySelector('.mobile-icon-close')?.classList.add('hidden');
}

// ── Dropdown ──────────────────────────────────────────────────────────────────

function initDropdownEvents() {
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.dropdown-trigger');

        // Close every open dropdown whose wrapper does not contain the click target
        document.querySelectorAll('.dropdown-menu.dropdown-open').forEach((menu) => {
            const wrapper = menu.closest('.dropdown-wrapper');
            if (!wrapper?.contains(e.target)) {
                menu.classList.remove('dropdown-open');
            }
        });

        if (trigger) {
            const menu = trigger.closest('.dropdown-wrapper')?.querySelector('.dropdown-menu');
            if (menu) menu.classList.toggle('dropdown-open');
        }
    });
}

// ── Notifications ─────────────────────────────────────────────────────────────

function initNotificationEvents() {
    // Window events survive for the lifetime of the app — no re-registration needed
    window.addEventListener('show-toast',       (e) => addToast(e.detail ?? {}));
    window.addEventListener('show-error-modal', (e) => openErrorModal(e.detail ?? {}));

    // Delegated: toast dismiss + error modal close
    document.addEventListener('click', (e) => {
        const dismissBtn = e.target.closest('[data-dismiss-toast]');
        if (dismissBtn) {
            dismissToast(dismissBtn.closest('[data-toast-id]'));
            return;
        }
        if (e.target.closest('[data-close-error-modal]') || e.target.id === 'error-modal-backdrop') {
            closeErrorModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeErrorModal();
    });
}

function addToast({ type = 'success', title = '', message = '' }) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const iconPaths = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>',
        error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
        info:    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
    };

    const id = Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
    const el = document.createElement('div');
    el.setAttribute('data-toast-id', id);
    el.className = `toast-item toast-${type} pointer-events-auto relative flex min-w-[20rem] max-w-xs items-start gap-3 overflow-hidden rounded-card border bg-white pl-4 pr-3 py-3.5 shadow-modal transition-all duration-300 opacity-0 translate-x-8`;
    el.innerHTML =
        `<div class="toast-icon flex h-9 w-9 shrink-0 items-center justify-center rounded-full">` +
            `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">${iconPaths[type] ?? iconPaths.info}</svg>` +
        `</div>` +
        `<div class="min-w-0 flex-1 pt-0.5">` +
            `<p class="text-sm font-bold leading-snug text-grayTheme-dark">${escHtml(title)}</p>` +
            (message ? `<p class="mt-0.5 text-xs leading-relaxed text-grayTheme-medium">${escHtml(message)}</p>` : '') +
        `</div>` +
        `<button type="button" class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-grayTheme-medium transition hover:bg-grayTheme-light hover:text-grayTheme-dark" data-dismiss-toast="${id}" aria-label="Dismiss">` +
            `<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">` +
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>` +
            `</svg>` +
        `</button>` +
        `<div class="toast-bar absolute bottom-0 left-0 h-[3px] w-full"></div>`;

    container.appendChild(el);

    // Animate in
    requestAnimationFrame(() => requestAnimationFrame(() => {
        el.classList.remove('opacity-0', 'translate-x-8');
        const bar = el.querySelector('.toast-bar');
        if (bar) {
            bar.style.transition = 'width linear 6s';
            bar.style.width = '0%';
        }
    }));

    setTimeout(() => dismissToast(el), 6000);
}

// ── Notification Panel ────────────────────────────────────────────────────────

function initNotificationPanel() {
    const csrf = () => document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';

    document.addEventListener('click', (e) => {
        const panel   = document.getElementById('notif-panel');
        const wrapper = document.getElementById('notif-panel-wrapper');
        if (!panel || !wrapper) return;

        const panelOpen = !panel.classList.contains('hidden');

        // Close on outside click
        if (panelOpen && !wrapper.contains(e.target)) {
            panel.classList.add('hidden');
            document.getElementById('notif-panel-toggle')?.setAttribute('aria-expanded', 'false');
            return;
        }

        if (!wrapper.contains(e.target)) return;

        // Toggle bell
        if (e.target.closest('#notif-panel-toggle')) {
            const nowHidden = panel.classList.toggle('hidden');
            e.target.closest('#notif-panel-toggle').setAttribute('aria-expanded', nowHidden ? 'false' : 'true');
            return;
        }

        // Mark all as read
        if (e.target.closest('#notif-mark-all-btn')) {
            const btn = document.getElementById('notif-mark-all-btn');
            const url = btn?.dataset.url;
            if (!url) return;
            fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    document.querySelectorAll('#notif-list [data-notif-id]').forEach(item => {
                        item.classList.remove('bg-primary-soft/30');
                        item.dataset.read = '1';
                        const dot = item.querySelector('[data-notif-dot]');
                        if (dot) { dot.classList.remove('bg-primary'); dot.classList.add('bg-transparent'); }
                        const title = item.querySelector('[data-notif-title]');
                        if (title) { title.classList.remove('font-semibold', 'text-grayTheme-dark'); title.classList.add('font-medium', 'text-grayTheme-medium'); }
                    });
                    notifUpdateBadge(0);
                }).catch(() => {});
            return;
        }

        // Delete button
        const deleteBtn = e.target.closest('.notif-delete-btn');
        if (deleteBtn) {
            const item = deleteBtn.closest('[data-notif-id]');
            const url  = item?.dataset.deleteUrl;
            if (!url) return;
            fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const wasUnread = item.dataset.read === '0';
                    item.style.transition = 'opacity 200ms ease';
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.remove();
                        if (wasUnread) notifDecrementBadge();
                        if (!document.querySelector('#notif-list [data-notif-id]')) notifShowEmpty();
                    }, 200);
                }).catch(() => {});
            return;
        }

        // Click item → mark read + navigate
        const notifItem = e.target.closest('[data-notif-id]');
        if (notifItem) {
            const readUrl = notifItem.dataset.readUrl;
            const viewUrl = notifItem.dataset.viewUrl;
            if (notifItem.dataset.read === '0' && readUrl) {
                fetch(readUrl, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' } }).catch(() => {});
                notifDecrementBadge();
                notifItem.dataset.read = '1';
            }
            if (viewUrl) window.location.href = viewUrl;
        }
    });
}

function notifUpdateBadge(count) {
    const badge      = document.getElementById('notif-badge');
    const panelBadge = document.getElementById('notif-unread-badge');
    const markAllBtn = document.getElementById('notif-mark-all-btn');
    if (count <= 0) {
        badge?.remove();
        panelBadge?.classList.add('hidden');
        markAllBtn?.classList.add('hidden');
    } else {
        const label = count > 9 ? '9+' : String(count);
        if (badge)      badge.textContent = label;
        if (panelBadge) { panelBadge.textContent = label; panelBadge.classList.remove('hidden'); }
        if (markAllBtn) markAllBtn.classList.remove('hidden');
    }
}

function notifDecrementBadge() {
    const badge   = document.getElementById('notif-badge');
    const current = parseInt(badge?.textContent ?? '1', 10);
    notifUpdateBadge(Math.max(0, current - 1));
}

function notifShowEmpty() {
    const list = document.getElementById('notif-list');
    if (!list) return;
    list.innerHTML =
        `<div class="flex flex-col items-center justify-center gap-2 py-12 text-center">` +
            `<div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">` +
                `<svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">` +
                    `<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />` +
                `</svg>` +
            `</div>` +
            `<p class="text-sm font-semibold text-grayTheme-dark">All caught up!</p>` +
            `<p class="text-xs text-grayTheme-medium">No notifications to show.</p>` +
        `</div>`;
}

function dismissToast(el) {
    if (!el?.isConnected) return;
    el.classList.add('opacity-0', 'translate-x-4');
    setTimeout(() => el.remove(), 300);
}

function openErrorModal({ title = 'Error', message = '', fieldErrors = {} }) {
    const modal    = document.getElementById('error-modal');
    const titleEl  = document.getElementById('error-modal-title');
    const msgEl    = document.getElementById('error-modal-message');
    const errorsEl = document.getElementById('error-modal-errors');

    if (!modal) return;

    if (titleEl) titleEl.textContent = title;
    if (msgEl)   msgEl.textContent   = message;

    if (errorsEl) {
        const entries = Object.entries(fieldErrors ?? {});
        if (entries.length) {
            errorsEl.innerHTML =
                '<div class="mt-4 space-y-3">' +
                    '<p class="text-sm font-semibold text-gray-700">Validation Issues</p>' +
                    '<ul class="text-sm list-disc list-inside text-gray-600">' +
                        entries.map(([, msgs]) =>
                            `<li>${escHtml(Array.isArray(msgs) ? msgs.join('; ') : String(msgs))}</li>`
                        ).join('') +
                    '</ul>' +
                '</div>';
            errorsEl.hidden = false;
        } else {
            errorsEl.innerHTML = '';
            errorsEl.hidden = true;
        }
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-y-hidden');
}

function closeErrorModal() {
    const modal = document.getElementById('error-modal');
    if (!modal || modal.classList.contains('hidden')) return;
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-y-hidden');
}

// ── File inputs ───────────────────────────────────────────────────────────────

function initFileInputEvents() {
    document.addEventListener('change', (e) => {
        if (e.target.type !== 'file') return;
        const wrapper = e.target.closest('[data-file-input]');
        if (!wrapper) return;
        const display = wrapper.querySelector('[data-file-name]');
        if (!display) return;
        const placeholder = display.dataset.placeholder ?? 'Choose file';
        display.textContent = e.target.files?.length ? e.target.files[0].name : placeholder;
    });
}

// ── Utility ───────────────────────────────────────────────────────────────────

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
