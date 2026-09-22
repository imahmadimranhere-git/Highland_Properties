@extends('layouts.public')

@section('meta_title', $post->meta_title ?: $post->title . ' | ' . setting('site_name', 'Highland Properties'))
@section('meta_description', $post->meta_description ?: $post->excerpt)
@if ($post->cover)
    @section('og_image', $post->cover->url)
@endif

@section('content')
    <article>
        <x-web.page-hero label="Blog" :title="$post->title">
            <p class="page-hero__text">
                {{ $post->published_at?->format('d F Y') }}
                @if ($post->author) &middot; {{ $post->author->name }} @endif
            </p>
        </x-web.page-hero>

        <section class="u-section">
            <div class="u-container">
                @if ($post->cover)
                    <img class="post-cover" src="{{ $post->cover->url }}" alt="{{ $post->cover->alt_text ?: $post->title }}"
                         width="{{ $post->cover->width ?? 1600 }}" height="{{ $post->cover->height ?? 900 }}"
                         fetchpriority="high" decoding="async">
                @endif

                <div class="prose prose--narrow">
                    {!! \Illuminate\Support\Str::markdown($post->content ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                </div>
            </div>
        </section>
    </article>

    @if ($related->isNotEmpty())
        <section class="u-section u-bg-off">
            <div class="u-container">
                <x-ui.section-heading label="Keep reading" title="More from the blog" />
                <div class="u-grid">
                    @foreach ($related as $item)
                        @include('web.partials.post-card', ['post' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
