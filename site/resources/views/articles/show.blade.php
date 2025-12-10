@extends('layouts.app')

@section('content')
<style>
    .article-wrapper {
        max-width: 750px;
        margin: 0 auto;
        padding: 3rem 20px;
    }

    .article-meta {
        font-size: 0.85rem;
        color: #999;
        margin-bottom: 1rem;
        font-family: Arial, sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .article-title {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1rem;
        color: #000;
    }

    .article-subtitle {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 2rem;
        line-height: 1.4;
        font-family: Arial, sans-serif;
    }

    .article-divider {
        border: 0;
        border-top: 1px solid #ddd;
        margin: 2rem 0;
    }

    .article-body {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }

    .article-body h1,
    .article-body h2 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: #000;
    }

    .article-body h3 {
        font-size: 1.4rem;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 0.8rem;
        color: #000;
    }

    .article-body p {
        margin-bottom: 1.2rem;
    }

    .article-body ul,
    .article-body ol {
        margin-left: 2rem;
        margin-bottom: 1.5rem;
    }

    .article-body li {
        margin-bottom: 0.5rem;
    }

    .article-body blockquote {
        border-left: 3px solid #000;
        padding-left: 1.5rem;
        margin: 2rem 0;
        font-style: italic;
        color: #666;
    }

    .article-body a {
        color: #000;
        text-decoration: underline;
    }

    .article-body a:hover {
        color: #666;
    }

    .debunk-section {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 2px solid #000;
    }

    .debunk-link {
        display: inline-block;
        padding: 0.8rem 1.5rem;
        background: #000;
        color: #fff;
        text-decoration: none;
        font-family: Arial, sans-serif;
        font-size: 0.95rem;
        font-weight: 600;
        transition: background 0.2s;
    }

    .debunk-link:hover {
        background: #333;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 2rem;
        color: #666;
        text-decoration: none;
        font-family: Arial, sans-serif;
        font-size: 0.9rem;
    }

    .back-link:hover {
        color: #000;
    }

    @media (max-width: 768px) {
        .article-title {
            font-size: 2rem;
        }

        .article-body {
            font-size: 1rem;
        }
    }
</style>

<div class="article-wrapper">
    
    <a href="/" class="back-link">← Retour aux articles</a>


    <h1 class="article-title">{{ $article->title }}</h1>

    @if($article->subtitle)
        <p class="article-subtitle">{{ $article->subtitle }}</p>
    @endif

    <hr class="article-divider">

    <div class="article-body">
        {!! $html !!}
    </div>

    @if($debunkLink ?? false)
        <div class="debunk-section">
            <a href="{{ $debunkLink }}" class="debunk-link">
                Voir l'article démystifié (Debunk)
            </a>
        </div>
    @endif

</div>
@endsection
