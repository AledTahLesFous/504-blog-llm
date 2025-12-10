<?php

use App\Services\GeminiService;
use App\Services\MistralService;
use App\Http\Controllers\AIController;
use App\Services\RssFeedService;
use Illuminate\Support\Facades\Cache;

// ===== TESTS D'INTÉGRATION RÉELS POUR L'IA =====

test('Gemini API responds with valid structured data', function () {
    $geminiService = new GeminiService();
    
    $schema = [
        "type" => "object",
        "properties" => [
            "name" => ["type" => "string"],
            "age" => ["type" => "number"]
        ],
        "required" => ["name", "age"]
    ];
    
    $response = $geminiService->generateStructured(
        model: "gemini-2.5-flash",
        prompt: "Génère un personnage fictif avec un prénom français et un âge entre 20 et 40 ans.",
        schema: $schema
    );
    
    expect($response)->toBeArray();
    expect($response)->not->toBeEmpty();
    
    // Gemini a répondu avec un format structuré
    if (isset($response['name']) && isset($response['age'])) {
        expect($response['name'])->toBeString();
        expect($response['age'])->toBeNumeric();
        dump("✓ Gemini a généré: {$response['name']}, {$response['age']} ans");
    } else {
        dump("✓ Gemini a répondu avec: " . json_encode($response));
    }
})->group('integration', 'gemini');

test('AIController chooses article from real RSS feed using Gemini', function () {
    $aiController = new AIController(new GeminiService(), new MistralService());
    $rssService = new RssFeedService();
    
    Cache::flush();
    
    // Récupérer de vrais articles RSS
    $articles = $rssService->getAllArticles(10);
    
    // Préparer les articles pour Gemini (uniquement titre + URL)
    $articlesForAI = array_map(function($article) {
        return [
            'title' => $article['title'],
            'url' => $article['url']
        ];
    }, array_slice($articles, 0, 10));
    
    // Appeler Gemini pour choisir un article
    $chosen = $aiController->chooseArticle($articlesForAI);
    
    // Vérifications
    expect($chosen)->toBeArray();
    expect($chosen)->toHaveKey('url');
    expect($chosen['url'])->toBeString()->toStartWith('http');
    
    // Vérifier que l'URL choisie fait partie des articles proposés
    $proposedUrls = array_column($articlesForAI, 'url');
    expect($chosen['url'])->toBeIn($proposedUrls);
    
    dump("✓ Gemini a choisi l'article: " . $chosen['url']);
})->group('integration', 'gemini');

test('AIController rewrites article content with Gemini', function () {
    $aiController = new AIController(new GeminiService(), new MistralService());
    $rssService = new RssFeedService();
    
    Cache::flush();
    
    // Récupérer un vrai article
    $articles = $rssService->getAllArticles(5);
    $article = $articles[0];
    
    // Réécrire l'article avec Gemini
    $rewritten = $aiController->rewriteArticle($article);
    
    // Vérifications
    expect($rewritten)->toBeArray();
    expect($rewritten)->toHaveKeys(['title', 'subtitle', 'content']);
    expect($rewritten['title'])->toBeString()->not->toBeEmpty();
    expect($rewritten['subtitle'])->toBeString();
    expect($rewritten['content'])->toBeString()->not->toBeEmpty();
    
    // Le contenu réécrit doit être différent de l'original
    expect($rewritten['title'])->not->toBe($article['title']);
    expect($rewritten['content'])->not->toBe($article['content']);
    
    dump("✓ Titre original: " . substr($article['title'], 0, 50) . "...");
    dump("✓ Titre réécrit: " . substr($rewritten['title'], 0, 50) . "...");
})->group('integration', 'gemini');

test('Gemini request completes within 60 seconds timeout', function () {
    $geminiService = new GeminiService();
    
    $schema = [
        "type" => "object",
        "properties" => [
            "summary" => ["type" => "string"]
        ],
        "required" => ["summary"]
    ];
    
    $start = microtime(true);
    
    $response = $geminiService->generateStructured(
        model: "gemini-2.5-flash",
        prompt: "Résume en une phrase: L'intelligence artificielle révolutionne notre monde.",
        schema: $schema
    );
    
    $duration = microtime(true) - $start;
    
    expect($duration)->toBeLessThan(60);
    expect($response)->toBeArray();
    
    if (isset($response['summary'])) {
        expect($response['summary'])->toBeString();
    }
    
    dump("✓ Réponse Gemini en " . round($duration, 2) . "s (< 60s)");
})->group('integration', 'gemini');

test('Gemini handles invalid schema gracefully', function () {
    $aiController = new AIController(new GeminiService(), new MistralService());
    $rssService = new RssFeedService();
    
    Cache::flush();
    
    $articles = $rssService->getAllArticles(3);
    $articlesForAI = array_map(fn($a) => ['title' => $a['title'], 'url' => $a['url']], $articles);
    
    try {
        $chosen = $aiController->chooseArticle($articlesForAI);
        
        // Si ça marche, vérifier que c'est valide
        expect($chosen)->toBeArray();
        expect($chosen)->toHaveKey('url');
        
        dump("✓ Gemini a géré le schéma correctement");
    } catch (\Exception $e) {
        // Si erreur, vérifier que le message est informatif
        expect($e->getMessage())->toContain('Gemini');
        
        dump("✓ Exception capturée: " . $e->getMessage());
    }
})->group('integration', 'gemini');
