<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AIController;
use Illuminate\Support\Facades\File;

class AutoProcessArticles extends Command
{
    protected $signature = 'articles:auto';
    protected $description = 'Choisir un article et lancer la réécriture + évaluation automatiquement';
    protected $logFile;

    public function __construct()
    {
        parent::__construct();
        $this->logFile = storage_path('logs/log.txt');
    }

    protected function writeLog(string $message)
    {
        $line = "[" . now()->toDateTimeString() . "] " . $message . PHP_EOL;
        File::append($this->logFile, $line);
    }

    public function handle(NewsController $news, AIController $ai)
    {
        $this->writeLog("=== Auto Article Processor launched ===");

        try {
            // 1. CHOOSE
            $choose = $news->apiOne($ai);
            $this->writeLog("Article choisi : OK");
        } catch (\Exception $e) {
            $this->writeLog("Erreur chooseArticle : " . $e->getMessage());
            return Command::FAILURE;
        }

        try {
            // 2. EVAL + REWRITE
            $eval = $news->Main($ai);
            $this->writeLog("Rewrite + Eval : OK");
        } catch (\Exception $e) {
            $this->writeLog("Erreur rewrite/eval : " . $e->getMessage());
            return Command::FAILURE;
        }

        try {
            // 3. DEBUNK
            $debunk = $news->apiDebunkOne($ai);
            $this->writeLog("Debunk : OK");
        } catch (\Exception $e) {
            $this->writeLog("Erreur debunk : " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->writeLog("=== Fin du traitement ===");

        return Command::SUCCESS;
    }
}
