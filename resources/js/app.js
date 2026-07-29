import './bootstrap';

import Swiper from 'swiper/bundle';
window.Swiper = Swiper;

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

// ===== Live Search =====
(function () {
    const input = document.getElementById('liveSearchInput');
    const results = document.getElementById('liveSearchResults');
    if (!input || !results) return;

    let debounceTimer;
    let selectedIndex = -1;

    function fetchResults(query) {
        selectedIndex = -1;
        results.innerHTML = '<div class="live-search-loading"><i data-lucide="loader-circle" class="w-4 h-4 animate-spin inline-block"></i> Mencari...</div>';
        results.classList.add('active');
        lucide.createIcons?.();

        fetch('/search?q=' + encodeURIComponent(query) + '&ajax=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(r => r.json())
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    results.innerHTML = '<div class="live-search-empty">Tidak ditemukan</div>';
                    return;
                }
                results.innerHTML = data.map((item, i) =>
                    '<a href="' + item.url + '" class="live-search-item" data-index="' + i +
                    '" onmouseenter="this.parentElement.querySelectorAll(\'.live-search-item\').forEach(el => el.classList.remove(\'highlighted\')); this.classList.add(\'highlighted\'); window._liveIdx = ' + i + '">' +
                    (item.thumb ? '<img src="' + item.thumb + '" alt="" loading="lazy">' : '<div class="live-search-noimg"><i data-lucide="file-text" class="w-4 h-4"></i></div>') +
                    '<div><div class="result-title">' + escapeHtml(item.title) + '</div>' +
                    '<div class="result-meta">' + (item.category ? '<span>' + escapeHtml(item.category) + '</span>' : '') + (item.date ? ' &middot; ' + item.date : '') + '</div></div></a>'
                ).join('');
                lucide.createIcons?.();
            })
            .catch(() => {
                results.innerHTML = '<div class="live-search-empty">Gagal memuat hasil</div>';
            });
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(debounceTimer);
        if (q.length < 2) {
            results.classList.remove('active');
            results.innerHTML = '';
            return;
        }
        debounceTimer = setTimeout(() => fetchResults(q), 300);
    });

    input.addEventListener('keydown', function (e) {
        const items = results.querySelectorAll('.live-search-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && items[selectedIndex]) {
                items[selectedIndex].click();
            } else if (this.value.trim().length >= 2) {
                window.location.href = '/search?q=' + encodeURIComponent(this.value.trim());
            }
            return;
        }

        items.forEach((el, i) => {
            el.classList.toggle('highlighted', i === selectedIndex);
        });
        if (selectedIndex >= 0 && items[selectedIndex]) {
            items[selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    });

    // Close on escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && results.classList.contains('active')) {
            const alpineData = document.querySelector('[x-data]')?.__x;
            if (alpineData) alpineData.$data.searchOpen = false;
        }
    });
})();
