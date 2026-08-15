export default function (Alpine) {
    Alpine.data('navMenuArrows', () => ({
        canLeft: false,
        canRight: false,

        init() {
            this.updateArrows();
            window.addEventListener('resize', () => this.updateArrows());
        },

        updateArrows() {
            const el = this.$refs.navMenu;
            if (!el) return;
            const tolerance = 4;
            this.canLeft = el.scrollLeft > tolerance;
            this.canRight = el.scrollLeft + el.clientWidth < el.scrollWidth - tolerance;
        },

        scrollMenu(delta) {
            this.$refs.navMenu.scrollBy({ left: delta, behavior: 'smooth' });
        }
    }));
}
