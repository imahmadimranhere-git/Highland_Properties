@extends('layouts.public')

@section('meta_title', 'Our Team | ' . setting('site_name', 'Highland Properties'))

@section('content')
    <x-web.page-hero label="Our team" title="People who answer the phone" text="Every inquiry is handled by one of the consultants below, from first call to possession." />

    <section class="u-section">
        <div class="u-container">
            @if ($team->isEmpty())
                <x-ui.empty-state title="Team profiles coming soon" />
            @else
                <div class="team-grid">
                    @foreach ($team as $member)
                        @include('web.partials.team-card', ['member' => $member, 'showBio' => true])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('web.partials.contact-strip')
@endsection
