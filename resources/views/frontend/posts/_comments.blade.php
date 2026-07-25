@php
    $comments = $post->comments()->approved()->latest()->get();
@endphp

<section class="mt-10 pt-8 border-t border-outline">
    <div class="flex items-center gap-3 mb-6">
        <h3 class="text-xl font-bold text-on-surface">Komentar</h3>
        <span class="bg-surface-container-low text-on-surface-variant text-xs font-semibold px-2.5 py-1 rounded-full">{{ $comments->count() }}</span>
    </div>

    {{-- Comment Form --}}
    <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-8" id="comment-form">
        @csrf
        <div class="bg-surface rounded-2xl p-5 border border-outline">
            <h4 class="text-sm font-semibold text-on-surface mb-4">Tulis Komentar</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="comment-name" class="block text-xs font-medium text-on-surface-variant mb-1.5">Nama</label>
                    <input type="text" id="comment-name" name="name" value="{{ old('name') }}" required
                           maxlength="100" autocomplete="name"
                           class="w-full bg-surface-container-low border border-outline rounded-xl px-4 py-2.5 text-sm text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                           placeholder="Nama Anda">
                    @error('name')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="comment-email" class="block text-xs font-medium text-on-surface-variant mb-1.5">Email</label>
                    <input type="email" id="comment-email" name="email" value="{{ old('email') }}" required
                           maxlength="255" autocomplete="email"
                           class="w-full bg-surface-container-low border border-outline rounded-xl px-4 py-2.5 text-sm text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                           placeholder="email@contoh.com">
                    <p class="text-[11px] text-on-surface-variant/60 mt-1">Email tidak akan ditampilkan.</p>
                    @error('email')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="comment-body" class="block text-xs font-medium text-on-surface-variant mb-1.5">Pesan</label>
                <textarea id="comment-body" name="body" rows="4" required
                          maxlength="2000"
                          class="w-full bg-surface-container-low border border-outline rounded-xl px-4 py-2.5 text-sm text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors resize-none"
                          placeholder="Tulis komentar Anda...">{{ old('body') }}</textarea>
                <div class="flex justify-between items-center mt-1">
                    @error('body')<p class="text-xs text-error">{{ $message }}</p>
                    @else
                    <span></span>
                    @enderror
                    <span id="char-count" class="text-[11px] text-on-surface-variant/60">0/2000</span>
                </div>
            </div>
            <div style="position:absolute;left:-9999px" aria-hidden="true">
                <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off">
            </div>
            <div class="flex items-center justify-between">
                <p class="text-[11px] text-on-surface-variant/60">
                    <i data-lucide="info" class="w-3 h-3 inline"></i>
                    Komentar akan ditampilkan setelah disetujui moderator.
                </p>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors cursor-pointer border-none">
                    Kirim Komentar
                </button>
            </div>
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var textarea = document.getElementById('comment-body');
            var counter = document.getElementById('char-count');
            if (textarea && counter) {
                function updateCount() {
                    counter.textContent = textarea.value.length + '/2000';
                }
                textarea.addEventListener('input', updateCount);
                updateCount();
            }
        });
    </script>

    {{-- Comments List --}}
    @if($comments->count() > 0)
        <div class="space-y-4">
            @foreach($comments as $comment)
                <div class="bg-surface rounded-2xl p-5 border border-outline/50">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center shrink-0">
                            <span class="text-primary font-bold text-sm">{{ strtoupper(substr($comment->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-sm font-semibold text-on-surface">{{ $comment->name }}</span>
                                <span class="text-xs text-on-surface-variant/60">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-on-surface leading-relaxed">{{ $comment->body }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-on-surface-variant">
            <i data-lucide="message-circle" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
            <p class="text-sm">Belum ada komentar. Jadikan yang pertama!</p>
        </div>
    @endif
</section>
