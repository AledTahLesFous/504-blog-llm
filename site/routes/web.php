<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Models\Article;




Route::get('/article/{id}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/', function () {
    return view('home'); // page Blade principale
});

Route::get('/articles/latest', function () {
    $articles = Article::orderBy('id', 'desc')->take(10)->get();
    return view('articles.main', compact('articles')); // partial Blade
});