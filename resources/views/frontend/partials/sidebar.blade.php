<aside class="sidebar-section space-y-4">
    {{-- Trending --}}
    <div class="bg-surface rounded-xl shadow-sm border border-outline overflow-hidden reveal">
        <div class="flex items-center gap-2 px-4 pt-3 pb-2 border-b border-outline">
            <i data-lucide="flame" class="w-3.5 h-3.5 text-accent"></i>
            <h3 class="font-bold text-xs text-on-surface uppercase tracking-wider">Trending</h3>
        </div>
        <div class="divide-y divide-outline">
            @forelse($trendingPosts as $index => $post)
                <a href="{{ route('posts.show', $post->slug) }}" class="flex items-start gap-2.5 px-4 py-2.5 hover:bg-surface-container-low transition-colors no-underline group">
                    <span class="trending-number mt-0.5 font-extrabold text-lg min-w-[24px]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">{{ $post->title }}</h4>
                        <div class="flex items-center gap-1.5 mt-1 text-[10px] text-on-surface-variant">
                            @if($post->categories->count() > 0)
                                <span class="w-1 h-1 rounded-full bg-on-surface-variant"></span>
                                <span>{{ $post->categories->first()->name }}</span>
                            @elseif($post->category)
                                <span class="w-1 h-1 rounded-full bg-on-surface-variant"></span>
                                <span>{{ $post->category->name }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-xs text-on-surface-variant p-4">Belum ada artikel populer.</p>
            @endforelse
        </div>
    </div>

    {{-- Kategori --}}
    <div class="bg-surface rounded-xl shadow-sm border border-outline overflow-hidden">
        <div class="flex items-center gap-2 px-4 pt-3 pb-2 border-b border-outline">
            <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-primary"></i>
            <h3 class="font-bold text-xs text-on-surface uppercase tracking-wider">Kategori</h3>
        </div>
        <div class="p-3 grid grid-cols-2 gap-1.5">
            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs font-medium text-on-surface hover:bg-primary-light hover:text-primary dark:hover:bg-primary-container transition-colors no-underline group">
                    <span>{{ $cat->name }}</span>
                    @if(isset($cat->all_posts_count) ? $cat->all_posts_count > 0 : $cat->posts_count > 0)
                        <span class="text-[10px] text-on-surface-variant bg-surface-container-low px-1.5 py-0.5 rounded-full group-hover:bg-primary/10 group-hover:text-primary">{{ $cat->all_posts_count ?? $cat->posts_count }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- Kecamatan --}}
    @if(isset($kecamatans) && $kecamatans->count() > 0)
    <div class="bg-surface rounded-xl shadow-sm border border-outline overflow-hidden">
        <div class="flex items-center gap-2 px-4 pt-3 pb-2 border-b border-outline">
            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-accent"></i>
            <h3 class="font-bold text-xs text-on-surface uppercase tracking-wider">Kecamatan</h3>
        </div>
        <div class="p-3 grid grid-cols-2 gap-1.5">
            @foreach($kecamatans as $k)
                <a href="{{ route('kecamatan.show', $k->slug) }}"
                   class="px-2.5 py-2 rounded-lg text-xs font-medium text-on-surface hover:bg-primary-light hover:text-primary dark:hover:bg-primary-container transition-colors no-underline group text-center">
                    <span>{{ $k->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Social Media --}}
    <div class="bg-surface rounded-xl shadow-sm border border-outline overflow-hidden">
        <div class="flex items-center gap-2 px-4 pt-3 pb-2 border-b border-outline">
            <i data-lucide="share-2" class="w-3.5 h-3.5 text-accent"></i>
            <h3 class="font-bold text-xs text-on-surface uppercase tracking-wider">Ikuti Kami</h3>
        </div>
        <div class="p-3 grid grid-cols-2 gap-1.5">
            @if(!empty($site_settings['facebook']))
                <a href="{{ $site_settings['facebook'] }}" target="_blank" class="flex items-center gap-2 px-2.5 py-2 rounded-lg bg-[#1877f2] text-white text-xs font-medium no-underline hover:opacity-90 transition-opacity">
                    <i class="fab fa-facebook text-xs"></i>
                    <span>Facebook</span>
                </a>
            @endif
            @if(!empty($site_settings['instagram']))
                <a href="{{ $site_settings['instagram'] }}" target="_blank" class="flex items-center gap-2 px-2.5 py-2 rounded-lg bg-gradient-to-tr from-[#833ab4] via-[#fd1d1d] to-[#f77737] text-white text-xs font-medium no-underline hover:opacity-90 transition-opacity">
                    <i class="fab fa-instagram text-xs"></i>
                    <span>Instagram</span>
                </a>
            @endif
            @if(!empty($site_settings['youtube']))
                <a href="{{ $site_settings['youtube'] }}" target="_blank" class="flex items-center gap-2 px-2.5 py-2 rounded-lg bg-[#ff0000] text-white text-xs font-medium no-underline hover:opacity-90 transition-opacity">
                    <i class="fab fa-youtube text-xs"></i>
                    <span>YouTube</span>
                </a>
            @endif
            @if(!empty($site_settings['whatsapp']))
                <a href="{{ $site_settings['whatsapp'] }}" target="_blank" class="flex items-center gap-2 px-2.5 py-2 rounded-lg bg-[#25d366] text-white text-xs font-medium no-underline hover:opacity-90 transition-opacity">
                    <i class="fab fa-whatsapp text-xs"></i>
                    <span>WhatsApp</span>
                </a>
            @endif
            @if(!empty($site_settings['email']))
                <a href="mailto:{{ $site_settings['email'] }}" class="flex items-center gap-2 px-2.5 py-2 rounded-lg bg-on-surface text-white text-xs font-medium no-underline hover:opacity-90 transition-opacity">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                    <span>Email</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Iklan Sidebar Atas --}}
    @if(isset($sidebarAdsTop) && $sidebarAdsTop->count() > 0)
        @foreach($sidebarAdsTop as $ad)
        <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="block bg-surface rounded-lg overflow-hidden border border-outline no-underline group">
            <div class="aspect-[2/1] overflow-hidden bg-surface-container-low">
                <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" loading="lazy">
            </div>
            <div class="p-1.5">
                <p class="text-[10px] font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">{{ $ad->title }}</p>
                <p class="text-[8px] text-on-surface-variant mt-0.5">Iklan</p>
            </div>
        </a>
        @endforeach
    @endif

    {{-- Iklan Sidebar Bawah --}}
    @if(isset($sidebarAdsBottom) && $sidebarAdsBottom->count() > 0)
        @foreach($sidebarAdsBottom as $ad)
        <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="block bg-surface rounded-lg overflow-hidden border border-outline no-underline group">
            <div class="aspect-[2/1] overflow-hidden bg-surface-container-low">
                <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" loading="lazy">
            </div>
            <div class="p-1.5">
                <p class="text-[10px] font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">{{ $ad->title }}</p>
                <p class="text-[8px] text-on-surface-variant mt-0.5">Iklan</p>
            </div>
        </a>
        @endforeach
    @endif
</aside>
