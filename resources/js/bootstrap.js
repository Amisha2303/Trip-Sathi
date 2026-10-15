import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * CSRF token taken from the meta tag the layout emits, so AJAX POSTs to the
 * web routes work without jQuery and without hardcoding the cookie name.
 */
const csrfToken = document.head.querySelector('meta[name="csrf-token"]');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

/**
 * Normalize jQuery-less fetch helpers so partial-page flows (coupon apply,
 * newsletter subscribe) share one response contract.
 */
window.TripSathi = window.TripSathi || {};
window.TripSathi.toJSON = async (response) => {
    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const error = new Error(data.message || 'Request failed');
        error.status = response.status;
        error.errors = data.errors || {};
        throw error;
    }

    return data;
};
