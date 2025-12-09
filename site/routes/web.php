<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\NewsController;
use App\Services\AIManager;

use App\Models\Article;


Route::get('/', function () {
    return view('home'); // main page for list articles
});

Route::get('/tmp-articles', [NewsController::class, 'index'])->name('news.index');   // temp route for news fetch
Route::get('/tmp-articles/{id}', [NewsController::class, 'article'])->name('news.tmp-article');  // temp route for news id 

Route::get('/article/{id}', [ArticleController::class, 'show'])->name('articles.show'); // Page for article detail view

Route::get('/articles/latest', function () {                        // api route for ajax update
    $articles = Article::orderBy('id', 'desc')->take(10)->get();
    return view('articles.main', compact('articles')); // article.main = blade layout of an article
});

Route::get('/test', function () {
    return app(AIManager::class)->test();
});

Route::get('/api/articles/json', [NewsController::class, 'apiJson'])->name('news.api.json'); // API JSON route
