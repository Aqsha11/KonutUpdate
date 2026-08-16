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
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController as AuthProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Contributor\DashboardController as ContributorDashboardController;
use App\Http\Controllers\Contributor\PostController as ContributorPostController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\KecamatanController as FrontendKecamatanController;
use App\Http\Controllers\Frontend\LatestController;
use App\Http\Controllers\Frontend\LikeController;
use App\Http\Controllers\Frontend\OpiniController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\TagController;
use App\Http\Controllers\Frontend\TrendingController;
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
Route::get('/trending', [TrendingController::class, 'index'])->name('trending');
Route::get('/terkini', [LatestController::class, 'index'])->name('terkini');
Route::get('/opini', [OpiniController::class, 'index'])->name('opini');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/berita/{post}/komentar', [CommentController::class, 'store'])->middleware('throttle:5,1')->name('comments.store');
Route::post('/berita/{post}/like', [LikeController::class, 'toggle'])->middleware('throttle:30,1')->name('posts.like');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('pages.about');
Route::get('/pedoman-media-siber', [PageController::class, 'pedoman'])->name('pages.pedoman');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/kontak', [PageController::class, 'kontak'])->name('pages.kontak');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');

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

// Panel Kontributor — masyarakat menulis berita/opini, menunggu verifikasi admin
Route::prefix('panel-kontributor')->name('kontributor.')->middleware(['auth', 'role:kontributor'])->group(function () {
    Route::get('/', [ContributorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tulis', [ContributorPostController::class, 'create'])->name('posts.create');
    Route::post('/tulis', [ContributorPostController::class, 'store'])->name('posts.store');
    Route::post('/upload-image', [ContributorPostController::class, 'uploadImage'])->name('posts.upload-image');
    Route::get('/{post}/edit', [ContributorPostController::class, 'edit'])->name('posts.edit');
    Route::put('/{post}', [ContributorPostController::class, 'update'])->name('posts.update');
    Route::delete('/{post}', [ContributorPostController::class, 'destroy'])->name('posts.destroy');
});

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
