<?php

namespace App\Http\Controllers;

use App\Services\RssFeedService;
use App\Services\TwitterService;
use Illuminate\Http\Request;
use App\Http\Controllers\AIController;
use App\Models\ChosenArticle;
use App\Models\Article;



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

        // Vérifier si l'article existe déjà dans la base
        $existing = ChosenArticle::find($articleId);

        if ($existing) {
            // Article déjà en base, ne rien faire, juste retourner l'existant
            return response()->json([
                'success' => true,
                'article' => $existing->data,
                'timestamp' => now()->toIso8601String()
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // Article nouveau, on l'ajoute
        $record = ChosenArticle::create([
            'id' => $articleId,
            'url' => $chosen['url'],   // <-- essentiel !
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
        // Récupérer depuis la table Article, pas depuis le RSS
        $article = Article::find($id);

        if (!$article) {
            return [
                'success' => false,
                'message' => 'Article non trouvé'
            ];
        }

        // Formater le tweet
        $tweetText = $this->twitterService->formatArticleForTweet($article->toArray());
        
        // Poster sur Twitter
        $result = $this->twitterService->postTweet($tweetText);
        
        return [
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'tweet' => $tweetText
        ];
    }


    public function apiEvalOne(AIController $ai)
    {
        $record = ChosenArticle::latest()->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun article enregistré'
            ], 404);
        }

        $original = $record->data;

        $maxAttempts = 3;
        $attempt = 0;
        $rewritten = $ai->rewriteArticle($original);
        $evaluation = $ai->evaluateArticle($rewritten);

        while ($evaluation['score_global'] < 0.6 && $attempt < $maxAttempts) {
            // Réécrire l'article et réévaluer
            $rewritten = $ai->rewriteArticle($original);
            $evaluation = $ai->evaluateArticle($rewritten);
            $attempt++;
        }

        $twitterResult = null;


        // Si l'article est "bon" (score >= 0.6), l'enregistrer
        if ($evaluation['score_global'] >= 0.6) {
            $articleId = md5($original['url']); // md5 de l'URL originale

            // Vérifier si l'article existe déjà
            $existing = Article::find($articleId);
            if (!$existing) {
                $article = Article::create([
                    'id' => $articleId,
                    'url' => $original['url'],
                    'title' => $rewritten['title'],
                    'subtitle' => $rewritten['subtitle'] ?? '',
                    'content' => $rewritten['content'], // Markdown
                    'published' => false,
                    'published_at' => null,
                ]);

                $twitterResult = $this->postToTwitter($article->id);
            }
        }

        return response()->json([
            'success' => true,
            'rewritten_article' => $rewritten,
            'evaluation' => $evaluation,
            'twitter' => $twitterResult,
            'attempts' => $attempt + 1,
            'timestamp' => now()->toIso8601String()
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


}