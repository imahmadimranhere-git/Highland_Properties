@extends('layouts.admin')

@section('title', 'Edit testimonial')

@section('content')
    <x-panel.page-head :title="$testimonial->name" sub="Editing testimonial" />
    <x-panel.box>
        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data" novalidate>
            @method('PUT')
            @include('admin.testimonials._form', ['submit' => 'Save changes'])
        </form>
    </x-panel.box>
@endsection
