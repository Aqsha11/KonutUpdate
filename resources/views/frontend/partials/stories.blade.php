@if(isset($stories) && $stories->isNotEmpty())
<section class="stories-strip" x-data="storyViewer()" x-init="init()" aria-label="Story">
    <div class="stories-track" x-ref="track">
        @foreach($stories as $story)
            <button type="button"
                    class="story-ring"
                    data-story
                    data-image="{{ $story->image_url }}"
                    data-title="{{ $story->display_title }}"
                    data-url="{{ $story->url }}"
                    x-on:click="openAt({{ $loop->index }})"
                    aria-label="Buka story: {{ $story->display_title }}">
                <span class="story-ring-avatar">
                    <img src="{{ $story->image_url }}" alt="{{ $story->display_title }}" loading="lazy">
                </span>
                <span class="story-ring-label">{{ $story->display_title }}</span>
            </button>
        @endforeach
    </div>

    {{-- Viewer fullscreen ala Instagram Story --}}
    <div class="story-viewer"
         x-show="open"
         x-cloak
         x-transition.opacity
         x-on:keydown.escape.window="close()"
         x-on:keydown.arrow-right.window="next()"
         x-on:keydown.arrow-left.window="prev()"
         role="dialog"
         aria-modal="true"
         aria-label="Story viewer">
        <div class="story-viewer-backdrop" x-on:click="close()"></div>

        <div class="story-viewer-stage">
            <div class="story-viewer-progress">
                <template x-for="(s, i) in stories" :key="i">
                    <span class="story-viewer-bar">
                        <span class="story-viewer-bar-fill"
                              :style="i < index ? 'width:100%' : (i === index ? 'width:' + progress + '%' : 'width:0')"></span>
                    </span>
                </template>
            </div>

            <div class="story-viewer-header">
                <span class="story-viewer-title" x-text="current.title"></span>
                <button type="button" class="story-viewer-close" x-on:click="close()" aria-label="Tutup">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <img class="story-viewer-image"
                 :src="current.image"
                 :alt="current.title"
                 x-on:pointerdown="pause()"
                 x-on:pointerup="resume()"
                 x-on:pointerleave="resume()"
                 draggable="false">

            <button type="button" class="story-viewer-nav story-viewer-nav-prev" x-on:click="prev()" aria-label="Story sebelumnya"></button>
            <button type="button" class="story-viewer-nav story-viewer-nav-next" x-on:click="next()" aria-label="Story berikutnya"></button>

            <a class="story-viewer-cta"
               x-show="current.url"
               x-cloak
               :href="current.url || '#'"
               target="_blank"
               rel="noopener"
               x-text="'Baca Selengkapnya'"></a>
        </div>
    </div>
</section>
@endif
