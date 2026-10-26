<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\TrendingController;
use App\Http\Controllers\Api\V1\VideoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show'])->where('slug', '[A-Za-z0-9\-]+');
    Route::get('/posts/{slug}/comments', [CommentController::class, 'index'])->where('slug', '[A-Za-z0-9\-]+');

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->where('slug', '[A-Za-z0-9\-]+');

    Route::get('/trending', [TrendingController::class, 'index']);
    Route::get('/search', [SearchController::class, 'index']);

    Route::get('/videos', [VideoController::class, 'index']);
});
