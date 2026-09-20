@extends('layouts.public')

@section('meta_title', $title)

@section('content')
    <section class="u-section">
        <div class="u-container">
            <x-ui.section-heading :label="'Highland Properties'" :title="$title" />
            <p class="lede">This page is built in step 6. The route and layout are already in place.</p>
        </div>
    </section>
@endsection
