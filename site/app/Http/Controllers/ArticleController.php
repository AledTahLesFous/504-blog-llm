<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use League\CommonMark\CommonMarkConverter;

class ArticleController extends Controller
{
    public function show($id)
    {
        $article = Article::findOrFail($id);

        $converter = new CommonMarkConverter();
        $html = $converter->convert($article->content);

        return view('articles.show', [
            'article' => $article,
            'html' => $html,
        ]);
    }
}
