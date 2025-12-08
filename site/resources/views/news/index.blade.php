<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités - NewsAPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b mb-8">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-gray-900">Actualités françaises</h1>
            <p class="text-gray-600 text-sm mt-1">Flux RSS - BFMTV, Le Monde, France Info, Le Parisien, CNews</p>
        </div>
    </header>

    <!-- Contenu -->
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($feeds as $article)
            <article class="bg-white rounded-lg shadow-sm hover:shadow-md transition">
                <a href="{{ route('news.article', $article['id']) }}" class="block">
                    @if(isset($article['image']) && $article['image'])
                    <img src="{{ $article['image'] }}" 
                         alt="{{ $article['title'] }}" 
                         class="w-full h-48 object-cover rounded-t-lg"
                         onerror="this.style.display='none'">
                    @endif
                    
                    <div class="p-4">
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $article['source'] === 'bfmtv' ? 'bg-red-100 text-red-800' : ($article['source'] === 'lemonde' ? 'bg-blue-100 text-blue-800' : ($article['source'] === 'leparisien' ? 'bg-purple-100 text-purple-800' : ($article['source'] === 'cnews' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'))) }}">
                            {{ $article['source'] === 'leparisien' ? 'LE PARISIEN' : strtoupper($article['source']) }}
                        </span>
                        
                        <h3 class="font-semibold text-gray-900 mb-2 mt-2 line-clamp-2">
                            {{ $article['title'] }}
                        </h3>
                        
                        <p class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($article['date'])->locale('fr')->diffForHumans() }}
                        </p>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </div>

    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="max-w-6xl mx-auto px-4 text-center text-sm text-gray-600">
            <p>Agrégateur RSS - BFMTV, Le Monde, France Info, Le Parisien, CNews</p>
            <p class="mt-1 text-xs text-gray-500">Conformité légale - Liens vers sources originales</p>
        </div>
    </footer>

    <script>
        // Fonction pour recharger les articles
        async function refreshArticles() {
            try {
                const response = await fetch(window.location.href);
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extraire la nouvelle grille d'articles
                const newGrid = doc.querySelector('.grid');
                const currentGrid = document.querySelector('.grid');
                
                if (newGrid && currentGrid) {
                    currentGrid.innerHTML = newGrid.innerHTML;
                }
            } catch (error) {
                console.error('Erreur lors du rafraîchissement:', error);
            }
        }

        // Rafraîchir toutes les 5 minutes
        setInterval(refreshArticles, 300000); // 300000ms = 5 minutes
    </script>
</body>
</html>
