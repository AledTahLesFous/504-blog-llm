<?php

namespace App\Http\Controllers;

use App\Services\RssFeedService;
use App\Services\TwitterService;
use Illuminate\Http\Request;
use App\Services\AIController;
use App\Models\ChosenArticle;


class NewsController extends Controller
{
    private RssFeedService $rssFeedService;
    private TwitterService $twitterService;

    public function __construct(RssFeedService $rssFeedService, TwitterService $twitterService)
    {
        $this->rssFeedService = $rssFeedService;
        $this->twitterService = $twitterService;
    }

    /**
     * API JSON - Retourne tous les articles avec titre, corps et lien
     */
    public function apiJson()
    {
        $feeds = $this->rssFeedService->getAllArticles(50);
        
        // Transformer les articles pour ne garder que id, titre, corps et lien
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


    public function apiOne(AIController $ai)
    {
        // Récupération locale (pas de requête HTTP interne)
        $feeds = $this->rssFeedService->getAllArticles(10);

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

        // Calculer le MD5 de l'URL pour l'utiliser comme id
        $articleId = md5($chosen['url'] ?? '');

        $record = ChosenArticle::create([
            'id' => $articleId,
            'data' => $chosen
        ]);


        return response()->json([
            'success' => true,
            'article' => $chosen,
            'timestamp' => now()->toIso8601String()
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function apiRewriteOne(AIController $ai)
    {
        $record = ChosenArticle::latest()->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun article enregistré'
            ], 404);
        }

        $article = $record->data;

        $rewritten = $ai->rewriteArticle($article);

        return response()->json([
            'success' => true,
            'article' => $rewritten,
            'timestamp' => now()->toIso8601String()
        ]);
    }


    /**
     * Poster un article sur Twitter (API JSON)
     */
    public function postToTwitter(string $id)
    {
        $article = $this->rssFeedService->getArticleById($id);
        
        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé'
            ], 404);
        }

        // Formater le tweet
        $tweetText = $this->twitterService->formatArticleForTweet($article);
        
        // Poster sur Twitter
        $result = $this->twitterService->postTweet($tweetText);
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'tweet' => $tweetText,
            'timestamp' => now()->toIso8601String()
        ], $result['success'] ? 200 : 400, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

/*
        public function MapiOne(AIController $ai)
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
        $chosen = $ai->MchooseArticle($articles);

         $record = ChosenArticle::create([
            'data' => $chosen
        ]);

        return response()->json([
            'success' => true,
            'article' => $chosen,
            'timestamp' => now()->toIso8601String()
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

        public function MapiRewriteOne(AIController $ai)
    {
        $record = ChosenArticle::latest()->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun article enregistré'
            ], 404);
        }

        $article = $record->data;

        $rewritten = $ai->MrewriteArticle($article);

        return response()->json([
            'success' => true,
            'article' => $rewritten,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    */
}