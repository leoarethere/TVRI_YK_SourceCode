import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';
import './bootstrap';

window.Alpine = Alpine;
window.alpineStarted = false;

Alpine.start();
window.alpineStarted = true;

document.addEventListener('turbo:submit-start', (event) => {
    showLoadingState(event.target);
});

document.addEventListener('turbo:submit-end', (event) => {
    hideLoadingState(event.target);
});

document.addEventListener('turbo:fetch-request-error', (event) => {
    console.error('Permintaan Turbo gagal:', event.detail);
    showErrorMessage('Terjadi gangguan jaringan. Silakan coba lagi.');
});

function showLoadingState(form) {
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

    if (!submitButton || submitButton.disabled) {
        return;
    }

    submitButton.disabled = true;
    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    submitButton.setAttribute('data-original-text', submitButton.innerHTML);
    submitButton.innerHTML = submitButton.getAttribute('data-loading-text') || 'Memproses...';
}

function hideLoadingState(form) {
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

    if (!submitButton?.hasAttribute('data-original-text')) {
        return;
    }

    submitButton.disabled = false;
    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    submitButton.innerHTML = submitButton.getAttribute('data-original-text');
    submitButton.removeAttribute('data-original-text');
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 z-50 rounded-lg bg-red-500 p-4 text-white shadow-lg';
    errorDiv.setAttribute('role', 'alert');
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);

    setTimeout(() => errorDiv.remove(), 5000);
}

Turbo.setProgressBarDelay(500);

export { Alpine, Turbo };
