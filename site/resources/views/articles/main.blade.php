@foreach($articles as $article)
    <article class="article">
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
