export default function (Alpine) {
    Alpine.data('bottomNav', () => ({
        active: 'home',
        pillX: 0,
        pillWidth: 0,
        pillStyle: '',

        init() {
            this.setActiveFromUrl();
            this.$nextTick(() => {
                this.updatePill();
            });
            window.addEventListener('resize', () => this.updatePill(), { passive: true });
        },

        setActiveFromUrl() {
            const path = window.location.pathname;
            if (path === '/' || path === '/home') {
                this.active = 'home';
                return;
            }
            if (path.startsWith('/terkini')) {
                this.active = 'terkini';
                return;
            }
            if (path.startsWith('/trending')) {
                this.active = 'trending';
                return;
            }
            if (this.$data.searchOpen) {
                this.active = 'search';
                return;
            }
            this.active = 'home';
        },

        updatePill() {
            const nav = this.$refs.navBar;
            if (!nav) return;
            const item = nav.querySelector('.mobile-dock-item.active');
            if (!item) {
                this.pillStyle = 'opacity: 0;';
                return;
            }
            const navRect = nav.getBoundingClientRect();
            const itemRect = item.getBoundingClientRect();
            const track = nav.querySelector('.mobile-dock-track');
            const trackRect = track.getBoundingClientRect();
            const x = itemRect.left - trackRect.left + track.scrollLeft;
            const width = itemRect.width;
            this.pillX = x;
            this.pillWidth = width;
            this.pillStyle = `transform: translateX(${this.pillX}px); width: ${this.pillWidth}px; opacity: 1;`;
        },

        goHome() {
            this.active = 'home';
            this.updatePill();
            window.location.href = '/';
        },

        openSearch() {
            this.active = 'search';
            this.updatePill();
            this.$data.searchOpen = true;
            this.$nextTick(() => {
                const input = document.getElementById('liveSearchInput');
                if (input) input.focus();
            });
        },

        openMenu() {
            this.active = 'menu';
            this.updatePill();
            this.$data.mobileOpen = true;
        }
    }));
}
