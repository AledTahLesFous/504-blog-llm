@foreach($articles as $article)
    <article class="article" style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
        <h2 style="margin-bottom: 0.75rem; line-height: 1.3;">
            <a href="{{ route('articles.show', $article->id) }}" style="color: #111827; text-decoration: none; font-size: 1.5rem; font-weight: 700;">
                {{ $article->title }}
            </a>
        </h2>
        @if($article->subtitle)
            <p class="article-subtitle" style="color: #6b7280; font-size: 1.1rem; line-height: 1.6; margin-bottom: 1.25rem;">{{ $article->subtitle }}</p>
        @endif
        @if($article->image_url)
            <a href="{{ route('articles.show', $article->id) }}" style="display: block; margin-bottom: 1rem;">
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px;">
            </a>
        @endif
    </article>
@endforeach
