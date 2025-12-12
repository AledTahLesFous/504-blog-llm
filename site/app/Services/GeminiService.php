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

    /**
     * Génère une image avec Gemini Imagen via l'API
     * 
     * @param string $prompt Description de l'image à générer
     * @return string URL de l'image générée en base64
     */
    public function generateImage(string $prompt): string
    {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict?key=" . $this->apiKey;

        $response = Http::timeout(60)->post($endpoint, [
            "instances" => [
                ["prompt" => $prompt]
            ],
            "parameters" => [
                "sampleCount" => 1,
                "aspectRatio" => "16:9"
            ]
        ]);

        if ($response->failed()) {
            \Log::error("Erreur Gemini Imagen: " . $response->body());
            throw new \Exception("Erreur Gemini Image : " . $response->body());
        }

        $result = $response->json();
        
        // L'image est retournée en base64 dans predictions
        $imageBase64 = $result['predictions'][0]['bytesBase64Encoded'] ?? null;
        
        if (!$imageBase64) {
            throw new \Exception("Aucune image générée par Gemini");
        }

        // Retourner l'URL data:image pour utilisation directe
        return 'data:image/png;base64,' . $imageBase64;
    }

}
