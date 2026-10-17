@extends('frontend.layouts.app')

@section('title', 'Akun Saya - ' . ($site_settings['site_name'] ?? 'KonutUpdate'))

@section('meta')
    <meta name="robots" content="noindex, nofollow" />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Akun Saya</span>
        </nav>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-full overflow-hidden bg-primary/10 flex items-center justify-center shrink-0 border border-outline">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="user-round" class="w-5 h-5 text-primary"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-on-surface">Profile</h1>
                    <p class="text-sm text-on-surface-variant">Kelola profil dan kata sandi Anda.</p>
                </div>
            </div>

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
                    <button type="button" onclick="toggleOpiniModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent/10 text-accent text-sm font-semibold hover:bg-accent hover:text-white transition-colors">
                        <i data-lucide="pen-line" class="w-4 h-4"></i> Kirim Opini
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Kirim Opini --}}
    <div id="opiniModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm" x-data="{}">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="relative inline-block w-full max-w-3xl p-5 md:p-8 text-left align-middle transition-all transform bg-surface rounded-xl lg:rounded-2xl shadow-xl border border-outline max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg md:text-xl font-bold text-on-surface">Kirim Opini</h3>
                    <button type="button" onclick="toggleOpiniModal()" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-surface-container transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                @if (session('success'))
                    <div class="mb-5 px-4 py-3 rounded-xl bg-primary/10 text-primary text-sm">{{ session('success') }}</div>
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
                        <textarea name="body" id="editor" rows="8" required data-upload-url="{{ route('opini.submit.upload-image') }}"
                                  class="w-full px-4 py-3 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-y"
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
                        <button type="button" onclick="toggleOpiniModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-surface-container text-on-surface text-sm font-semibold border border-outline hover:bg-surface-container/80 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleOpiniModal() {
            const modal = document.getElementById('opiniModal');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(function() {
                    if (typeof initCKEditor !== 'undefined') {
                        initCKEditor('editor', 'Tulis isi opini di sini...', '{{ route('opini.submit.upload-image') }}');
                    }
                }, 100);
            } else {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.getElementById('opiniModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                toggleOpiniModal();
            }
        });

        @if ($errors->any() || session('success'))
            window.addEventListener('load', function() {
                const modal = document.getElementById('opiniModal');
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(function() {
                    if (typeof initCKEditor !== 'undefined') {
                        initCKEditor('editor', 'Tulis isi opini di sini...', '{{ route('opini.submit.upload-image') }}');
                    }
                }, 100);
            });
        @endif
    </script>
@endsection
