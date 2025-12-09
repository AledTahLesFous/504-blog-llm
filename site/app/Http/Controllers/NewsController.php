<?php

namespace App\Http\Controllers;

use App\Services\RssFeedService;
use Illuminate\Http\Request;
use App\Services\AIManager;

class NewsController extends Controller
{
    private RssFeedService $rssFeedService;

    public function __construct(RssFeedService $rssFeedService)
    {
        $this->rssFeedService = $rssFeedService;
    }

    /**
     * API JSON - Retourne tous les articles avec titre, corps et lien
     */
    public function apiJson()
    {
        $feeds = $this->rssFeedService->getAllArticles(50);
        
        // Transformer les articles pour ne garder que titre, corps et lien
        $articles = array_map(function($article) {
            return [
                'titre' => $article['title'] ?? '',
                'corps' => $article['content'] ?? '',
                'lien' => $article['url'] ?? ''
            ];
        }, $feeds);
        
        return response()->json([
            'success' => true,
            'count' => count($articles),
            'articles' => $articles,
            'timestamp' => now()->toIso8601String()
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


public function apiOne(AIManager $ai)
{
    // Récupération locale (pas de requête HTTP interne)
    $feeds = $this->rssFeedService->getAllArticles(50);

    if (empty($feeds)) {
        return response()->json([
            'success' => false,
            'message' => 'Aucun article disponible'
        ], 404);
    }

    // Prépare des articles propres pour l'IA
    $articles = array_map(function($a) {
        return [
            "title" => $a['title'] ?? '',
            "subtitle" => "",
            "content" => $a['content'] ?? '',
            "url" => $a['url'] ?? '',
        ];
    }, $feeds);
    

    // L'IA choisit 1 article
    $chosen = $ai->chooseArticle($articles);

    return response()->json([
        'success' => true,
        'article' => $chosen,
        'timestamp' => now()->toIso8601String()
    ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

public function apiRewriteOne(AIManager $ai)
{
    // Récupérer un article choisi par l'IA
    $feeds = $this->rssFeedService->getAllArticles(50);
    if (empty($feeds)) {
        return response()->json(['success'=>false,'message'=>'Aucun article disponible'],404);
    }

    $articles = array_map(function($a){
        return [
            "title"=>$a['title'] ?? '',
            "subtitle"=>"",
            "content"=>$a['content'] ?? '',
            "url"=>$a['url'] ?? ''
        ];
    }, $feeds);

    $chosen = $ai->chooseArticle($articles);

    // Réécriture du contenu
    $rewritten = $ai->rewriteArticle($chosen);

    return response()->json([
        'success' => true,
        'article' => $rewritten,
        'original_url' => $chosen['url'] ?? null,
        'timestamp' => now()->toIso8601String()
    ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}


}
