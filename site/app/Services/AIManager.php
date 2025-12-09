<?php

namespace App\Services;

class AIManager
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

public function askGemini(string $prompt): array
{
    $response = $this->gemini->generateStructured(
        model: "gemini-2.5-flash",
        prompt: $prompt,
        schema: [
            "type" => "object",
            "properties" => [
                "title" => ["type" => "string"],
                "subtitle" => ["type" => "string"],
                "content_markdown" => ["type" => "string"]
            ],
            "required" => ["title", "subtitle", "content_markdown"]
        ]
    );

    // Récupération du texte brisé dans la réponse
    $raw = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

    if (!$raw) {
        throw new \Exception("Réponse inattendue de Gemini");
    }

    // Décodage JSON → array PHP
    return json_decode($raw, true);
}



    public function test()
    {       
        return $this->askGemini("Donne moi un titre, un sous titre, et un contenu de 5 lignes. Je veux que le contenu soit en format Markdown. 
        Le reste en plain/text.
        Renvoie **uniquement** le JSON demandé.");
    }
}
