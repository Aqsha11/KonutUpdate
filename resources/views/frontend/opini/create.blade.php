@extends('frontend.layouts.app')

@section('title', 'Kirim Opini - ' . ($site_settings['site_name'] ?? 'KonutUpdate'))

@section('meta')
    <meta name="robots" content="noindex, nofollow" />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Kirim Opini</span>
        </nav>
    </div>

    <div class="relative bg-gradient-to-br from-primary/90 via-primary to-primary-dark rounded-xl lg:rounded-2xl overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
        </div>
        <div class="relative px-6 py-10 md:px-12 md:py-12 text-center">
            <div class="w-14 h-14 md:w-16 md:h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="pen-line" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
            </div>
            <h1 class="text-2xl md:text-4xl font-extrabold text-white mb-3">Kirim Opini</h1>
            <p class="text-white/80 text-base md:text-lg max-w-xl mx-auto">Tulis gagasan dan pandangan Anda. Opini akan ditayangkan setelah diverifikasi redaksi.</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto">
        @if (session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-primary/10 text-primary text-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 text-red-600 text-sm border border-red-200">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
            <form method="POST" action="{{ route('opini.submit.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-on-surface mb-1.5">Judul Opini</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                           class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                           placeholder="Judul opini Anda">
                </div>

                <div>
                    <label for="excerpt" class="block text-sm font-medium text-on-surface mb-1.5">Ringkasan <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                    <input type="text" name="excerpt" id="excerpt" value="{{ old('excerpt') }}" maxlength="500"
                           class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                           placeholder="Ringkasan singkat opini">
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-on-surface mb-1.5">Isi Opini</label>
                    <textarea name="body" id="body" rows="12" required
                              class="w-full px-4 py-3 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-y"
                              placeholder="Tuliskan isi opini Anda (minimal 50 karakter)...">{{ old('body') }}</textarea>
                </div>

                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-on-surface mb-1.5">Gambar Utama <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="w-full text-sm text-on-surface-variant file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                    <p class="text-xs text-on-surface-variant mt-1">Format JPG/PNG/WebP, maksimal 2 MB.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-2">Kategori <span class="font-normal text-on-surface-variant">(maks. 3)</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2" id="categoryList">
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-container text-sm text-on-surface cursor-pointer">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                       class="rounded border-outline text-primary focus:ring-primary/30"
                                       {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="kecamatan_id" class="block text-sm font-medium text-on-surface mb-1.5">Kecamatan <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                        <select name="kecamatan_id" id="kecamatan_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <option value="">— Pilih Kecamatan —</option>
                            @foreach ($kecamatans as $kecamatan)
                                <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tags" class="block text-sm font-medium text-on-surface mb-1.5">Tag <span class="font-normal text-on-surface-variant">(opsional)</span></label>
                        <input type="text" name="tags" id="tags" value="{{ old('tags') }}" maxlength="255"
                               class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                               placeholder="Pisahkan dengan koma">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition-colors">
                        <i data-lucide="send" class="w-4 h-4"></i> Kirim Opini
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var list = document.getElementById('categoryList');
            if (!list) return;
            list.addEventListener('change', function (e) {
                var checked = list.querySelectorAll('input[type="checkbox"]:checked');
                if (checked.length > 3) {
                    e.target.checked = false;
                    alert('Maksimal 3 kategori.');
                }
            });
        })();
    </script>
    @endpush
@endsection
