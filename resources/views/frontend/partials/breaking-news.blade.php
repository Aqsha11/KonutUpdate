@if($breakingNews->count() > 0)
    <div class="bg-primary text-white/90 border-b border-primary-hover">
        <div class="max-w-7xl mx-auto px-4 flex items-center h-9 lg:h-10">
            <span class="bg-accent text-white text-[10px] lg:text-[11px] font-bold px-2.5 py-1 rounded-sm uppercase tracking-wider flex-shrink-0 mr-3 flex items-center gap-1.5">
                <i data-lucide="zap" class="w-3 h-3"></i> Breaking
            </span>
            <div class="breaking-ticker flex-1 overflow-hidden relative">
                <div class="breaking-ticker-track">
                    @foreach($breakingNews as $news)
                        <a href="{{ route('posts.show', $news->slug) }}" class="text-sm font-medium text-white/90 hover:text-white no-underline whitespace-nowrap px-4 transition-colors">
                            {{ $news->title }}
                        </a>
                        <span class="text-accent/60 font-bold px-2">●</span>
                    @endforeach
                    @foreach($breakingNews as $news)
                        <a href="{{ route('posts.show', $news->slug) }}" class="text-sm font-medium text-white/90 hover:text-white no-underline whitespace-nowrap px-4 transition-colors">
                            {{ $news->title }}
                        </a>
                        <span class="text-accent/60 font-bold px-2">●</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
