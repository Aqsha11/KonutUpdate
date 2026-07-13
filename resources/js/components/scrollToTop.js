export default function (Alpine) {
    Alpine.data('scrollToTop', () => ({
        showScroll: false,
        scrollTimer: null,

        init() {
            this.handleScroll();
            window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
        },

        handleScroll() {
            if (this.scrollTimer) cancelAnimationFrame(this.scrollTimer);
            this.scrollTimer = requestAnimationFrame(() => {
                this.showScroll = window.scrollY > 300;
            });
        },

        scrollTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }));
}
