<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\FeedController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\VideoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);

    // Tab beranda aplikasi mobile.
    Route::get('/latest', [FeedController::class, 'latest']);
    Route::get('/trending', [FeedController::class, 'trending']);
    Route::get('/opini', [FeedController::class, 'opini']);
    Route::get('/featured', [FeedController::class, 'featured']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show'])->where('slug', '[A-Za-z0-9\-]+');
    Route::get('/posts/{slug}/comments', [CommentController::class, 'index'])->where('slug', '[A-Za-z0-9\-]+');

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->where('slug', '[A-Za-z0-9\-]+');

    Route::get('/search', [SearchController::class, 'index']);

    Route::get('/videos', [VideoController::class, 'index']);
});
