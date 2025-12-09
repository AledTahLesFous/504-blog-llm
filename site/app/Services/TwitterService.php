<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwitterService
{
    private string $bearerToken;
    private string $apiKey;
    private string $apiSecret;
    private string $accessToken;
    private string $accessTokenSecret;

    public function __construct()
    {
        $this->bearerToken = config('services.twitter.bearer_token');
        $this->apiKey = config('services.twitter.api_key');
        $this->apiSecret = config('services.twitter.api_secret');
        $this->accessToken = config('services.twitter.access_token');
        $this->accessTokenSecret = config('services.twitter.access_token_secret');
    }

    /**
     * Poster un tweet
     */
    public function postTweet(string $text): array
    {
        try {
            // Twitter API v2 endpoint
            $url = 'https://api.twitter.com/2/tweets';

            // Utiliser OAuth 1.0a User Context (requis pour poster)
            $oauth = $this->buildOAuthHeader($url, 'POST');

            $response = Http::withHeaders([
                'Authorization' => $oauth,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'text' => $text
            ]);

            if ($response->successful()) {
                Log::info('Tweet posté avec succès', ['response' => $response->json()]);
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Tweet posté avec succès !'
                ];
            }

            Log::error('Erreur Twitter API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la publication : ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Exception Twitter', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Construire le header OAuth 1.0a pour Twitter API v2
     */
    private function buildOAuthHeader(string $url, string $method): string
    {
        $oauth = [
            'oauth_consumer_key' => $this->apiKey,
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_token' => $this->accessToken,
            'oauth_version' => '1.0'
        ];

        // Créer la signature base string
        $baseInfo = $this->buildBaseString($url, $method, $oauth);
        
        // Créer la signing key
        $compositeKey = rawurlencode($this->apiSecret) . '&' . rawurlencode($this->accessTokenSecret);
        
        // Générer la signature
        $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $baseInfo, $compositeKey, true));

        // Construire le header Authorization
        $header = 'OAuth ';
        $values = [];
        foreach($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
        $header .= implode(', ', $values);
        
        return $header;
    }

    /**
     * Construire la base string pour OAuth
     */
    private function buildBaseString(string $url, string $method, array $params): string
    {
        $return = [];
        ksort($params);
        
        foreach($params as $key => $value) {
            $return[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        
        return $method . "&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $return));
    }

    /**
     * Formater un article pour Twitter (max 280 caractères)
     */
    public function formatArticleForTweet(array $article): string
    {
        $title = $article['title'];
        $url = $article['url'];
        $source = strtoupper($article['source']);

        // Format : "Titre... - SOURCE https://url"
        $maxTitleLength = 280 - strlen($url) - strlen($source) - 6; // 6 = " - " + " " + "..."

        if (strlen($title) > $maxTitleLength) {
            $title = substr($title, 0, $maxTitleLength - 3) . '...';
        }

        return "{$title} - {$source}\n{$url}";
    }
}
