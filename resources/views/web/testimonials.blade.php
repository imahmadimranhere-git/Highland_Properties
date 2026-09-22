@extends('layouts.public')

@section('meta_title', 'Testimonials | ' . setting('site_name', 'Highland Properties'))

@section('content')
    <x-web.page-hero label="Testimonials" title="In our clients' words" />

    <section class="u-section">
        <div class="u-container">
            @if ($testimonials->isEmpty())
                <x-ui.empty-state title="No testimonials yet" />
            @else
                <div class="u-grid">
                    @foreach ($testimonials as $t)
                        @include('web.partials.testimonial', ['t' => $t])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('web.partials.contact-strip')
@endsection
