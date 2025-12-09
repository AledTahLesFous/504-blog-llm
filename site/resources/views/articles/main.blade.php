@foreach($articles as $article)
    <div class="article">
        <h3>
            <a href="{{ route('articles.show', $article->id) }}">
                {{ $article->title }}
            </a>
        </h3>
        <p>{{ $article->subtitle }}</p> <!-- si tu veux afficher le subtitle aussi -->
    </div>
@endforeach
