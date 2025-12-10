@extends('layouts.app')

@section('content')
<style>
    .debunk-wrapper {
        max-width: 850px;
        margin: 60px auto;
        padding: 0 20px;
        font-family: "Georgia", serif;
        color: #222;
    }

    .debunk-title {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }

    .debunk-subtitle {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 1.5rem;
        font-style: italic;
    }

    .debunk-body {
        font-size: 1.15rem;
        line-height: 1.75;
    }

    .debunk-body h2 {
        font-size: 1.8rem;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }

    .debunk-body h3 {
        font-size: 1.4rem;
        margin-top: 2rem;
        margin-bottom: 0.8rem;
    }

    .debunk-body p {
        margin-bottom: 1.4rem;
    }

    .debunk-body ul {
        margin-left: 1.8rem;
        margin-bottom: 1.6rem;
    }

    .debunk-body blockquote {
        border-left: 4px solid #ccc;
        padding-left: 1rem;
        margin: 2rem 0;
        font-style: italic;
        color: #555;
    }
</style>

<div class="debunk-wrapper">

    <h1 class="debunk-title">{{ $debunk->title }}</h1>

    @if($debunk->subtitle)
        <p class="debunk-subtitle">{{ $debunk->subtitle }}</p>
    @endif

    <div class="debunk-body">
        {!! $html !!} {{-- contenu HTML généré automatiquement depuis Markdown --}}
    </div>

</div>
@endsection
