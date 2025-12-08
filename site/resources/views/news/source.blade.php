<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($source) }} - Actualités</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b mb-8">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <a href="/" class="text-blue-600 hover:text-blue-700 text-sm mb-2 inline-block">← Retour</a>
            <h1 class="text-2xl font-bold text-gray-900">{{ strtoupper($source) }}</h1>
            <p class="text-gray-600 text-sm mt-1">{{ count($feedData) }} articles</p>
        </div>
    </header>

    <!-- Articles -->
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($feedData as $article)
            <article class="bg-white rounded-lg shadow-sm hover:shadow-md transition">
                <a href="{{ route('news.article', $article['id']) }}" class="block">
                    @if(isset($article['image']) && $article['image'])
                    <img src="{{ $article['image'] }}" 
                         alt="{{ $article['title'] }}" 
                         class="w-full h-48 object-cover rounded-t-lg"
                         onerror="this.style.display='none'">
                    @endif
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
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

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="max-w-6xl mx-auto px-4 text-center text-sm text-gray-600">
            <p>Agrégateur RSS - Liens vers sources originales</p>
        </div>
    </footer>
</body>
</html>
