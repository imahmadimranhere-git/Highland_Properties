@extends('layouts.admin')

@section('title', 'Add testimonial')

@section('content')
    <x-panel.page-head title="Add testimonial" />
    <x-panel.box>
        <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data" novalidate>
            @include('admin.testimonials._form', ['submit' => 'Add testimonial'])
        </form>
    </x-panel.box>
@endsection
