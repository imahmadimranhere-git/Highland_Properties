@extends('layouts.public')

@section('meta_title', 'Societies | ' . setting('site_name', 'Highland Properties'))
@section('meta_description', 'Housing societies with plots in Marla and Kanal, marketed by ' . setting('site_name', 'Highland Properties') . '.')

@section('content')
    <x-web.page-hero
        label="Societies"
        title="Plots in schemes we have checked"
        text="Approvals, developer record and plot rates in Marla and Kanal — set out plainly for every society." />

    <section class="u-section">
        <div class="u-container">
            @if ($societies->isEmpty())
                <x-ui.empty-state title="New societies are on the way" text="Call us to hear about launches before they are listed." />
            @else
                <div class="u-grid">
                    @foreach ($societies as $society)
                        <x-web.society-card :society="$society" :eager="$loop->index < 3" />
                    @endforeach
                </div>

                <div class="u-mt-40">{{ $societies->links() }}</div>
            @endif
        </div>
    </section>

    @include('web.partials.contact-strip')
@endsection
