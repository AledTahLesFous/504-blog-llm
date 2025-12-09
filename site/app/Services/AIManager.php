<?php

namespace App\Services;

class AIManager
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function test()
    {       
        return $this->askGemini("
        Donne moi un titre, un sous titre, et un contenu de 5 lignes.
        Le champ `content` doit être du vrai Markdown (titres, paragraphes).
        Le reste en plain/text.
        Renvoie **uniquement** le JSON demandé.");
    }

    public function chooseArticle(array $articles): array   // A RAJOUTER LE IF DEJA BDD ET A METTRE LE LINK DANS LA BDD
    {
        // On envoie la liste complète à Gemini
        $prompt = "
    Voici une liste d'articles JSON :

    " . json_encode($articles, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "

    Choisis le MEILLEUR article selon toi (informatif, clair, actuel).
    Il faut que l'article ait un potentiel pour susciter les réactions émotionnelles énervantes.
    Renvoie uniquement le JSON EXACT correspondant à la structure :

    {
    \"title\": \"...\",
    \"subtitle\": \"...\",
    \"content\": \"...\",
    \"url\": \"...\"
    }

    NE RENVOIE RIEN D’AUTRE.
    ";

        // Utilise la même structure que askGemini mais adaptée
        $response = $this->gemini->generateStructured(
            model: "gemini-2.5-flash",
            prompt: $prompt,
            schema: [
                "type" => "object",
                "properties" => [
                    "title" => ["type" => "string"],
                    "subtitle" => ["type" => "string"],
                    "content" => ["type" => "string"],
                    "url" => ["type" => "string"]
                ],
                "required" => ["title", "subtitle", "content", "url"]
            ]
        );

        $raw = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$raw) {
            throw new \Exception("Réponse inattendue de Gemini");
        }

        return json_decode($raw, true);
    }

}
