<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MistralService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        # $this->apiKey = config('services.mistral.key');
        $this->apiUrl = rtrim(config('services.mistral.url', 'https://api.mistral.ai/v1'), '/');
    }

    /**
     * Génération de texte simple via Mistral
     */
    public function generateText(string $model, string $prompt, array $options = []): string
    {
        return $this->chat($model, [
            [
                'role' => 'user',
                'content' => $prompt,
            ]
        ], $options);
    }

    /**
     * Génération structurée (texte brut, pas de parsing JSON)
     */
    public function generateStructured(string $model, string $prompt, array $schema = [], array $options = []): array
    {
        $text = $this->chat($model, [
            [
                'role' => 'user',
                'content' => $prompt,
            ]
        ], $options);

        // Retour au format Gemini mais texte brut
        return [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Méthode interne pour chat standard
     */
protected function chat(string $model, array $messages, array $options = []): string
{
    $endpoint = $this->apiUrl . '/chat/completions';

    $body = array_merge([
        'model' => $model,
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 800,
        'response_format' => [
            'type' => 'json_object'
        ]
    ], $options);

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Content-Type'  => 'application/json',
        'Accept'        => 'application/json',
    ])->post($endpoint, $body);

    if ($response->failed()) {
        throw new \Exception("Erreur Mistral : " . $response->body());
    }

    $json = $response->json();

    // Maintenant c’est TOUJOURS du JSON valide
    return $json['choices'][0]['message']['content'] ?? '';
}





}
