export default function (Alpine) {
    Alpine.data('storyViewer', () => ({
        stories: [],
        open: false,
        index: 0,
        progress: 0,
        duration: 6000,
        raf: null,
        startAt: 0,
        paused: false,
        pausedAt: 0,

        init() {
            const nodes = this.$root.querySelectorAll('[data-story]');
            this.stories = Array.from(nodes).map((node) => ({
                image: node.dataset.image || '',
                title: node.dataset.title || '',
                url: node.dataset.url || '',
            }));
        },

        get current() {
            return this.stories[this.index] || { image: '', title: '', url: '' };
        },

        openAt(i) {
            if (!this.stories.length) return;
            this.index = Number(i) || 0;
            this.open = true;
            document.body.style.overflow = 'hidden';
            this.play();
        },

        close() {
            this.open = false;
            this.stop();
            this.progress = 0;
            this.paused = false;
            document.body.style.overflow = '';
        },

        next() {
            if (this.index < this.stories.length - 1) {
                this.index++;
                this.play();
            } else {
                this.close();
            }
        },

        prev() {
            if (this.index > 0) {
                this.index--;
            }
            this.play();
        },

        play() {
            this.stop();
            this.progress = 0;
            this.paused = false;
            this.startAt = performance.now();
            this.runFrame();
        },

        runFrame() {
            this.raf = requestAnimationFrame((now) => {
                if (this.paused) return;
                const pct = ((now - this.startAt) / this.duration) * 100;
                if (pct >= 100) {
                    this.progress = 100;
                    this.next();
                    return;
                }
                this.progress = pct;
                this.runFrame();
            });
        },

        pause() {
            if (!this.open || this.paused) return;
            this.paused = true;
            this.pausedAt = performance.now();
            if (this.raf) cancelAnimationFrame(this.raf);
            this.raf = null;
        },

        resume() {
            if (!this.open || !this.paused) return;
            this.startAt += performance.now() - this.pausedAt;
            this.paused = false;
            this.runFrame();
        },

        stop() {
            if (this.raf) cancelAnimationFrame(this.raf);
            this.raf = null;
        },
    }));
}
