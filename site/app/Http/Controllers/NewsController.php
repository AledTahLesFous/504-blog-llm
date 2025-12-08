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
     * Affiche les articles d'une source spécifique
     */
    public function source(string $source)
    {
        $feedData = $this->rssFeedService->getArticlesBySource($source, 20);
        
        if (empty($feedData)) {
            abort(404, 'Source non trouvée');
        }
        
        return view('news.source', [
            'feedData' => $feedData,
            'source' => $source
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
        
        return view('news.article', [
            'article' => $article
        ]);
    }
}
