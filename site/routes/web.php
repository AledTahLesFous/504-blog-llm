<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DebunkController;
use App\Http\Controllers\NewsController;

use App\Models\Article;


Route::get('/', function () {
    return view('home'); // main page for list articles
});


Route::get('/article/{id}', [ArticleController::class, 'show'])->name('articles.show'); // Page for article detail view
Route::get('/articles/{articleId}/debunk', [DebunkController::class, 'show'])->name('debunk.show');


Route::get('/articles/latest', function () {                        // api route for ajax update
    $articles = Article::orderBy('created_at', 'desc')->get();
    return view('articles.main', compact('articles')); // article.main = blade layout of an article
});




// 🔐 Routes API avec sécurité API KEY
Route::middleware('api.key')->group(function () {

    Route::get('/api/articles/json', [NewsController::class, 'apiJson'])->name('news.api.json');
    Route::get('/api/articles/one', [NewsController::class, 'apiOne']);
    Route::get('/api/articles/rewrite', [NewsController::class, 'Main']);
    Route::get('/api/articles/debunk', [NewsController::class, 'apiDebunkOne']);

});

