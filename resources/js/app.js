// FILE: resources/js/app.js

import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';
import './bootstrap';
import 'flowbite';

// --- Owl Carousel Setup ---
import jQuery from 'jquery';
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel/dist/assets/owl.theme.default.css';

window.$ = window.jQuery = jQuery;

import('owl.carousel').then(() => {
    console.log('🦉 Owl Carousel Loaded Successfully');
    document.dispatchEvent(new Event('owl-loaded'));
}).catch((err) => console.error('Owl loading failed', err));

// --- Alpine Setup ---
window.Alpine = Alpine;
window.appInitialized = false;
window.alpineStarted = false;

// ============================================
// INISIALISASI ALPINE
// ============================================
console.log('🔧 Starting Alpine.js immediately...');
Alpine.start();
window.alpineStarted = true;
console.log('✅ Alpine.js started');

// ============================================
// TURBO EVENT HANDLERS
// ============================================

document.addEventListener('turbo:render', function() {
    console.log('🎨 Turbo render complete');
    
    setTimeout(() => {
        // ❌ PERBAIKAN DI SINI:
        // Wajib dikomentari agar tidak berebut dengan Dashboard Layout
        // checkForFlashMessages(); 
        
        initializeFlowbite();
    }, 100); 
});

// Fallback untuk first load
document.addEventListener('DOMContentLoaded', function() {
    if (window.appInitialized) return;
    
    console.log('🚀 DOMContentLoaded - First page load');
    
    setTimeout(() => {
        // ❌ PERBAIKAN DI SINI JUGA:
        // Wajib dikomentari
        // checkForFlashMessages();
        
        initializeFlowbite();
    }, 150); 
    
    window.appInitialized = true;
});

// Cleanup before cache
document.addEventListener('turbo:before-cache', function() {
    console.log('🗑️ Turbo caching page - cleaning up');
    cleanupBeforeCache();
});

// Form submission handlers
document.addEventListener('turbo:submit-start', function(event) {
    console.log('📤 Form submission started');
    showLoadingState(event.target);
});

document.addEventListener('turbo:submit-end', function(event) {
    console.log('✅ Form submission ended');
    hideLoadingState(event.target);
});

// Error handler
document.addEventListener('turbo:fetch-request-error', function(event) {
    console.error('❌ Turbo fetch error:', event.detail);
    showErrorMessage('Network error occurred');
});

// ============================================
// FUNGSI FLASH MESSAGE (UTILITY)
// ============================================
// Biarkan fungsi ini tetap ada sebagai utility, 
// tapi jangan dipanggil otomatis di event listener di atas.
window.checkForFlashMessages = function() {
    console.log('🔍 Manual check for flash messages...');
    
    if (!window.Alpine || !window.Alpine.store) {
        return;
    }
    
    // 1. Cek Toast Notification
    const toastTrigger = document.getElementById('notification-trigger');
    if (toastTrigger) {
        const message = toastTrigger.getAttribute('data-message');
        const type = toastTrigger.getAttribute('data-type');
        
        if (message) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message, type }
            }));
            toastTrigger.remove();
        }
    }

    // 2. Cek Modal Popup
    const modalTrigger = document.getElementById('modal-trigger');
    if (modalTrigger) {
        const title = modalTrigger.getAttribute('data-title');
        const message = modalTrigger.getAttribute('data-message');
        const type = modalTrigger.getAttribute('data-type');
        
        if (message) {
            window.dispatchEvent(new CustomEvent('show-modal', {
                detail: { title, message, type }
            }));
            modalTrigger.remove();
        }
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function initializeFlowbite() {
    if (typeof initFlowbite === 'function') {
        try {
            initFlowbite();
            console.log('✅ Flowbite re-initialized');
        } catch (error) {
            console.error('❌ Flowbite initialization failed:', error);
        }
    }
}

function cleanupBeforeCache() {
    const openModals = document.querySelectorAll('[data-modal-toggle]');
    openModals.forEach(modal => {
        const event = new Event('hide.flowbite.modal');
        modal.dispatchEvent(event);
    });
}

function showLoadingState(form) {
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        
        const originalText = submitButton.innerHTML;
        submitButton.setAttribute('data-original-text', originalText);
        submitButton.innerHTML = submitButton.getAttribute('data-loading-text') || 'Memproses...';
    }
}

function hideLoadingState(form) {
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitButton && submitButton.hasAttribute('data-original-text')) {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        submitButton.innerHTML = submitButton.getAttribute('data-original-text');
    }
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

// Turbo configuration
Turbo.setProgressBarDelay(500);

document.addEventListener('turbo:click', function(event) {
    const target = event.target;
    if (target.matches('[data-turbo="false"]')) {
        event.preventDefault();
        window.location.href = target.href;
    }
});

export { Turbo, Alpine };
