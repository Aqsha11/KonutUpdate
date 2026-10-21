const TEASER_KEY = 'ku_hub_teaser_seen';

export default function (Alpine) {
    Alpine.data('floatingHub', () => ({
        menuOpen: false,
        teaser: false,
        teaserTimer: null,

        chatOpen: false,
        chatEnabled: false,
        messages: [],
        input: '',
        loading: false,
        error: '',
        confirmEnd: false,
        authPrompt: false,
        guestRemaining: null,
        isGuest: false,
        endpoint: '',
        csrf: '',
        suggestions: [
            'Apa berita terbaru di Konawe Utara?',
            'Bagaimana cara daftar akun?',
            'Bagaimana cara mengirim opini?',
            'Bagaimana cara menjadi kontributor?',
        ],

        init() {
            this.endpoint = this.$root.dataset.endpoint || '';
            this.csrf = this.$root.dataset.csrf || '';
            this.chatEnabled = this.$root.dataset.chat === '1';
            this.isGuest = this.$root.dataset.guest === '1';

            if (this.chatEnabled && !this.teaserSeen()) {
                this.teaserTimer = setTimeout(() => {
                    if (!this.menuOpen && !this.chatOpen) {
                        this.teaser = true;
                        this.markTeaserSeen();
                    }
                }, 3500);
            }
        },

        teaserSeen() {
            try {
                return localStorage.getItem(TEASER_KEY) === '1';
            } catch (e) {
                return true;
            }
        },

        markTeaserSeen() {
            try {
                localStorage.setItem(TEASER_KEY, '1');
            } catch (e) {
                // localStorage unavailable — teaser will simply not persist.
            }
        },

        dismissTeaser() {
            this.teaser = false;
            this.markTeaserSeen();
            if (this.teaserTimer) {
                clearTimeout(this.teaserTimer);
                this.teaserTimer = null;
            }
        },

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
            if (this.menuOpen) {
                this.teaser = false;
            }
        },

        openChat() {
            this.menuOpen = false;
            this.chatOpen = true;
            this.confirmEnd = false;
            this.dismissTeaser();
            this.$nextTick(() => this.scrollToBottom());
        },

        // Perkecil: sembunyikan panel, percakapan tetap tersimpan.
        minimizeChat() {
            this.chatOpen = false;
            this.confirmEnd = false;
        },

        // Akhiri: kosongkan percakapan (dengan konfirmasi bila ada isi).
        requestEnd() {
            if (this.messages.length === 0) {
                this.endChat();

                return;
            }

            this.confirmEnd = true;
        },

        endChat() {
            this.messages = [];
            this.error = '';
            this.input = '';
            this.confirmEnd = false;
            this.authPrompt = false;
            this.guestRemaining = null;
            this.scrollToBottom();
        },

        logout() {
            const form = document.getElementById('frontend-logout-form');
            if (form) {
                form.submit();
            }
        },

        useSuggestion(text) {
            this.input = text;
            this.send();
        },

        async send() {
            const question = this.input.trim();
            if (!question || this.loading) return;
            if (this.authPrompt) return;

            this.messages.push({ role: 'user', text: question });
            this.input = '';
            this.loading = true;
            this.error = '';
            this.scrollToBottom();

            try {
                const response = await fetch(this.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ question }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        this.error = Object.values(data.errors).flat()[0] || 'Permintaan tidak valid.';
                    } else if (data && data.requires_auth) {
                        this.authPrompt = true;
                        this.error = '';
                    } else {
                        this.error = data.message || 'Terjadi kesalahan. Coba lagi nanti.';
                    }
                } else {
                    this.messages.push({
                        role: 'assistant',
                        text: data.answer || '',
                        sources: data.sources || [],
                    });
                    if (data.guest_remaining !== null && data.guest_remaining !== undefined) {
                        this.guestRemaining = data.guest_remaining;
                    }
                    this.authPrompt = false;
                }
            } catch (e) {
                this.error = 'Gagal terhubung. Periksa koneksi Anda.';
            } finally {
                this.loading = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const body = this.$refs.body;
                if (body) {
                    body.scrollTop = body.scrollHeight;
                }
            });
        },
    }));
}
