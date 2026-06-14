@extends('market.layout', ['title' => $page->title])

@section('content')
    <article class="zm-container py-10">
        <h1 class="text-3xl font-bold">{{ $page->title }}</h1>
        <div class="prose mt-6 max-w-none">{!! $page->body !!}</div>
    </article>
@endsection
