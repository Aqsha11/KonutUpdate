@php
    $comments = $post->comments()->approved()->latest()->get();
@endphp

<section class="mt-6 pt-4 border-t border-outline">
    <div class="flex items-center gap-2 mb-3">
        <h3 class="text-sm font-bold text-on-surface">Komentar</h3>
        <span class="bg-surface-container-low text-on-surface-variant text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ $comments->count() }}</span>
    </div>

    {{-- Comment Form --}}
    <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-4" id="comment-form">
        @csrf
        <div class="bg-surface-container-low rounded-lg p-3 border border-outline">
            <h4 class="text-xs font-semibold text-on-surface mb-2">Tulis Komentar</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name"
                       class="comment-input" placeholder="Nama">
                <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                       class="comment-input" placeholder="Email">
            </div>
            <textarea name="body" rows="2" required maxlength="2000"
                      class="comment-input w-full resize-none mb-2"
                      placeholder="Tulis komentar Anda...">{{ old('body') }}</textarea>
            <div style="position:absolute;left:-9999px" aria-hidden="true">
                <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off">
            </div>
            <div class="flex items-center justify-between">
                <p class="text-[9px] text-on-surface-variant/60"><i data-lucide="info" class="w-2.5 h-2.5 inline"></i> Komentar ditampilkan setelah disetujui.</p>
                <button type="submit" class="btn-primary-sm">Kirim</button>
            </div>
        </div>
    </form>

    {{-- Comments List --}}
    @if($comments->count() > 0)
        <div class="space-y-2">
            @foreach($comments as $comment)
                <div class="flex items-start gap-2.5 py-2.5 border-b border-outline/50 last:border-b-0">
                    <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="text-primary font-bold text-[10px]">{{ strtoupper(substr($comment->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs font-semibold text-on-surface">{{ $comment->name }}</span>
                            <span class="text-[10px] text-on-surface-variant/60">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-on-surface leading-relaxed">{{ $comment->body }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-4 text-on-surface-variant">
            <i data-lucide="message-circle" class="w-6 h-6 mx-auto mb-1 opacity-40"></i>
            <p class="text-[11px]">Belum ada komentar.</p>
        </div>
    @endif
</section>
