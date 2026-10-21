<?php

use App\Http\Controllers\Admin\AdController as AdminAdController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KecamatanController;
use App\Http\Controllers\Admin\OpiniController as AdminOpiniController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StoryController as AdminStoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController as AuthProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Frontend\AiChatController;
use App\Http\Controllers\Frontend\AuthorController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\KecamatanController as FrontendKecamatanController;
use App\Http\Controllers\Frontend\LatestController;
use App\Http\Controllers\Frontend\LikeController;
use App\Http\Controllers\Frontend\NewsArchiveController;
use App\Http\Controllers\Frontend\OpiniController;
use App\Http\Controllers\Frontend\OpiniSubmissionController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\TagController;
use App\Http\Controllers\Frontend\TrendingController;
use App\Http\Controllers\Frontend\VideoArchiveController;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// Frontend Routes
Route::get('/iklan/{ad}', function (Ad $ad) {
    $ad->incrementClicks();

    return redirect($ad->link ?: url('/'));
})->name('ads.click');
Route::get('/', [HomeController::class, 'index'])->name('home');

// Backward-compat: URL lama /berita/{slug} -> 301 ke URL kanonik tanpa prefix.
// Dijaga agar backlink lama & indeks search engine tidak ikut mati.
Route::get('/berita/{slug}', function (string $slug) {
    return redirect()->to(url($slug), 301);
})->where('slug', '[A-Za-z0-9\-]+')->name('posts.legacy');
Route::get('/semua-berita', [NewsArchiveController::class, 'index'])->name('news.index');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tag/{slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/kecamatan/{slug}', [FrontendKecamatanController::class, 'show'])->name('kecamatan.show');
Route::get('/trending', [TrendingController::class, 'index'])->name('trending');
Route::get('/terkini', [LatestController::class, 'index'])->name('terkini');
Route::get('/video', [VideoArchiveController::class, 'index'])->name('videos');
Route::get('/opini', [OpiniController::class, 'index'])->name('opini');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/tanya-berita', [AiChatController::class, 'ask'])->middleware('throttle:ai-chat')->name('ai-chat.ask');
Route::middleware(['auth', 'role:pembaca,kontributor'])->group(function () {
    Route::get('/kirim-opini', fn () => redirect()->route('profile.index', ['opini' => 1]))->name('opini.submit.create');
    Route::post('/kirim-opini', [OpiniSubmissionController::class, 'store'])->middleware('throttle:5,1')->name('opini.submit.store');
    Route::post('/kirim-opini/upload-image', [OpiniSubmissionController::class, 'uploadImage'])->middleware('throttle:10,1')->name('opini.submit.upload-image');
});
Route::post('/berita/{post}/komentar', [CommentController::class, 'store'])->middleware('throttle:5,1')->name('comments.store');
Route::post('/berita/{post}/like', [LikeController::class, 'toggle'])->middleware('throttle:30,1')->name('posts.like');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('pages.about');
Route::get('/pedoman-media-siber', [PageController::class, 'pedoman'])->name('pages.pedoman');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/kontak', [PageController::class, 'kontak'])->name('pages.kontak');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');

// Profil publik penulis (byline artikel). Harus di atas catch-all /{slug}.
Route::get('/penulis/{slug}', [AuthorController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('authors.show');

// Auth Routes (Laravel built-in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
    Route::get('/daftar', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'register'])->middleware('throttle:register');
    Route::get('/daftar/sukses', [RegisterController::class, 'success'])->name('register.success');
    Route::get('/lupa-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Verifikasi Email
Route::get('/email/verifikasi/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verifikasi/ulang', [VerificationController::class, 'resend'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::get('/email/verifikasi', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/profil', [AuthProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil', [AuthProfileController::class, 'update'])->name('profile.update');
});

// Panel kontributor dihapus — kontributor memakai halaman profil publik.
Route::middleware(['auth', 'role:kontributor'])->get('/panel-kontributor', fn () => redirect()->route('profile.index'))
    ->name('kontributor.dashboard');

// SEO Routes
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Disallow: /login',
        'Disallow: /daftar',
        'Disallow: /email/',
        'Disallow: /search',
        'Disallow: /kirim-opini',
        '',
        'Sitemap: '.url('sitemap.xml'),
        'Sitemap: '.url('sitemap-news.xml'),
    ];

    return response(implode("\n", $lines))->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/feed', function () {
    $posts = Post::published()->with(['author', 'categories'])->latest()->take(20)->get();
    $xml = view('frontend.rss.feed', compact('posts'))->render();
    $xml = preg_replace('/\n\s*\n/', "\n", trim($xml));

    return response($xml, 200)
        ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('rss.feed');

Route::get('/sitemap.xml', function () {
    $xml = Cache::remember('sitemap_xml', 3600, function () {
        // Artikel duplikat (duplicate_of_id) sengaja tidak dimasukkan.
        $posts = Post::published()->notDuplicate()
            ->orderByDesc('published_at')
            ->get(['slug', 'published_at', 'updated_at', 'content_updated_at']);

        // Kategori diambil lewat pivot post_categories (relasi category_id
        // lama tidak pernah diisi admin sehingga bisa kosong semua).
        $categories = Category::query()
            ->whereHas('allPosts', fn ($q) => $q->published()->notDuplicate())
            ->withMax(['allPosts' => fn ($q) => $q->published()->notDuplicate()], 'published_at')
            ->orderBy('name')
            ->get();

        $tags = Tag::query()
            ->whereHas('posts', fn ($q) => $q->published()->notDuplicate())
            ->withCount(['posts' => fn ($q) => $q->published()->notDuplicate()])
            ->orderByDesc('posts_count')
            ->get()
            ->filter(fn ($tag) => $tag->posts_count >= 2)
            ->values();

        $kecamatans = Kecamatan::query()
            ->ordered()
            ->withMax(['posts' => fn ($q) => $q->published()->notDuplicate()], 'published_at')
            ->get();

        // Halaman statis tanpa URL ramah tetap perlu masuk sitemap.
        $pages = Page::published()
            ->whereNotIn('slug', array_keys(Page::FRIENDLY_ROUTES))
            ->orderBy('title')
            ->get();

        $lastmod = $posts->first()?->published_at ?? now();

        return response()->view('frontend.partials.sitemap', compact('posts', 'categories', 'tags', 'kecamatans', 'pages', 'lastmod'))
            ->getContent();
    });

    return response($xml, 200)
        ->header('Content-Type', 'application/xml')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('sitemap');

// News sitemap (Google News). Alias /news-sitemap.xml disediakan karena
// itu konvensi yang paling umum dipakai tooling & publisher lain.
$newsSitemap = function () {
    $xml = Cache::remember('sitemap_news_xml', 900, function () {
        $posts = Post::published()->notDuplicate()
            ->where('published_at', '>=', now()->subDays(2))
            ->with(['writer', 'categories'])
            ->orderByDesc('published_at')
            ->limit(1000)
            ->get();

        return response()->view('frontend.partials.sitemap-news', compact('posts'))->getContent();
    });

    return response($xml, 200)
        ->header('Content-Type', 'application/xml')
        ->header('Cache-Control', 'public, max-age=900');
};

Route::get('/sitemap-news.xml', $newsSitemap)->name('sitemap.news');
Route::get('/news-sitemap.xml', $newsSitemap)->name('sitemap.news.alias');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.session-timeout'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', AdminPostController::class);
    Route::post('/posts/{post}/publish', [AdminPostController::class, 'publish'])->name('posts.publish');
    Route::post('/posts/{post}/draft', [AdminPostController::class, 'draft'])->name('posts.draft');
    Route::post('/posts/{post}/approve', [AdminPostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/reject', [AdminPostController::class, 'reject'])->name('posts.reject');
    Route::post('/posts/upload-image', [AdminPostController::class, 'uploadImage'])->name('posts.upload-image');

    Route::get('/opini', [AdminOpiniController::class, 'index'])->middleware('permission:manage_opini')->name('opini.index');
    Route::get('/opini/buat', [AdminOpiniController::class, 'create'])->middleware('permission:manage_opini')->name('opini.create');
    Route::post('/opini', [AdminOpiniController::class, 'store'])->middleware('permission:manage_opini')->name('opini.store');
    Route::get('/opini/{post}/edit', [AdminOpiniController::class, 'edit'])->middleware('permission:manage_opini')->name('opini.edit');
    Route::put('/opini/{post}', [AdminOpiniController::class, 'update'])->middleware('permission:manage_opini')->name('opini.update');
    Route::delete('/opini/{post}', [AdminOpiniController::class, 'destroy'])->middleware('permission:manage_opini')->name('opini.destroy');
    Route::post('/opini/{post}/publish', [AdminOpiniController::class, 'publish'])->middleware('permission:manage_opini')->name('opini.publish');
    Route::post('/opini/{post}/draft', [AdminOpiniController::class, 'draft'])->middleware('permission:manage_opini')->name('opini.draft');
    Route::post('/opini/{post}/approve', [AdminOpiniController::class, 'approve'])->middleware('permission:manage_opini')->name('opini.approve');
    Route::post('/opini/{post}/reject', [AdminOpiniController::class, 'reject'])->middleware('permission:manage_opini')->name('opini.reject');
    Route::post('/opini/upload-image', [AdminOpiniController::class, 'uploadImage'])->middleware('permission:manage_opini')->name('opini.upload-image');

    Route::get('/videos', [AdminVideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/buat', [AdminVideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [AdminVideoController::class, 'store'])->name('videos.store');
    Route::get('/videos/{post}/edit', [AdminVideoController::class, 'edit'])->name('videos.edit');
    Route::put('/videos/{post}', [AdminVideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{post}', [AdminVideoController::class, 'destroy'])->name('videos.destroy');
    Route::post('/videos/{post}/publish', [AdminVideoController::class, 'publish'])->name('videos.publish');
    Route::post('/videos/{post}/draft', [AdminVideoController::class, 'draft'])->name('videos.draft');

    Route::get('/stories', [AdminStoryController::class, 'index'])->name('stories.index');
    Route::get('/stories/buat', [AdminStoryController::class, 'create'])->name('stories.create');
    Route::post('/stories', [AdminStoryController::class, 'store'])->name('stories.store');
    Route::get('/stories/{story}/edit', [AdminStoryController::class, 'edit'])->name('stories.edit');
    Route::put('/stories/{story}', [AdminStoryController::class, 'update'])->name('stories.update');
    Route::delete('/stories/{story}', [AdminStoryController::class, 'destroy'])->name('stories.destroy');
    Route::post('/stories/{story}/extend', [AdminStoryController::class, 'extend'])->name('stories.extend');
    Route::post('/stories/{story}/toggle', [AdminStoryController::class, 'toggle'])->name('stories.toggle');

    Route::resource('categories', AdminCategoryController::class);
    Route::resource('kecamatans', KecamatanController::class);

    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    Route::resource('ads', AdminAdController::class);
    Route::resource('pages', App\Http\Controllers\Admin\PageController::class);
    Route::post('/pages/{page}/publish', [App\Http\Controllers\Admin\PageController::class, 'publish'])->name('pages.publish');
    Route::post('/pages/{page}/draft', [App\Http\Controllers\Admin\PageController::class, 'draft'])->name('pages.draft');
    Route::resource('roles', AdminRoleController::class);
    Route::resource('permissions', AdminPermissionController::class);
    Route::resource('users', AdminUserController::class)->middleware('role:super_admin');
    Route::post('users/{user}/verifikasi', [AdminUserController::class, 'verify'])
        ->middleware('role:super_admin')
        ->name('users.verify');

    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::post('/comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

// ── Detail berita: URL kanonik /{slug} (tanpa prefix /berita) ──────────────
// WAJIB didaftarkan paling akhir. Laravel mencocokkan route sesuai urutan
// registrasi, sehingga semua route literal di atas (/terkini, /trending,
// /opini, /video, /search, /login, /daftar, /admin, ...) tetap menang dulu.
// Menambah route root-level baru berarti menambahkannya DI ATAS blok ini.
Route::get('/{slug}', [PostController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('posts.show');
