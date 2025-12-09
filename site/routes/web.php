<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\NewsController;

use App\Models\Article;


Route::get('/', function () {
    return view('home'); // main page for list articles
});


Route::get('/article/{id}', [ArticleController::class, 'show'])->name('articles.show'); // Page for article detail view

Route::get('/articles/latest', function () {                        // api route for ajax update
    $articles = Article::orderBy('id', 'desc')->take(10)->get();
    return view('articles.main', compact('articles')); // article.main = blade layout of an article
});


Route::get('/api/articles/json', [NewsController::class, 'apiJson'])->name('news.api.json'); // API JSON route
Route::get('/api/articles/one', [NewsController::class, 'MapiOne']);
Route::get('/api/articles/rewrite', [NewsController::class, 'MapiRewriteOne']);
