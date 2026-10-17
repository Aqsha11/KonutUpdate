@php
    $chatEnabled = config('services.gemini.enabled') && filled(config('services.gemini.key'));
    $accountRole = auth()->check() ? auth()->user()->role?->slug : null;
    $accountUrl = match ($accountRole) {
        'pembaca' => route('profile.index'),
        'kontributor' => route('profile.index'),
        'super_admin', 'editor', 'reporter' => route('admin.dashboard'),
        default => route('home'),
    };
@endphp

<div class="ku-hub"
     x-data="floatingHub()"
     x-init="init()"
     data-chat="{{ $chatEnabled ? '1' : '0' }}"
     data-guest="{{ auth()->check() ? '0' : '1' }}"
     data-endpoint="{{ route('ai-chat.ask') }}"
     data-csrf="{{ csrf_token() }}"
     x-cloak>

    @if ($chatEnabled)
        {{-- Teaser ajakan (sekali per pengunjung) --}}
        <div class="ku-hub-teaser" x-show="teaser" x-transition.opacity.duration.250ms x-cloak>
            <div class="ku-hub-teaser-text">
                <strong>Tanya AI</strong>
                <span>Butuh info seputar Konawe Utara? Tanya asisten AI kami.</span>
            </div>
            <button type="button" class="ku-hub-teaser-close" x-on:click="dismissTeaser()" aria-label="Tutup">&times;</button>
        </div>
    @endif

    {{-- Menu mengembang --}}
    <div class="ku-hub-menu" x-show="menuOpen" x-transition.opacity.duration.150ms x-cloak>
        @if ($chatEnabled)
            <button type="button" class="ku-hub-item ku-hub-item--ai" x-on:click="openChat()">
                <span class="ku-hub-item-label">Tanya AI</span>
                <span class="ku-hub-item-icon"><i data-lucide="sparkles" class="w-5 h-5"></i></span>
            </button>
        @endif

        @auth
            <a href="{{ $accountUrl }}" class="ku-hub-item">
                <span class="ku-hub-item-label">Profile</span>
                <span class="ku-hub-item-icon"><i data-lucide="user-round" class="w-5 h-5"></i></span>
            </a>
            @if (in_array(auth()->user()->role?->slug, ['pembaca', 'kontributor']))
                <a href="{{ route('opini.submit.create') }}" class="ku-hub-item">
                    <span class="ku-hub-item-label">Kirim Opini</span>
                    <span class="ku-hub-item-icon"><i data-lucide="pen-line" class="w-5 h-5"></i></span>
                </a>
            @endif
            <button type="button" class="ku-hub-item ku-hub-item--danger" x-on:click="logout()">
                <span class="ku-hub-item-label">Keluar</span>
                <span class="ku-hub-item-icon"><i data-lucide="log-out" class="w-5 h-5"></i></span>
            </button>
        @else
            <a href="{{ route('login') }}" class="ku-hub-item">
                <span class="ku-hub-item-label">Masuk / Daftar</span>
                <span class="ku-hub-item-icon"><i data-lucide="user-round" class="w-5 h-5"></i></span>
            </a>
        @endauth
    </div>

    {{-- Tombol utama --}}
    <button type="button"
            class="ku-hub-fab"
            x-on:click="toggleMenu()"
            :aria-expanded="menuOpen ? 'true' : 'false'"
            aria-label="Menu bantuan">
        <i data-lucide="{{ $chatEnabled ? 'sparkles' : 'user-round' }}" class="w-6 h-6" x-show="!menuOpen"></i>
        <i data-lucide="x" class="w-6 h-6" x-show="menuOpen"></i>
        <span class="ku-hub-fab-badge" x-show="!chatOpen && messages.length > 0" x-cloak></span>
    </button>

    @auth
        <form id="frontend-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    @endauth

    @if ($chatEnabled)
        {{-- Panel Chat AI --}}
        <div id="ku-chat-panel"
             class="ku-chat-panel"
             x-show="chatOpen"
             x-transition.opacity.duration.200ms
             role="dialog"
             aria-label="Tanya Berita">
            <div class="ku-chat-header">
                <div class="ku-chat-header-text">
                    <span class="ku-chat-title">Tanya Berita</span>
                    <span class="ku-chat-subtitle">Asisten AI seputar berita Konut</span>
                </div>
                <button type="button" class="ku-chat-iconbtn" x-on:click="minimizeChat()" aria-label="Perkecil" title="Perkecil (percakapan disimpan)">
                    <i data-lucide="chevron-down" class="w-5 h-5"></i>
                </button>
                <button type="button" class="ku-chat-iconbtn" x-on:click="requestEnd()" aria-label="Akhiri percakapan" title="Akhiri percakapan">
                    <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="ku-chat-confirm" x-show="confirmEnd" x-cloak>
                <span>Hapus riwayat percakapan dan mulai topik baru?</span>
                <div class="ku-chat-confirm-actions">
                    <button type="button" class="ku-chat-confirm-yes" x-on:click="endChat()">Ya, akhiri</button>
                    <button type="button" class="ku-chat-confirm-no" x-on:click="confirmEnd = false">Batal</button>
                </div>
            </div>

            <div class="ku-chat-body" x-ref="body">
                <template x-if="messages.length === 0">
                    <div class="ku-chat-intro">
                        <p>Halo! Tanya apa saja seputar berita Konawe Utara. Contoh:</p>
                        <div class="ku-chat-suggestions">
                            <template x-for="suggestion in suggestions" :key="suggestion">
                                <button type="button" class="ku-chat-chip" x-on:click="useSuggestion(suggestion)" x-text="suggestion"></button>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-for="(message, i) in messages" :key="i">
                    <div class="ku-chat-row" :class="message.role === 'user' ? 'is-user' : 'is-bot'">
                        <div class="ku-chat-bubble" x-text="message.text"></div>
                        <template x-if="message.sources && message.sources.length">
                            <div class="ku-chat-sources">
                                <span class="ku-chat-sources-title">Sumber:</span>
                                <template x-for="(source, j) in message.sources" :key="j">
                                    <a class="ku-chat-source" :href="source.url" x-text="source.title"></a>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="ku-chat-row is-bot" x-show="loading" x-cloak>
                    <div class="ku-chat-bubble ku-chat-typing" aria-label="Sedang menjawab">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                <div class="ku-chat-error" x-show="error" x-cloak x-text="error"></div>

                <div class="ku-chat-row is-bot" x-show="authPrompt" x-cloak>
                    <div class="ku-chat-bubble">
                        <p class="text-sm mb-3">Batas pertanyaan gratis Anda sudah habis. Buat akun atau masuk untuk melanjutkan Tanya Berita.</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold no-underline hover:bg-primary-hover">Masuk</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-outline bg-surface text-on-surface text-xs font-semibold no-underline hover:bg-surface-container">Daftar</a>
                        </div>
                    </div>
                </div>
            </div>

            <form class="ku-chat-form" x-on:submit.prevent="send()">
                <textarea class="ku-chat-input"
                          x-model="input"
                          rows="1"
                          maxlength="300"
                          x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"
                          placeholder="Tulis pertanyaan…"
                          aria-label="Pertanyaan"></textarea>
                <button type="submit" class="ku-chat-send" :disabled="loading || !input.trim() || authPrompt" aria-label="Kirim">
                    <i data-lucide="send" class="w-5 h-5"></i>
                </button>
            </form>

            <p class="ku-chat-disclaimer">
                Jawaban dibuat AI dari berita Konut Update. Selalu cek tautan sumbernya.
                <span x-show="isGuest && guestRemaining !== null && guestRemaining > 0" x-cloak>
                    &nbsp;Sisa pertanyaan gratis: <span x-text="guestRemaining"></span>.
                </span>
            </p>
        </div>
    @endif
</div>
