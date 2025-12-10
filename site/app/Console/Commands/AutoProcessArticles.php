<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AIController;

class AutoProcessArticles extends Command
{
    protected $signature = 'articles:auto';
    protected $description = 'Choisir un article et lancer la réécriture + évaluation automatiquement';

    public function handle(NewsController $news, AIController $ai)
    {
        $this->info("=== Auto Article Processor launched ===");

        try {
            // 1. CHOOSE
            $choose = $news->apiOne($ai);
            $this->info("Article choisi : OK");

        } catch (\Exception $e) {
            $this->error("Erreur chooseArticle : " . $e->getMessage());
            return Command::FAILURE;
        }

        try {
            // 2. EVAL + REWRITE
            $eval = $news->Main($ai);
            $this->info("Rewrite + Eval : OK");

        } catch (\Exception $e) {
            $this->error("Erreur rewrite/eval : " . $e->getMessage());
            return Command::FAILURE;
        }
        try {
            // 3. DEBUNK
            $eval = $news->apiDebunkOne($ai);
            $this->info("Debunk : OK");

        } catch (\Exception $e) {
            $this->error("Erreur debunk : " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("=== Fin du traitement ===");
        return Command::SUCCESS;
    }
}
