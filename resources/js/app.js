import './bootstrap';

import Alpine from 'alpinejs';

import scrollToTop from './components/scrollToTop';
scrollToTop(Alpine);

import '@fortawesome/fontawesome-free/css/fontawesome.css';
import '@fortawesome/fontawesome-free/css/brands.css';

Alpine.start();

import { createIcons, icons } from 'lucide';

window.__lucideCreateIcons = createIcons;
window.__lucideIcons = icons;

function initLucide() {
    try {
        createIcons({ icons });
    } catch (e) {}
}

initLucide();
document.addEventListener('DOMContentLoaded', initLucide);
document.addEventListener('livewire:navigated', initLucide);

// ===== Toast Notification System =====
window.showToast = function(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = {
        success: '✓',
        error: '✕',
        warning: '!',
        info: 'i'
    };

    const titles = {
        success: 'Berhasil',
        error: 'Gagal',
        warning: 'Peringatan',
        info: 'Informasi'
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-icon">${icons[type] || 'i'}</div>
        <div class="toast-body">
            <div class="toast-title">${titles[type] || 'Info'}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.closest('.toast').remove()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));

    if (duration > 0) {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 350);
        }, duration);
    }
};

// ===== Scroll Reveal =====
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -60px 0px', threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});

// ===== Reading Progress Bar (article pages only) =====
document.addEventListener('DOMContentLoaded', () => {
    const progressBar = document.getElementById('readingProgressBar');
    if (!progressBar) return;

    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
        progressBar.style.width = progress + '%';
    }, { passive: true });
});

// ===== Infinite Scroll (improved) =====
document.addEventListener('DOMContentLoaded', function () {
    const sentinel = document.getElementById('infinite-scroll-sentinel');
    if (!sentinel) return;

    let page = 2;
    let loading = false;
    let hasMore = true;

    const observer = new IntersectionObserver(async (entries) => {
        const entry = entries[0];
        if (!entry.isIntersecting || loading || !hasMore) return;

        loading = true;
        sentinel.innerHTML = '<div class="flex justify-center py-6"><div class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin"></div></div>';

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);

            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                hasMore = false;
                sentinel.remove();
                return;
            }

            const html = await response.text();
            const container = document.getElementById('posts-container');
            const temp = document.createElement('div');
            temp.innerHTML = html;

            const posts = temp.querySelectorAll('[data-post-item]');
            if (posts.length === 0) {
                hasMore = false;
                sentinel.remove();
                return;
            }

            posts.forEach(post => {
                post.classList.add('reveal');
                container.appendChild(post.cloneNode(true));
            });

            page++;
            initLucide();

            // Trigger reveal for new posts
            setTimeout(() => {
                document.querySelectorAll('.reveal:not(.revealed)').forEach(el => {
                    el.classList.add('revealed');
                });
            }, 100);
        } catch {
            hasMore = false;
            sentinel.remove();
        }

        loading = false;
        if (sentinel) sentinel.innerHTML = '';
    }, { rootMargin: '200px' });

    observer.observe(sentinel);
});

// ===== Keyboard Shortcut Toast Hint =====
document.addEventListener('keydown', (e) => {
    if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
        e.preventDefault();
        const searchBtn = document.querySelector('[x-on\\:click*="searchOpen = true"]');
        if (searchBtn) searchBtn.click();
    }
});
