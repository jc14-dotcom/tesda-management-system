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
    // Only poll when the notification bell is present (authenticated layouts)
    if (document.getElementById('notif-bell') || document.getElementById('notif-badge')) {
        initNotificationPolling();
    }
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
            // Close notification panel when sidebar opens
            if (open) {
                const notifPanel = document.getElementById('notif-panel');
                if (notifPanel && !notifPanel.classList.contains('hidden')) {
                    notifPanel.classList.add('hidden');
                    document.getElementById('notif-panel-toggle')?.setAttribute('aria-expanded', 'false');
                }
            }
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

    window.__toastReady = true;
    const pendingToasts = Array.isArray(window.__pendingToasts) ? window.__pendingToasts.splice(0) : [];
    pendingToasts.forEach((toast) => addToast(toast));

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

    const configs = {
        success: {
            gradient: 'linear-gradient(135deg, #065f46, #047857)',
            shadow:   '0 8px 30px rgba(6,95,70,.30)',
            viewBox:  '0 0 512 512',
            svgPath:  '<path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>',
        },
        error: {
            gradient: 'linear-gradient(135deg, #800000, #991b1b)',
            shadow:   '0 8px 30px rgba(128,0,0,.30)',
            viewBox:  '0 0 512 512',
            svgPath:  '<path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1-64 0z"/>',
        },
        warning: {
            gradient: 'linear-gradient(135deg, #92400e, #b45309)',
            shadow:   '0 8px 30px rgba(146,64,14,.30)',
            viewBox:  '0 0 512 512',
            svgPath:  '<path fill="currentColor" d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7.2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8.2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0-64 0 32 32 0 1 0 64 0z"/>',
        },
        info: {
            gradient: 'linear-gradient(135deg, #1e3a5f, #1e40af)',
            shadow:   '0 8px 30px rgba(30,64,175,.30)',
            viewBox:  '0 0 512 512',
            svgPath:  '<path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>',
        },
    };

    const cfg = configs[type] ?? configs.info;
    const id  = Date.now().toString(36) + Math.random().toString(36).slice(2, 7);

    // ── Toast wrapper ───────────────────────────────────────────────────────
    const el = document.createElement('div');
    el.setAttribute('data-toast-id', id);

    // Pure inline styles — no Tailwind class dependency so animation is reliable
    el.style.cssText = [
        'pointer-events:auto',
        'display:flex',
        'align-items:center',
        'gap:14px',
        'padding:16px 18px 19px 18px',
        'border-radius:12px',
        `background:${cfg.gradient}`,
        `box-shadow:${cfg.shadow}`,
        'color:#fff',
        'position:relative',
        'overflow:hidden',
        'min-width:320px',
        'max-width:400px',
        // Start state for slide-in animation
        'opacity:0',
        'transform:translateX(40px)',
        'transition:opacity 0.4s cubic-bezier(.22,1,.36,1), transform 0.4s cubic-bezier(.22,1,.36,1)',
    ].join(';');

    el.innerHTML =
        // ── Icon badge (rounded square with semi-transparent white bg — matches images 2 & 3) ──
        `<div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.20);` +
             `display:flex;align-items:center;justify-content:center;flex-shrink:0;">` +
            `<svg style="width:20px;height:20px;display:block;" viewBox="${cfg.viewBox}" ` +
                 `xmlns="http://www.w3.org/2000/svg">${cfg.svgPath}</svg>` +
        `</div>` +
        // ── Body ─────────────────────────────────────────────────────────────
        `<div style="flex:1;min-width:0;">` +
            `<div style="font-weight:700;font-size:14px;color:#fff;line-height:1.3;">${escHtml(title)}</div>` +
            (message
                ? `<div style="font-size:12px;color:rgba(255,255,255,0.82);margin-top:3px;line-height:1.4;">${escHtml(message)}</div>`
                : '') +
        `</div>` +
        // ── Close button ─────────────────────────────────────────────────────
        `<button type="button" data-dismiss-toast="${id}" aria-label="Dismiss" ` +
                `style="background:none;border:none;padding:4px;cursor:pointer;color:rgba(255,255,255,0.6);` +
                       `display:flex;align-items:center;flex-shrink:0;">` +
            `<svg style="width:14px;height:14px;display:block;" fill="none" viewBox="0 0 24 24" ` +
                 `stroke="currentColor" stroke-width="2.5">` +
                `<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>` +
            `</svg>` +
        `</button>` +
        // ── Progress bar ─────────────────────────────────────────────────────
        `<div data-toast-bar style="position:absolute;bottom:0;left:0;height:3px;width:100%;` +
             `background:rgba(255,255,255,0.35);border-radius:0 0 12px 12px;"></div>`;

    container.appendChild(el);

    // ── Slide-in animation (two rAFs: first commits initial state, second transitions) ──
    requestAnimationFrame(() => requestAnimationFrame(() => {
        el.style.opacity   = '1';
        el.style.transform = 'translateX(0)';
    }));

    // ── Progress bar shrink ─────────────────────────────────────────────────
    // getBoundingClientRect() forces a reflow so the browser records width:100%
    // as the start value BEFORE the transition begins — without this the
    // browser may batch start+end in one frame and skip the animation entirely.
    requestAnimationFrame(() => {
        const bar = el.querySelector('[data-toast-bar]');
        if (!bar) return;
        bar.getBoundingClientRect();              // force reflow
        bar.style.transition = 'width 4.5s linear';
        bar.style.width      = '0%';
    });

    setTimeout(() => dismissToast(el), 4500);
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
            // Close mobile sidebar when notification panel opens
            if (!nowHidden) closeMobileSidebar();
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
    const toggle     = document.getElementById('notif-panel-toggle');
    let   badge      = document.getElementById('notif-badge');
    const panelBadge = document.getElementById('notif-unread-badge');
    const markAllBtn = document.getElementById('notif-mark-all-btn');
    if (count <= 0) {
        badge?.remove();
        panelBadge?.classList.add('hidden');
        markAllBtn?.classList.add('hidden');
    } else {
        const label = count > 9 ? '9+' : String(count);
        // Re-create badge if it was removed when count previously hit 0
        if (!badge && toggle) {
            badge = document.createElement('span');
            badge.id        = 'notif-badge';
            badge.className = 'absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-danger text-[10px] font-bold leading-none text-white';
            toggle.appendChild(badge);
        }
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
    el.style.opacity = '0';
    el.style.transform = 'translateX(30px)';
    setTimeout(() => el.remove(), 350);
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

// ── Notification polling ──────────────────────────────────────────────────────

function initNotificationPolling() {
    const POLL_INTERVAL = 30_000; // 30 seconds

    let lastLatestId  = undefined; // undefined = baseline not yet established
    let lastUnreadCnt = 0;
    let pollTimer     = null;

    async function poll() {
        try {
            const res = await fetch('/notifications/poll', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (res.status === 401 || res.status === 403) {
                // User is no longer authenticated — stop polling
                clearTimeout(pollTimer);
                return;
            }
            if (!res.ok) return;

            const data        = await res.json();
            const unreadCount = data.unread_count ?? 0;
            const latestId    = data.latest_id    ?? null;

            // First poll: establish baseline from the server-rendered state
            if (lastLatestId === undefined) {
                lastLatestId  = latestId;
                lastUnreadCnt = unreadCount;
                return;
            }

            const isNewNotif = unreadCount > lastUnreadCnt;
            const listChanged = latestId !== lastLatestId;

            if (listChanged) {
                lastLatestId  = latestId;
                lastUnreadCnt = unreadCount;
                await refreshPanel(isNewNotif ? { title: data.latest_title, message: data.latest_message } : null);
            } else if (unreadCount !== lastUnreadCnt) {
                // Count changed (read in another tab) — just sync the badge
                lastUnreadCnt = unreadCount;
                notifUpdateBadge(unreadCount);
            }
        } catch (_) {
            // Network error — silently ignore, try again next interval
        }
    }

    async function refreshPanel(toastData) {
        try {
            const res = await fetch('/notifications/panel', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;

            const data = await res.json();

            // Replace the list HTML
            const list = document.getElementById('notif-list');
            if (list) list.innerHTML = data.html ?? '';

            // Sync the badge
            notifUpdateBadge(data.unread_count ?? 0);

            // Show a toast only if the panel is currently closed
            if (toastData) {
                const panel = document.getElementById('notif-panel');
                if (!panel || panel.classList.contains('hidden')) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type:    'info',
                            title:   toastData.title   ?? 'New Notification',
                            message: toastData.message ?? 'You have a new notification.',
                        },
                    }));
                }
            }
        } catch (_) {
            // Silently ignore
        }
    }

    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(poll, POLL_INTERVAL);
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    // Establish baseline immediately, then poll on interval
    poll();
    startPolling();

    // Pause when the tab is hidden, resume + immediate check when visible again
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            stopPolling();
        } else {
            poll();
            startPolling();
        }
    });
}
