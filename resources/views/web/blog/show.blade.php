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

                {{--
                    Nothing renders when either field is empty, so older posts
                    are unaffected. The text is escaped by Blade and the address
                    was checked on save, so neither can carry a script.
                --}}
                @if ($post->hasExternalLink())
                    <aside class="post-link prose--narrow">
                        <p class="post-link__label">Read more</p>

                        <a href="{{ $post->external_link_url }}" target="_blank" rel="noopener noreferrer"
                           class="post-link__anchor">
                            <span>{{ $post->external_link_text }}</span>

                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                 stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 4h6v6M20 4 10 14M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>
                            </svg>
                        </a>

                        <span class="post-link__host">{{ parse_url($post->external_link_url, PHP_URL_HOST) }}</span>
                    </aside>
                @endif
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
