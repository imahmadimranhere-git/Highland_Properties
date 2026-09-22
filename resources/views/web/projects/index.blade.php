@extends('layouts.public')

@section('meta_title', 'Our Projects | ' . setting('site_name', 'Highland Properties'))
@section('meta_description', 'Residential and commercial projects marketed and developed by ' . setting('site_name', 'Highland Properties') . '.')

@section('content')
    <x-web.page-hero
        label="Our projects"
        title="A curated list, not a catalogue"
        text="Every project here has been checked for approvals, developer track record and realistic payment terms." />

    <section class="u-section">
        <div class="u-container">
            @if ($projects->isEmpty())
                <x-ui.empty-state title="New projects are on the way" text="Call us to hear about launches before they are listed." />
            @else
                {{-- Deliberately no search bar or filters: visitors browse the grid. --}}
                <div class="u-grid">
                    @foreach ($projects as $project)
                        <x-web.project-card :project="$project" :eager="$loop->index < 3" />
                    @endforeach
                </div>

                <div class="u-mt-40">{{ $projects->links() }}</div>
            @endif
        </div>
    </section>

    @include('web.partials.contact-strip')
@endsection
