/**
 * Notifications Alpine component.
 * Provides a global toast stack and error modal, both triggered via
 * custom window events: `show-toast` and `show-error-modal`.
 */
export function registerNotifications(Alpine) {
    Alpine.data('notifications', () => ({
        toasts: [],
        modal: {
            open: false,
            title: '',
            message: '',
            fieldErrors: {},
        },

        addToast(detail) {
            const id =
                Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
            const t = {
                id,
                type: detail.type || 'success',
                title: detail.title || '',
                message: detail.message || '',
                progress: 100,
                removing: false,
            };

            this.toasts.push(t);

            const interval = setInterval(() => {
                t.progress = Math.max(0, t.progress - 100 / (4 * 10));
            }, 100);

            setTimeout(() => {
                clearInterval(interval);
                this.removeToast(id);
            }, 4200);
        },

        removeToast(id) {
            const idx = this.toasts.findIndex((x) => x.id === id);
            if (idx === -1) return;
            this.toasts[idx].removing = true;
            setTimeout(() => {
                this.toasts.splice(idx, 1);
            }, 300);
        },

        openErrorModal(detail) {
            this.modal.title = detail.title || 'Error';
            this.modal.message = detail.message || '';
            this.modal.fieldErrors = detail.fieldErrors || {};
            this.modal.open = true;
        },

        closeModal() {
            this.modal.open = false;
        },
    }));
}
