@extends('layouts.app')

@section('content')
<style>
    .article-wrapper {
        max-width: 850px;
        margin: 60px auto;
        padding: 0 20px;
        font-family: "Georgia", serif;
        color: #222;
    }

    .article-title {
        font-size: 2.8rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }

    .article-subtitle {
        font-size: 1.3rem;
        color: #666;
        margin-bottom: 1.5rem;
        font-style: italic;
    }

    .article-date {
        color: #999;
        font-size: 0.9rem;
        text-transform: uppercase;
        margin-bottom: 3rem;
        letter-spacing: 1px;
    }

    .article-body {
        font-size: 1.15rem;
        line-height: 1.75;
    }

    .article-body h2 {
        font-size: 1.9rem;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }

    .article-body h3 {
        font-size: 1.5rem;
        margin-top: 2rem;
        margin-bottom: 0.8rem;
    }

    .article-body p {
        margin-bottom: 1.4rem;
    }

    .article-body ul {
        margin-left: 1.8rem;
        margin-bottom: 1.6rem;
    }

    .article-body blockquote {
        border-left: 4px solid #ccc;
        padding-left: 1rem;
        margin: 2rem 0;
        font-style: italic;
        color: #555;
    }
</style>

<div class="article-wrapper">

    <h1 class="article-title">{{ $article->title }}</h1>

    @if($article->subtitle)
        <p class="article-subtitle">{{ $article->subtitle }}</p>
    @endif

    <p class="article-date">
        {{ $article->published_at ? $article->published_at->format('d F Y') : '' }}
    </p>

    <div class="article-body">
        {!! $html !!} {{-- contenu HTML généré automatiquement depuis Markdown --}}
    </div>

     {{-- Lien vers l'article debunk --}}
    @if($debunkLink ?? false)
        <hr style="margin:3rem 0; border-color:#ddd;">
        <p>
            <a href="{{ $debunkLink }}" style="color:#c00; font-weight:600;">
                Voir l'article démystifié (Debunk)
            </a>
        </p>
    @endif

</div>
@endsection
