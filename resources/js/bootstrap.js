// CSRF token for non-GET requests (used by form submissions and fetch calls).
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window._csrfToken = token.content;
}
