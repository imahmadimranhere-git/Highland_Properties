@extends('layouts.admin')

@section('title', 'Content')

@section('content')
    <x-panel.page-head title="Content" sub="Blog posts, client testimonials and frequently asked questions." />

    <nav class="tab-bar" aria-label="Content sections">
        <a href="{{ route('admin.content.index', ['tab' => 'posts']) }}" class="tab-bar__link {{ $tab === 'posts' ? 'is-active' : '' }}">Blog</a>
        <a href="{{ route('admin.content.index', ['tab' => 'testimonials']) }}" class="tab-bar__link {{ $tab === 'testimonials' ? 'is-active' : '' }}">
            Testimonials
            @if (($pendingCount ?? 0) > 0)
                <span class="badge badge-gold" style="margin-left:6px;">{{ $pendingCount }} pending</span>
            @endif
        </a>
        <a href="{{ route('admin.content.index', ['tab' => 'faqs']) }}" class="tab-bar__link {{ $tab === 'faqs' ? 'is-active' : '' }}">FAQs</a>
    </nav>

    @include('admin.content.' . $tab)
@endsection
