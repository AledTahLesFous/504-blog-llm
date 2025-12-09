<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $apiKey;
    protected string $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/";

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function generateText(string $model, string $prompt): string
    {
        $endpoint = $this->apiUrl . $model . ":generateContent?key=" . $this->apiKey;

        $response = Http::post($endpoint, [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception("Erreur Gemini : " . $response->body());
        }

        // Récupération du texte dans la réponse
        return $response->json("candidates.0.content.parts.0.text") ?? "";
    }

    public function generateStructured(string $model, string $prompt, array $schema): array
{
    $endpoint = $this->apiUrl . $model . ":generateContent?key=" . $this->apiKey;

    $response = Http::timeout(60)->post($endpoint, [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "responseMimeType" => "application/json",
            "responseSchema" => $schema
        ]
    ]);

    if ($response->failed()) {
        throw new \Exception("Erreur Gemini : " . $response->body());
    }

    return $response->json();
}

}
