<?php

use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrendingController;
use App\Http\Controllers\WatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CommentController;


Route::get('/', [MovieController::class, 'index'])->name('home');

Route::get('/anime/{slug}', [MovieController::class, 'show'])->name('movie.show');
Route::get('/episode/{id}', [EpisodeController::class, 'show'])->name('episode.show');
Route::get('/anime/{slug}/play', [MovieController::class, 'play'])->name('movie.play');

Route::post('/comments', [CommentController::class, 'store'])->name('comment.store');
require __DIR__.'/auth.php';
