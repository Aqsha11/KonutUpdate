<?php

use App\Http\Controllers\Admin\AdController as AdminAdController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\TagController;
use App\Http\Controllers\Frontend\KecamatanController as FrontendKecamatanController;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Route;

// Frontend Routes
Route::get('/iklan/{ad}', function (Ad $ad) {
    $ad->incrementClicks();

    return redirect($ad->link ?: url('/'));
})->name('ads.click');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tag/{slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/kecamatan/{slug}', [FrontendKecamatanController::class, 'show'])->name('kecamatan.show');
Route::get('/trending', [\App\Http\Controllers\Frontend\TrendingController::class, 'index'])->name('trending');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/berita/{post}/komentar', [CommentController::class, 'store'])->middleware('throttle:5,1')->name('comments.store');
Route::post('/berita/{post}/like', [\App\Http\Controllers\Frontend\LikeController::class, 'toggle'])->name('posts.like');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('pages.about');
Route::get('/pedoman-media-siber', [PageController::class, 'pedoman'])->name('pages.pedoman');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/kontak', [PageController::class, 'kontak'])->name('pages.kontak');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');

// Auth Routes (Laravel built-in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// SEO Routes
Route::get('/robots.txt', function () {
    $robots = "User-agent: *\n";
    $robots .= "Allow: /\n";
    $robots .= "Disallow: /admin\n";
    $robots .= "Disallow: /login\n";
    $robots .= 'Sitemap: '.url('sitemap.xml')."\n";

    return response($robots)->header('Content-Type', 'text/plain');
});

Route::get('/feed', function () {
    $posts = Post::published()->with(['author', 'categories'])->latest()->take(20)->get();
    $xml = view('frontend.rss.feed', compact('posts'))->render();
    $xml = preg_replace('/\n\s*\n/', "\n", trim($xml));

    return response($xml, 200)
        ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('rss.feed');

Route::get('/sitemap.xml', function () {
    $posts = Post::published()->latest()->get();
    $categories = Category::all();
    $tags = Tag::all();

    return response()->view('frontend.partials.sitemap', compact('posts', 'categories', 'tags'))->header('Content-Type', 'application/xml');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', AdminPostController::class);
    Route::post('/posts/{post}/publish', [AdminPostController::class, 'publish'])->name('posts.publish');
    Route::post('/posts/{post}/draft', [AdminPostController::class, 'draft'])->name('posts.draft');
    Route::post('/posts/upload-image', [AdminPostController::class, 'uploadImage'])->name('posts.upload-image');

    Route::resource('categories', AdminCategoryController::class);
    Route::resource('kecamatans', \App\Http\Controllers\Admin\KecamatanController::class);

    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    Route::resource('ads', AdminAdController::class);
    Route::resource('pages', App\Http\Controllers\Admin\PageController::class);
    Route::resource('roles', AdminRoleController::class);
    Route::resource('permissions', AdminPermissionController::class);
    Route::resource('users', AdminUserController::class)->middleware('role:super_admin');

    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::post('/comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
