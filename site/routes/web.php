<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Models\Article;




Route::get('/article/{id}', [ArticleController::class, 'show'])->name('articles.show');


use App\Http\Controllers\NewsController;

// Routes pour l'interface web
Route::get('/', [NewsController::class, 'index'])->name('news.index');
Route::get('/source/{source}', [NewsController::class, 'source'])->name('news.source');
Route::get('/article/{id}', [NewsController::class, 'article'])->name('news.article');

// Routes API JSON
Route::prefix('api')->group(function () {
    Route::get('/news', [NewsController::class, 'apiAll'])->name('api.news.all');
    Route::get('/news/{source}', [NewsController::class, 'apiSource'])->name('api.news.source');
});

Route::get('/articles/latest', function () {
    $articles = Article::orderBy('id', 'desc')->take(10)->get();
    return view('articles.main', compact('articles')); // partial Blade
});