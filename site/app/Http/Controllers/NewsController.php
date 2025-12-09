<?php

namespace App\Http\Controllers;

use App\Services\RssFeedService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private RssFeedService $rssFeedService;

    public function __construct(RssFeedService $rssFeedService)
    {
        $this->rssFeedService = $rssFeedService;
    }

    /**
     * Affiche tous les articles de toutes les sources
     */
    public function index()
    {
        $feeds = $this->rssFeedService->getAllArticles(15);
        
        return view('news.index', [
            'feeds' => $feeds
        ]);
    }



    /**
     * API JSON - Retourne tous les articles
     */
    public function apiAll()
    {
        $feeds = $this->rssFeedService->getAllArticles(30);
        
        return response()->json([
            'success' => true,
            'data' => $feeds,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * API JSON - Retourne les articles d'une source
     */
    public function apiSource(string $source)
    {
        $feedData = $this->rssFeedService->getArticlesBySource($source, 30);
        
        if (empty($feedData)) {
            return response()->json([
                'success' => false,
                'message' => 'Source non trouvée'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $feedData,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Affiche un article complet sur notre site
     */
    public function article(string $id)
    {
        $article = $this->rssFeedService->getArticleById($id);
        
        if (!$article) {
            abort(404, 'Article non trouvé');
        }
        
        return view('news.tmp-article', [
            'article' => $article
        ]);
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
}
