<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function show($id)
    {
        // Récupérer l'article principal
        $article = Article::findOrFail($id);

        // Convertir le Markdown en HTML
        $converter = new CommonMarkConverter();
        $html = $converter->convert($article->content);

        // Vérifier s'il existe un debunk pour cet article
        $debunk = DB::table('debunks')->where('article_id', $article->id)->first();

        // Créer le lien vers la page du debunk si disponible
        $debunkLink = $debunk ? route('debunk.show', ['articleId' => $article->id]) : null;

        return view('articles.show', [
            'article' => $article,
            'html' => $html,
            'debunkLink' => $debunkLink,
        ]);
    }
}
