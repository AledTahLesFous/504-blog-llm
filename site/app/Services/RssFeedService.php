<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RssFeedService
{
    private array $feeds = [
        'franceinfo' => 'https://www.franceinfo.fr/decouverte.rss',
        'lemonde' => 'https://www.lemonde.fr/sciences/rss_full.xml',
        'bfmtv' => 'https://www.bfmtv.com/rss/sciences/',
        'leparisien' => 'https://feeds.leparisien.fr/leparisien/rss/futurs',
        'cnews' => 'https://www.cnews.fr/rss/categorie/science',
    ];

    /**
     * Récupère tous les articles de toutes les sources
     */
    public function getAllArticles(int $limit = 15): array
    {
        $allArticles = [];
        
        foreach ($this->feeds as $sourceName => $url) {
            $articles = $this->getFeedArticles($url, $sourceName, 10);
            $allArticles = array_merge($allArticles, $articles);
        }

        // Trier par date
        usort($allArticles, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($allArticles, 0, $limit);
    }

    /**
     * Récupère les articles d'une source spécifique
     */
    public function getArticlesBySource(string $source, int $limit = 20): array
    {
        if (!isset($this->feeds[$source])) {
            return [];
        }

        return $this->getFeedArticles($this->feeds[$source], $source, $limit);
    }

    /**
     * Récupère un article par son ID
     */
    public function getArticleById(string $id): ?array
    {
        $allArticles = $this->getAllArticles(100);
        
        foreach ($allArticles as $article) {
            if ($article['id'] === $id) {
                return $article;
            }
        }

        return null;
    }

    /**
     * Parse un flux RSS
     */
    private function getFeedArticles(string $url, string $sourceName, int $limit): array
    {
        try {
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                Log::error("Failed to fetch RSS feed: {$url}");
                return [];
            }

            $xml = simplexml_load_string($response->body());
            
            if ($xml === false) {
                Log::error("Failed to parse RSS feed: {$url}");
                return [];
            }

            $articles = [];
            $count = 0;

            foreach ($xml->channel->item as $item) {
                if ($count >= $limit) {
                    break;
                }

                $article = $this->parseArticle($item, $sourceName);
                if ($article) {
                    $articles[] = $article;
                    $count++;
                }
            }

            return $articles;

        } catch (\Exception $e) {
            Log::error("Exception fetching RSS feed {$url}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse un article RSS
     */
    private function parseArticle($item, string $sourceName): ?array
    {
        try {
            $title = (string) $item->title;
            $url = (string) $item->link;
            
            if (empty($title) || empty($url)) {
                return null;
            }

            // Extraire le contenu (prioriser content:encoded, sinon description)
            $content = '';
            $description = '';
            $namespaces = $item->getNamespaces(true);
            
            // Récupérer description
            $description = strip_tags((string) $item->description);
            
            // Récupérer content:encoded si disponible
            if (isset($namespaces['content'])) {
                $contentNs = $item->children($namespaces['content']);
                $contentEncoded = strip_tags((string) $contentNs->encoded);
                
                // Utiliser content:encoded seulement s'il est différent et plus long que description
                if (!empty($contentEncoded) && $contentEncoded !== $description && strlen($contentEncoded) > strlen($description)) {
                    $content = $contentEncoded;
                } else {
                    $content = $description;
                }
            } else {
                $content = $description;
            }

            // Extraire l'image
            $image = null;
            if (isset($namespaces['media'])) {
                $mediaNs = $item->children($namespaces['media']);
                if (isset($mediaNs->content)) {
                    $image = (string) $mediaNs->content->attributes()->url;
                } elseif (isset($mediaNs->thumbnail)) {
                    $image = (string) $mediaNs->thumbnail->attributes()->url;
                }
            }
            
            if (!$image && isset($item->enclosure)) {
                $image = (string) $item->enclosure->attributes()->url;
            }

            return [
                'id' => md5($url),
                'title' => $title,
                'content' => $content,
                'description' => $description,
                'url' => $url,
                'image' => $image,
                'date' => isset($item->pubDate) ? date('Y-m-d H:i:s', strtotime((string) $item->pubDate)) : date('Y-m-d H:i:s'),
                'source' => $sourceName,
            ];

        } catch (\Exception $e) {
            Log::error("Exception parsing RSS item: " . $e->getMessage());
            return null;
        }
    }
}
