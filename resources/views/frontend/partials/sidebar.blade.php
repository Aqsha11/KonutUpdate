<aside class="sidebar-section space-y-6">
    {{-- Trending --}}
    <div class="bg-surface rounded-2xl shadow-sm border border-outline overflow-hidden reveal">
        <div class="flex items-center gap-2 px-5 pt-4 pb-3 border-b border-outline">
            <i data-lucide="flame" class="w-4 h-4 text-accent"></i>
            <h3 class="font-bold text-sm text-on-surface uppercase tracking-wider">Trending</h3>
        </div>
        <div class="divide-y divide-outline">
            @forelse($trendingPosts as $index => $post)
                <a href="{{ route('posts.show', $post->slug) }}" class="flex items-start gap-3 px-5 py-3.5 hover:bg-surface-container-low transition-colors no-underline group">
                    <span class="trending-number mt-0.5">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">{{ $post->title }}</h4>
                        <div class="flex items-center gap-2 mt-1.5 text-xs text-on-surface-variant">
                            <span class="flex items-center gap-1">
                                <i data-lucide="eye" class="w-3 h-3"></i>
                                {{ number_format($post->views_count) }}
                            </span>
                            @if($post->category)
                                <span class="w-1 h-1 rounded-full bg-on-surface-variant"></span>
                                <span>{{ $post->category->name }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-sm text-on-surface-variant p-5">Belum ada artikel populer.</p>
            @endforelse
        </div>
    </div>

    {{-- Kategori --}}
    <div class="bg-surface rounded-2xl shadow-sm border border-outline overflow-hidden">
        <div class="flex items-center gap-2 px-5 pt-4 pb-3 border-b border-outline">
            <i data-lucide="layout-grid" class="w-4 h-4 text-primary"></i>
            <h3 class="font-bold text-sm text-on-surface uppercase tracking-wider">Kategori</h3>
        </div>
        <div class="p-4 grid grid-cols-2 gap-2">
            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface hover:bg-primary-light hover:text-primary dark:hover:bg-primary-container transition-colors no-underline group">
                    <span>{{ $cat->name }}</span>
                    @if($cat->posts_count > 0)
                        <span class="text-xs text-on-surface-variant bg-surface-container-low px-1.5 py-0.5 rounded-full group-hover:bg-primary/10 group-hover:text-primary">{{ $cat->posts_count }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
    {{-- Social Media --}}
    <div class="bg-surface rounded-2xl shadow-sm border border-outline overflow-hidden">
        <div class="flex items-center gap-2 px-5 pt-4 pb-3 border-b border-outline">
            <i data-lucide="share-2" class="w-4 h-4 text-accent"></i>
            <h3 class="font-bold text-sm text-on-surface uppercase tracking-wider">Ikuti Kami</h3>
        </div>
        <div class="p-4 grid grid-cols-2 gap-2">
            @if(!empty($site_settings['facebook']))
                <a href="{{ $site_settings['facebook'] }}" target="_blank" class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-[#1877f2] text-white text-sm font-medium no-underline hover:opacity-90 transition-opacity">
                    <i class="fab fa-facebook text-sm"></i>
                    <span>Facebook</span>
                </a>
            @endif
            @if(!empty($site_settings['instagram']))
                <a href="{{ $site_settings['instagram'] }}" target="_blank" class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-gradient-to-tr from-[#833ab4] via-[#fd1d1d] to-[#f77737] text-white text-sm font-medium no-underline hover:opacity-90 transition-opacity">
                    <i class="fab fa-instagram text-sm"></i>
                    <span>Instagram</span>
                </a>
            @endif
            @if(!empty($site_settings['youtube']))
                <a href="{{ $site_settings['youtube'] }}" target="_blank" class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-[#ff0000] text-white text-sm font-medium no-underline hover:opacity-90 transition-opacity">
                    <i class="fab fa-youtube text-sm"></i>
                    <span>YouTube</span>
                </a>
            @endif
            @if(!empty($site_settings['email']))
                <a href="mailto:{{ $site_settings['email'] }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-on-surface text-white text-sm font-medium no-underline hover:opacity-90 transition-opacity">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <span>Email</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Iklan Sidebar Atas --}}
    @if(isset($sidebarAdsTop) && $sidebarAdsTop->count() > 0)
        @foreach($sidebarAdsTop as $ad)
        <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="block bg-surface rounded-2xl overflow-hidden card-hover border border-outline no-underline group">
            <div class="aspect-video overflow-hidden bg-surface-container-low">
                <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" loading="lazy">
            </div>
            <div class="p-3">
                <p class="text-xs font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">{{ $ad->title }}</p>
                <p class="text-[10px] text-on-surface-variant mt-1">Iklan</p>
            </div>
        </a>
        @endforeach
    @else
        <div class="bg-surface-container-low rounded-2xl p-4 text-center border border-outline">
            <p class="text-xs text-on-surface-variant mb-2 uppercase tracking-wider">Iklan</p>
            <div class="bg-surface-container rounded-xl h-[200px] flex items-center justify-center text-on-surface-variant text-sm">
                <span>Space Iklan</span>
            </div>
        </div>
    @endif

    {{-- Iklan Sidebar Bawah --}}
    @if(isset($sidebarAdsBottom) && $sidebarAdsBottom->count() > 0)
        @foreach($sidebarAdsBottom as $ad)
        <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="block bg-surface rounded-2xl overflow-hidden card-hover border border-outline no-underline group">
            <div class="aspect-video overflow-hidden bg-surface-container-low">
                <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" loading="lazy">
            </div>
            <div class="p-3">
                <p class="text-xs font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">{{ $ad->title }}</p>
                <p class="text-[10px] text-on-surface-variant mt-1">Iklan</p>
            </div>
        </a>
        @endforeach
    @endif
</aside>
