@extends('layouts.admin')

@section('title', 'Add developer')

@section('content')
    <x-panel.page-head title="Add developer" sub="Projects are linked to a developer record." />

    <x-panel.box>
        <form method="POST" action="{{ route('admin.developers.store') }}" enctype="multipart/form-data" novalidate>
            @include('admin.developers._form', ['submit' => 'Create developer'])
        </form>
    </x-panel.box>
@endsection
