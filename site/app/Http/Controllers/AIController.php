<?php

namespace App\Http\Controllers;
use App\Services\GeminiService;
use App\Services\MistralService;


class AIController
{
    protected GeminiService $gemini;
    protected MistralService $mistral;

    public function __construct(GeminiService $gemini, MistralService $mistral)
    {
        $this->gemini = $gemini;
        $this->mistral = $mistral;
    }

    protected function callGemini(string $prompt, array $schema): array
    {
        $response = $this->gemini->generateStructured(
            model: "gemini-2.5-flash",
            prompt: $prompt,
            schema: $schema
        );

        $raw = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$raw) {
            throw new \Exception("Réponse inattendue de Gemini (aucun texte renvoyé)");
        }

        $data = json_decode($raw, true);
        if (!$data) {
            throw new \Exception("JSON invalide renvoyé par Gemini : " . json_last_error_msg());
        }

        return $data;
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
    \"id\": \"...\",
    \"title\": \"...\",
    \"subtitle\": \"...\",
    \"content\": \"...\",
    \"url\": \"...\"
    }

    NE RENVOIE RIEN D'AUTRE.
    ";


    $schema = [
        "type" => "object",
        "properties" => [
            "title" => ["type" => "string"],
            "subtitle" => ["type" => "string"],
            "content" => ["type" => "string"],
            "url" => ["type" => "string"]
        ],
        "required" => ["title", "subtitle", "content", "url"]
    ];


        $data = $this->callGemini($prompt, $schema);

        // fallback si Gemini n’a pas renvoyé d’URL
        if (!isset($data['url'])) {
            $data['url'] = $articles[0]['url'] ?? '';
        }

        return $data;
    }


    public function rewriteArticle(array $article): array
    {
        $prompt = "
        Voici un article original :

        " . json_encode($article, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "

        Réécris-le avec les consignes suivantes :
        - Titre : choc et ambigu et provoquant. Essaye d'etre le plus dans l'ambiguité provocatrice et le complotisme sans pour autant etre dans le factuellement faux.
        Il faut alerter, et rendre incompréhensible certains termes pour rendre le titre complexe pour brainfuck les gens.
        Fait également des liens avec des mot techniques, complexes, mais qui signifient la meme chose, MAIS N'INVENTE PAS DE MOTS !
        Il faut que les gens pensent que ca fasse allusion a des théorie du complot.
        Exemple : 'Présence d'OXYDE DE DIHYDROGÈNE dans l'eau !'
        - Accroche (subtitle) : alarmiste et provoquant, il faut que ca ALERTE
        - Corps : factuel et précis
        - Le corps doit rester fidèle aux informations originales mais reformulé
        - Le corps doit etre en format Markdown
        - Renvoie uniquement un JSON structuré avec les champs :
        {
        \"title\": \"...\",
        \"subtitle\": \"...\",
        \"content\": \"Markdown...\"
        }
        ";

        $schema = [
            "type" => "object",
            "properties" => [
                "title" => ["type" => "string"],
                "subtitle" => ["type" => "string"],
                "content" => ["type" => "string"],
                "url" => ["type" => "string"]
            ],
            "required" => ["title", "subtitle", "content", "url"]
        ];


        $data = $this->callGemini($prompt, $schema);

        // fallback si Gemini n’a pas renvoyé d’URL
        if (!isset($data['url'])) {
            $data['url'] = $articles[0]['url'] ?? '';
        }

        return $data;

    }


    public function evaluateArticle(array $article): array
    {
        $prompt = "
        Évalue objectivement cet article selon plusieurs critères.

        ARTICLE :
        " . json_encode($article, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "

        Tu dois analyser et renvoyer un JSON strict (rien d’autre) :
        {
        \"ragebait\": 0.0 à 1.0,
        \"clarte\": 0.0 à 1.0,
        \"coherence\": 0.0 à 1.0,
        \"impact\": 0.0 à 1.0,
        \"score_global\": moyenne des 4 critères,
        \"commentaire\": \"analyse concise\"
        }
        ";  

        $schema = [
            "type" => "object",
            "properties" => [
                "ragebait" => ["type" => "number"],
                "clarte" => ["type" => "number"],
                "coherence" => ["type" => "number"],
                "impact" => ["type" => "number"],
                "score_global" => ["type" => "number"],
                "commentaire" => ["type" => "string"]
            ],
            "required" => ["ragebait", "clarte", "coherence", "impact", "score_global", "commentaire"]
        ];


       return $this->callGemini($prompt, $schema);

    }


    public function twitterPost(array $article): array
    {
        $title = $article['title'] ?? '';
        $subtitle = $article['subtitle'] ?? '';
        $url = $article['url'] ?? '';

        if (!$title || !$url) {
            return [
                'success' => false,
                'message' => 'Article incomplet pour Twitter'
            ];
        }

        // Prompt IA pour générer le tweet
        $prompt = "
    Voici un article avec titre et sous-titre :

    Titre : {$title}
    Sous-titre : {$subtitle}
    URL : {$url}

    Génère un texte de tweet basé sur cet article :
    - Ne modifie pas le titre ni le sous-titre.
    - Tweet concis et impactant.
    - Ajoute l'URL complète à la fin.
    - Maximum 280 caractères (en comptant l'URL).
    - Renvoie uniquement le texte du tweet.
    ";

        $schema = [
            "type" => "object",
            "properties" => [
                "tweet" => ["type" => "string"]
            ],
            "required" => ["tweet"]
        ];

        try {
            $data = $this->callGemini($prompt, $schema);

            // Laisser l'URL complète intacte, tronquer seulement le texte si nécessaire
            $tweetText = $data['tweet'] ?? "{$title} - {$subtitle} {$url}";

            // Calculer le surplus si le tweet dépasse 280 caractères
            if (mb_strlen($tweetText) > 280) {
                // On garde la fin pour le lien complet
                $tweetText = mb_substr($tweetText, 0, 280 - mb_strlen($url) - 1) . ' ' . $url;
            }

            return [
                'success' => true,
                'tweet' => $tweetText
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la génération du tweet : ' . $e->getMessage()
            ];
        }
    }

    public function debunk(array $article): array
{
    $title = $article['title'] ?? '';
    $subtitle = $article['subtitle'] ?? '';
    $content = $article['content'] ?? '';
    $url = $article['url'] ?? '';


    $prompt = "
Voici un article alarmiste ou sensationnaliste :

Titre : {$title}
Sous-titre : {$subtitle}
Contenu : {$content}

Rédige un article de démystification (debunk) :
- Explique calmement les points exagérés ou alarmistes.
- Clarifie les termes techniques ou scientifiques.
- Rassure le lecteur sur ce qui est normal ou attendu.
- Garde un ton factuel, pédagogique et accessible.
- Renvoie uniquement un JSON structuré avec :
{
  \"title\": \"Titre démystifié\",
  \"subtitle\": \"Sous-titre explicatif\",
  \"content\": \"Texte en Markdown...\",
  \"url\": \"...\"
}
";

    $schema = [
        "type" => "object",
        "properties" => [
            "title" => ["type" => "string"],
            "subtitle" => ["type" => "string"],
            "content" => ["type" => "string"],
            "url" => ["type" => "string"]

        ],
        "required" => ["title", "subtitle", "content", "url"]
    ];

    $data = $this->callGemini($prompt, $schema);

    // fallback si l'URL n'est pas renvoyée
    $data['url'] = $url;


    return $data;
}


}
