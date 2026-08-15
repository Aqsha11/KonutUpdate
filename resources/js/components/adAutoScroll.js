export default function (Alpine) {
    Alpine.data('adAutoScroll', () => ({
        timer: null,
        paused: false,

        init() {
            const el = this.$refs.scroller;
            if (!el) return;

            const axis = el.dataset.axis || 'y';
            const max = () => (axis === 'y'
                ? el.scrollHeight - el.clientHeight
                : el.scrollWidth - el.clientWidth);

            el.addEventListener('mouseenter', () => this.paused = true);
            el.addEventListener('mouseleave', () => this.paused = false);
            el.addEventListener('touchstart', () => this.paused = true);
            el.addEventListener('touchend', () => this.paused = false);

            this.timer = setInterval(() => {
                if (this.paused) return;

                const m = max();
                if (m <= 0) return;

                const pos = axis === 'y' ? el.scrollTop : el.scrollLeft;
                const step = axis === 'y' ? el.clientHeight : el.clientWidth;
                let target = pos + step;
                if (target >= m) target = 0;

                el.scrollTo({ [axis === 'y' ? 'top' : 'left']: target, behavior: 'smooth' });
            }, 3000);
        },

        destroy() {
            if (this.timer) clearInterval(this.timer);
        }
    }));
}
