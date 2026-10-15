// ---------------------------------------------------------------------------
// Save-to-wishlist heart buttons: optimistic toggle with a toast fallback.
// Configured by the layout through window.TS_WISHLIST.
// ---------------------------------------------------------------------------

function showToast(message, tone = 'info') {
    const host = document.querySelector('[data-ts-toasts]');
    if (!host || !window.bootstrap) return;

    const tones = {
        success: 'text-success',
        error: 'text-danger',
        info: 'text-info',
        warning: 'text-warning',
    };
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        info: 'bi-info-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
    };

    const wrapper = document.createElement('div');
    wrapper.className = 'toast';
    wrapper.setAttribute('role', 'alert');
    wrapper.innerHTML = `
        <div class="toast-header">
            <span class="me-2 ${tones[tone] || tones.info}"><i class="bi ${icons[tone] || icons.info}"></i></span>
            <strong class="me-auto">${tone === 'error' ? 'Something went wrong' : 'Trip Sathi'}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>`;

    wrapper.querySelector('.toast-body').textContent = message;
    host.appendChild(wrapper);

    const toast = new bootstrap.Toast(wrapper, { delay: 4000 });
    toast.show();
    wrapper.addEventListener('hidden.bs.toast', () => wrapper.remove());
}

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-ts-wish]');
    if (!button) return;

    event.preventDefault();

    const config = window.TS_WISHLIST || {};
    if (!config.store) return;

    const saved = button.classList.contains('is-active');
    button.disabled = true;

    try {
        const { data } = await window.axios.post(config.store, {
            type: button.dataset.type,
            id: button.dataset.id,
        });

        button.classList.toggle('is-active', Boolean(data.saved));
        button.setAttribute('aria-pressed', data.saved ? 'true' : 'false');

        const icon = button.querySelector('i');
        if (icon) {
            icon.className = data.saved ? 'bi bi-heart-fill' : 'bi bi-heart';
        }

        showToast(data.message, data.saved ? 'success' : 'info');
    } catch (error) {
        if (error.status === 419) {
            window.location.reload();
            return;
        }

        if (error.status === 401 && config.loginUrl) {
            window.location.href = config.loginUrl;
            return;
        }

        // Roll back the visual state we optimistically assumed.
        button.classList.toggle('is-active', saved);
        showToast(error.message || 'Could not update your wishlist.', 'error');
    } finally {
        button.disabled = false;
    }
});

export { showToast };
