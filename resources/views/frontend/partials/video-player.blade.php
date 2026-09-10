{{-- Video Player Modal — buka & putar video langsung tanpa redirect ke halaman detail / TikTok --}}
<div id="kuVideoPlayer" class="ku-video-player" hidden>
    <div class="ku-video-player-backdrop" data-video-close></div>
    <div class="ku-video-player-panel" role="dialog" aria-modal="true" aria-labelledby="kuVideoPlayerTitle">
        <div class="ku-video-player-header">
            <h3 id="kuVideoPlayerTitle" class="ku-video-player-title">Video</h3>
            <a id="kuVideoPlayerDetail" href="#" class="ku-video-player-detail-link">Detail</a>
            <button type="button" class="ku-video-player-close" data-video-close aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="ku-video-player-body">
            <div id="kuVideoEmbedWrap" class="ku-video-embed ku-video-embed-wide" hidden></div>
            <div id="kuVideoDirectWrap" class="ku-video-direct" hidden></div>
            <div id="kuVideoFallback" class="ku-video-fallback" hidden>
                <i class="bi bi-tiktok" style="font-size:2rem;color:#9ca3af;"></i>
                <p class="mt-2 font-semibold">Pemutar video tidak dapat dimuat</p>
                <p class="text-sm text-gray-500 mb-3">Gunakan link lengkap berformat tiktok.com/@user/video/ID agar video bisa diputar.</p>
                <a id="kuVideoFallbackLink" href="#" target="_blank" rel="noopener" class="btn-admin btn-admin-primary"><i class="bi bi-box-arrow-up-right"></i> Buka di TikTok</a>
            </div>
        </div>
        <div class="ku-video-player-footer">
            <a id="kuVideoSource" href="#" target="_blank" rel="noopener" class="ku-video-player-source"><i class="bi bi-box-arrow-up-right"></i> Buka di Sumber</a>
        </div>
    </div>
</div>

<script>
(function() {
    var container = document.getElementById('kuVideoPlayer');
    if (!container) return;

    var embedWrap = document.getElementById('kuVideoEmbedWrap');
    var directWrap = document.getElementById('kuVideoDirectWrap');
    var fallback = document.getElementById('kuVideoFallback');
    var fallbackLink = document.getElementById('kuVideoFallbackLink');
    var sourceLink = document.getElementById('kuVideoSource');
    var titleEl = document.getElementById('kuVideoPlayerTitle');
    var detailLink = document.getElementById('kuVideoPlayerDetail');

    function open(data) {
        if (!data) return;

        titleEl.textContent = data.title || 'Video';
        embedWrap.hidden = true;
        directWrap.hidden = true;
        fallback.hidden = true;
        embedWrap.innerHTML = '';
        directWrap.innerHTML = '';
        sourceLink.hidden = !data.url;
        detailLink.href = data.slug ? '/berita/' + encodeURIComponent(data.slug) : '#';

        if (data.embed) {
            var frame = document.createElement('iframe');
            frame.setAttribute('src', data.embed);
            frame.setAttribute('frameborder', '0');
            frame.setAttribute('allowfullscreen', '');
            frame.setAttribute('allow', 'autoplay; encrypted-media;');
            if (data.tiktok) {
                frame.setAttribute('sandbox', 'allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-top-navigation');
            }
            frame.className = 'ku-video-embed-frame';
            embedWrap.appendChild(frame);
            embedWrap.className = data.tiktok
                ? 'ku-video-embed ku-video-embed-tiktok'
                : 'ku-video-embed ku-video-embed-wide';
            embedWrap.hidden = false;
        } else if (data.url) {
            var video = document.createElement('video');
            video.setAttribute('controls', '');
            video.setAttribute('playsinline', '');
            video.setAttribute('autoplay', '');
            video.setAttribute('preload', 'metadata');
            if (data.poster) video.setAttribute('poster', data.poster);
            var source = document.createElement('source');
            source.setAttribute('src', data.url);
            video.appendChild(source);
            directWrap.appendChild(video);
            directWrap.hidden = false;
        } else {
            fallback.hidden = false;
        }

        if (data.url) {
            fallbackLink.setAttribute('href', data.url);
            sourceLink.setAttribute('href', data.url);
        }

        container.hidden = false;
        requestAnimationFrame(function() {
            container.classList.add('is-open');
        });
        document.body.classList.add('overflow-hidden');
    }

    function close() {
        container.classList.remove('is-open');
        embedWrap.innerHTML = '';
        directWrap.innerHTML = '';
        setTimeout(function() {
            container.hidden = true;
        }, 220);
        document.body.classList.remove('overflow-hidden');
    }

    container.addEventListener('click', function(e) {
        if (e.target.closest('[data-video-close]')) {
            e.preventDefault();
            close();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !container.hidden) close();
    });

    // Klik thumbnail video (data-video-player) di mana pun di halaman → buka modal langsung
    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('[data-video-player]');
        if (!trigger) return;
        e.preventDefault();
        try {
            open(JSON.parse(trigger.getAttribute('data-video-player') || '{}'));
        } catch (err) {}
    });

    window.KuVideoPlayer = { open: open, close: close };
})();
</script>