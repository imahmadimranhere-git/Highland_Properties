@extends('layouts.public')

@section('meta_title', 'Frequently Asked Questions | ' . setting('site_name', 'Highland Properties'))

@section('content')
    <x-web.page-hero label="FAQ" title="Questions buyers ask us" />

    <section class="u-section">
        <div class="u-container" style="max-width:820px;">
            @forelse ($faqs as $group => $items)
                @if ($faqs->count() > 1)
                    <h2 class="h3 u-mt-40">{{ \Illuminate\Support\Str::headline($group) }}</h2>
                @endif

                {{-- Native <details>: opens and closes with no JavaScript at all. --}}
                <div class="faq-list">
                    @foreach ($items as $faq)
                        <details class="faq">
                            <summary>{{ $faq->question }}</summary>
                            <div class="faq__answer">{!! nl2br(e($faq->answer)) !!}</div>
                        </details>
                    @endforeach
                </div>
            @empty
                <x-ui.empty-state title="No questions yet" />
            @endforelse

            <p class="u-mt-40">Didn't find your answer? <a href="{{ route('contact') }}">Ask us directly.</a></p>
        </div>
    </section>
@endsection
