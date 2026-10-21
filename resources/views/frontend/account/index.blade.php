@extends('frontend.layouts.app')

@section('title', 'Akun Saya - ' . ($site_settings['site_name'] ?? 'KonutUpdate'))

@section('seo_robots', 'noindex, nofollow')

@php
    $openOpini = $errors->has('title') || $errors->has('body') || request()->boolean('opini');
    $initialTab = ($errors->any() && ! $openOpini) || request('tab') === 'edit' ? 'edit' : 'posts';
@endphp

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Akun Saya</span>
        </nav>
    </div>

    <div class="max-w-3xl mx-auto" x-data="{ tab: '{{ $initialTab }}', opini: {{ $openOpini ? 'true' : 'false' }} }">
        {{-- Kepala profil ala Instagram --}}
        <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 md:gap-8">
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden bg-surface-container border-2 border-primary/20 shrink-0 flex items-center justify-center mx-auto sm:mx-0">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="user-round" class="w-9 h-9 md:w-10 md:h-10 text-on-surface-variant"></i>
                    @endif
                </div>

                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <h1 class="text-lg md:text-xl font-bold text-on-surface truncate">{{ $user->name }}</h1>
                    <p class="text-sm text-on-surface-variant truncate">{{ $user->email }}</p>

                    <div class="flex items-center justify-center sm:justify-start gap-6 mt-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-on-surface">{{ $stats['published'] }}</div>
                            <div class="text-xs text-on-surface-variant">Terbit</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-on-surface">{{ $stats['pending'] }}</div>
                            <div class="text-xs text-on-surface-variant">Verifikasi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-on-surface">{{ $stats['rejected'] }}</div>
                            <div class="text-xs text-on-surface-variant">Ditolak</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-6">
                <button type="button" x-on:click="opini = true"
                        class="px-4 py-2 rounded-full bg-primary text-white text-sm font-medium hover:bg-primary-hover transition-colors">
                    <i data-lucide="pen-line" class="w-4 h-4 inline mr-1.5"></i> Kirim Opini
                </button>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-full border border-red-500 text-red-600 text-sm font-medium hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4 inline mr-1.5"></i> Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        @if (session('success'))
            <div class="mt-5 px-4 py-3 rounded-xl bg-primary/10 text-primary text-sm">{{ session('success') }}</div>
        @endif

        {{-- Tab --}}
        <div class="mt-6">
            <div class="flex items-center gap-1 border-b border-outline">
                <button type="button" x-on:click="tab = 'posts'"
                        :class="tab === 'posts' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent'"
                        class="px-4 py-3 text-sm font-semibold border-b-2 -mb-px transition-colors inline-flex items-center gap-2">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i> Kiriman Saya
                </button>
                <button type="button" x-on:click="tab = 'edit'"
                        :class="tab === 'edit' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent'"
                        class="px-4 py-3 text-sm font-semibold border-b-2 -mb-px transition-colors inline-flex items-center gap-2">
                    <i data-lucide="settings" class="w-4 h-4"></i> Edit Profil
                </button>
            </div>
        </div>

        {{-- Tab: Kiriman Saya --}}
        <div x-show="tab === 'posts'" x-cloak class="mt-5">
            <div class="flex flex-wrap gap-2 mb-4">
                @php
                    $filters = [
                        'all' => 'Semua',
                        'published' => 'Terbit',
                        'pending' => 'Verifikasi',
                        'rejected' => 'Ditolak',
                    ];
                @endphp
                @foreach ($filters as $key => $label)
                    <a href="{{ route('profile.index', array_filter(['filter' => $key === 'all' ? null : $key, 'tab' => 'posts'])) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-semibold no-underline border transition-colors {{ $filter === $key ? 'bg-primary text-white border-primary' : 'bg-surface-container text-on-surface border-outline hover:bg-surface-container/70' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($posts->count())
                <div class="space-y-3">
                    @foreach ($posts as $post)
                        @php
                            $statusMap = [
                                'published' => ['Terbit', 'bg-green-100 text-green-700'],
                                'pending' => ['Menunggu Verifikasi', 'bg-amber-100 text-amber-700'],
                                'rejected' => ['Ditolak', 'bg-red-100 text-red-700'],
                                'draft' => ['Draft', 'bg-slate-100 text-slate-600'],
                            ];
                            [$statusLabel, $statusClass] = $statusMap[$post->status] ?? ['-', 'bg-slate-100 text-slate-600'];
                        @endphp
                        <div class="flex gap-4 bg-surface rounded-xl border border-outline p-3 md:p-4">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-lg overflow-hidden bg-surface-container shrink-0 flex items-center justify-center">
                                @if ($post->thumbnail)
                                    <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="{{ $post->type === 'opini' ? 'pen-line' : 'newspaper' }}" class="w-6 h-6 text-on-surface-variant"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $post->type === 'opini' ? 'bg-primary/10 text-primary' : 'bg-accent/10 text-accent' }}">
                                        {{ $post->type === 'opini' ? 'Opini' : 'Berita' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                                <h3 class="mt-1.5 text-sm md:text-base font-semibold text-on-surface line-clamp-2">
                                    @if ($post->status === 'published')
                                        <a href="{{ route('posts.show', $post->slug) }}" class="no-underline text-on-surface hover:text-primary">{{ $post->title }}</a>
                                    @else
                                        {{ $post->title }}
                                    @endif
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-1">
                                    {{ $post->published_at?->translatedFormat('d M Y') ?? $post->created_at->translatedFormat('d M Y') }}
                                    @if ($post->categories->isNotEmpty())
                                        &middot; {{ $post->categories->pluck('name')->take(2)->join(', ') }}
                                    @endif
                                </p>
                                @if ($post->status === 'rejected' && $post->rejection_reason)
                                    <p class="mt-2 text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                                        <span class="font-semibold">Alasan ditolak:</span> {{ $post->rejection_reason }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="bg-surface rounded-xl border border-outline p-8 text-center">
                    <i data-lucide="inbox" class="w-8 h-8 text-on-surface-variant mx-auto mb-3"></i>
                    <p class="text-sm text-on-surface-variant">Belum ada kiriman.</p>
                    <button type="button" x-on:click="opini = true"
                            class="mt-4 px-4 py-2 rounded-full bg-primary text-white text-sm font-medium hover:bg-primary-hover transition-colors">
                        <i data-lucide="pen-line" class="w-4 h-4 inline mr-1.5"></i> Kirim Opini
                    </button>
                </div>
            @endif
        </div>

        {{-- Tab: Edit Profil --}}
        <div x-show="tab === 'edit'" x-cloak class="mt-5">
            <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
                @if ($errors->any() && ! $openOpini)
                    <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 text-red-600 text-sm border border-red-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-surface-container border border-outline shrink-0 flex items-center justify-center">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="user-round" class="w-7 h-7 text-on-surface-variant"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="avatar" class="block text-sm font-medium text-on-surface mb-1.5">Foto Profil</label>
                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                   class="w-full text-sm text-on-surface-variant file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                            <p class="text-xs text-on-surface-variant mt-1">Format JPG/PNG/WebP, maksimal 2 MB.</p>
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-on-surface mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-on-surface mb-1.5">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>

                    <div class="pt-4 mt-4 border-t border-outline">
                        <p class="text-sm font-semibold text-on-surface mb-3">Ganti Kata Sandi <span class="font-normal text-on-surface-variant">(opsional)</span></p>
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-on-surface mb-1.5">Kata Sandi Saat Ini</label>
                                <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                                       class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            </div>
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-on-surface mb-1.5">Kata Sandi Baru</label>
                                <input type="password" name="new_password" id="new_password" autocomplete="new-password"
                                       class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            </div>
                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-on-surface mb-1.5">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" autocomplete="new-password"
                                       class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition-colors">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Kirim Opini --}}
        <div x-show="opini" x-cloak
             x-effect="if (opini) { window.initCKEditor && window.initCKEditor('editor', 'Tulis isi opini di sini...', '{{ route('opini.submit.upload-image') }}') }"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm">
            <div class="min-h-screen px-4 py-6 text-center flex items-center justify-center">
                <div class="relative inline-block w-full max-w-3xl p-5 md:p-8 text-left align-middle transition-all transform bg-surface rounded-xl lg:rounded-2xl shadow-xl border border-outline max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg md:text-xl font-bold text-on-surface">Kirim Opini</h3>
                        <button type="button" x-on:click="opini = false" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-surface-container transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    @if ($openOpini && $errors->any())
                        <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 text-red-600 text-sm border border-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('opini.submit.store') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div>
                            <label for="modal_title" class="block text-sm font-medium text-on-surface mb-1.5">Judul Opini</label>
                            <input type="text" name="title" id="modal_title" value="{{ old('title') }}" required maxlength="255"
                                   class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                   placeholder="Judul opini Anda">
                        </div>

                        <div>
                            <label for="modal_excerpt" class="block text-sm font-medium text-on-surface mb-1.5">Ringkasan <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                            <input type="text" name="excerpt" id="modal_excerpt" value="{{ old('excerpt') }}" maxlength="500"
                                   class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                   placeholder="Ringkasan singkat opini">
                        </div>

                        <div>
                            <label for="modal_body" class="block text-sm font-medium text-on-surface mb-1.5">Isi Opini</label>
                            <textarea name="body" id="editor" rows="8" required
                                      placeholder="Tuliskan isi opini Anda (minimal 50 karakter)...">{{ old('body') }}</textarea>
                        </div>

                        <div>
                            <label for="modal_thumbnail" class="block text-sm font-medium text-on-surface mb-1.5">Gambar Utama <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                            <input type="file" name="thumbnail" id="modal_thumbnail" accept="image/*"
                                   class="w-full text-sm text-on-surface-variant file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                            <p class="text-xs text-on-surface-variant mt-1">Format JPG/PNG/WebP, maksimal 2 MB.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-2">Kategori <span class="font-normal text-on-surface-variant">(maks. 3)</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($categories ?? [] as $category)
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-outline bg-surface-container/50 hover:bg-surface-container transition-colors cursor-pointer">
                                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                               class="rounded text-primary focus:ring-primary/30"
                                               {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                                        <span class="text-sm text-on-surface truncate">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('category_ids')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                            @error('category_ids.*')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="modal_kecamatan_id" class="block text-sm font-medium text-on-surface mb-1.5">Kecamatan <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                            <select name="kecamatan_id" id="modal_kecamatan_id"
                                    class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                <option value="">Pilih Kecamatan</option>
                                @foreach ($kecamatans ?? [] as $kecamatan)
                                    <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="modal_tags" class="block text-sm font-medium text-on-surface mb-1.5">Tag <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                            <input type="text" name="tags" id="modal_tags" value="{{ old('tags') }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                   placeholder="Pisahkan dengan koma, contoh: ekonomi, pendidikan">
                            <p class="text-xs text-on-surface-variant mt-1">Pisahkan beberapa tag dengan koma.</p>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition-colors">
                                <i data-lucide="send" class="w-4 h-4"></i> Kirim Opini
                            </button>
                            <button type="button" x-on:click="opini = false" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-surface-container text-on-surface text-sm font-semibold border border-outline hover:bg-surface-container/80 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
