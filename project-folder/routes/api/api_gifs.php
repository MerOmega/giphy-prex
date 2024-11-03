<?php


use App\Http\Controllers\GifController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/gifs/search', [GifController::class, 'searchGifs']);
    Route::get('/gifs/{id}', [GifController::class, 'getGifById']);
    Route::post('/gifs/favorite', [GifController::class, 'saveFavoriteGif']);
});
