@foreach($articles as $article)
    <article class="article">
        <div class="article-meta">
            {{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : 'Non publié' }}
        </div>
        <h2>
            <a href="{{ route('articles.show', $article->id) }}">
                {{ $article->title }}
            </a>
        </h2>
        @if($article->subtitle)
            <p class="article-subtitle">{{ $article->subtitle }}</p>
        @endif
    </article>
@endforeach
