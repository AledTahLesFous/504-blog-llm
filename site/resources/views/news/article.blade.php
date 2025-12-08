<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article['title'] }} - Actualités</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header Simple -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <a href="/" class="text-blue-600 hover:text-blue-700 text-sm">← Retour</a>
        </div>
    </header>

    <!-- Article -->
    <article class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <!-- En-tête -->
            <div class="mb-6">
                <span class="inline-block px-3 py-1 {{ $article['source'] === 'bfmtv' ? 'bg-red-100 text-red-700' : ($article['source'] === 'lemonde' ? 'bg-blue-100 text-blue-700' : ($article['source'] === 'leparisien' ? 'bg-purple-100 text-purple-700' : ($article['source'] === 'cnews' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'))) }} text-sm rounded-full mb-4">
                    {{ $article['source'] === 'leparisien' ? 'LE PARISIEN' : strtoupper($article['source']) }}
                </span>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-3">
                    {{ $article['title'] }}
                </h1>

                @if(isset($article['author']) && $article['author'])
                <p class="text-sm text-gray-600 mb-1">
                    Par {{ $article['author'] }}
                </p>
                @endif

                @if($article['date'])
                <p class="text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($article['date'])->locale('fr')->isoFormat('LL') }}
                </p>
                @endif
            </div>

            <!-- Image -->
            @if(isset($article['image']) && $article['image'])
            <div class="mb-6">
                <img src="{{ $article['image'] }}" 
                     alt="{{ $article['title'] }}" 
                     class="w-full rounded-lg"
                     onerror="this.style.display='none'">
            </div>
            @endif

            <!-- Contenu -->
            <div class="prose prose-lg max-w-none text-gray-800">
                <div class="text-lg leading-relaxed">
                    {{ $article['content'] }}
                </div>
            </div>

            <!-- Lien vers l'article original -->
            <div class="mt-8 pt-6 border-t">
                <a href="{{ $article['url'] }}" 
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center text-blue-600 hover:text-blue-700">
                    Lire l'article complet sur {{ $article['source'] }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>
    </article>
</body>
</html>
