@extends('layouts.admin')

@section('title', 'Website Settings')

@section('content')
    <x-panel.page-head title="Website Settings" sub="Changes go live on the website as soon as you save." />

    @php
        $tabs = [
            'general' => 'General',
            'contact' => 'Contact',
            'social' => 'Social links',
            'seo' => 'SEO',
            'video' => 'Video',
            'about' => 'About page',
            'slider' => 'Home slider',
        ];
    @endphp

    <nav class="tab-bar" aria-label="Settings sections">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.settings.index', ['tab' => $key]) }}"
               class="tab-bar__link {{ $tab === $key ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>

    @if ($errors->any())
        <div class="alert alert--danger">{{ $errors->first() }}</div>
    @endif

    @if ($tab === 'slider')
        @include('admin.settings.slider')
    @else
        <x-panel.box>
            <form method="POST" action="{{ route('admin.settings.update', $tab) }}" enctype="multipart/form-data" novalidate>
                @csrf @method('PUT')
                @include('admin.settings.' . $tab)
                <button type="submit" class="btn btn--primary u-mt-8">Save {{ strtolower($tabs[$tab]) }}</button>
            </form>
        </x-panel.box>
    @endif
@endsection
