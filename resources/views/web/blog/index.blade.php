@extends('layouts.public')

@section('meta_title', 'Blog | ' . setting('site_name', 'Highland Properties'))

@section('content')
    <x-web.page-hero label="Blog" title="Notes on buying property well" />

    <section class="u-section">
        <div class="u-container">
            @if ($posts->isEmpty())
                <x-ui.empty-state title="First articles coming soon" />
            @else
                <div class="u-grid">
                    @foreach ($posts as $post)
                        @include('web.partials.post-card', ['post' => $post])
                    @endforeach
                </div>
                <div class="u-mt-40">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>
@endsection
