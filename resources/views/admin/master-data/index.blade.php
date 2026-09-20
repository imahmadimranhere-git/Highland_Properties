@extends('layouts.admin')

@section('title', 'Master Data')

@section('content')
    <x-panel.page-head
        title="Master Data"
        sub="Cities, locations, project types and amenities used across every project." />

    @php
        $tabs = [
            'cities' => 'Cities',
            'locations' => 'Locations / Societies',
            'types' => 'Project Types',
            'amenities' => 'Amenities',
        ];
    @endphp

    <nav class="tab-bar" aria-label="Master data sections">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.master-data.index', ['tab' => $key]) }}"
               class="tab-bar__link {{ $tab === $key ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>

    @include('admin.master-data.' . $tab)
@endsection
