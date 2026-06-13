@extends('market.layout', ['title' => $page->title])

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">{{ $page->title }}</h1>
        <div class="prose mt-6 max-w-none whitespace-pre-line">{{ $page->body }}</div>
    </article>
@endsection
