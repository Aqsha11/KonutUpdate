@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex sm:hidden w-full">
            @if ($paginator->onFirstPage())
                <span class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-on-surface-variant bg-surface border border-outline rounded-lg cursor-not-allowed">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-on-surface bg-surface border border-outline rounded-lg hover:bg-surface-container-low transition-colors no-underline">
                    Sebelumnya
                </a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-on-surface bg-surface border border-outline rounded-lg hover:bg-surface-container-low transition-colors no-underline ml-2">
                    Selanjutnya
                </a>
            @else
                <span class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-on-surface-variant bg-surface border border-outline rounded-lg cursor-not-allowed ml-2">
                    Selanjutnya
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
            <p class="text-sm text-on-surface-variant">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-semibold text-on-surface">{{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-semibold text-on-surface">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari
                <span class="font-semibold text-on-surface">{{ $paginator->total() }}</span>
                hasil
            </p>

            <div class="flex items-center gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-on-surface-variant bg-surface border border-outline cursor-not-allowed">
                        <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-on-surface bg-surface border border-outline hover:bg-primary-light hover:text-primary hover:border-primary transition-colors no-underline">
                        <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-on-surface-variant bg-surface border border-outline rounded-lg cursor-default">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-primary border border-primary rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-on-surface bg-surface border border-outline rounded-lg hover:bg-primary-light hover:text-primary hover:border-primary transition-colors no-underline" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-on-surface bg-surface border border-outline hover:bg-primary-light hover:text-primary hover:border-primary transition-colors no-underline">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-on-surface-variant bg-surface border border-outline cursor-not-allowed">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
