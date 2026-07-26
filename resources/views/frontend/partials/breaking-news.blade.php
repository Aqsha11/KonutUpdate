@if($breakingNews->count() > 0)
    <div class="bg-gradient-to-r from-primary to-primary-hover text-white border-b border-primary/20">
        <div class="max-w-7xl mx-auto px-4 flex items-center h-6 lg:h-7">
            <span class="bg-accent text-white text-[8px] lg:text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider flex-shrink-0 mr-2 flex items-center gap-1">
                <span class="inline-block w-1 h-1 rounded-full bg-white animate-pulse"></span> Breaking
            </span>
            <div class="breaking-ticker flex-1 overflow-hidden relative">
                <div class="breaking-ticker-track">
                    @foreach($breakingNews as $news)
                        <a href="{{ route('posts.show', $news->slug) }}" class="text-[10px] font-medium text-white/85 hover:text-white no-underline whitespace-nowrap px-2 transition-colors">
                            {{ $news->title }}
                        </a>
                        <span class="text-white/30 font-bold px-0.5">|</span>
                    @endforeach
                    @foreach($breakingNews as $news)
                        <a href="{{ route('posts.show', $news->slug) }}" class="text-[10px] font-medium text-white/85 hover:text-white no-underline whitespace-nowrap px-2 transition-colors">
                            {{ $news->title }}
                        </a>
                        <span class="text-white/30 font-bold px-0.5">|</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
