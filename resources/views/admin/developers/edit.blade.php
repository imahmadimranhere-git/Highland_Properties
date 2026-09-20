@extends('layouts.admin')

@section('title', 'Edit developer')

@section('content')
    <x-panel.page-head :title="$developer->name" sub="Editing developer profile" />

    <x-panel.box>
        <form method="POST" action="{{ route('admin.developers.update', $developer) }}" enctype="multipart/form-data" novalidate>
            @method('PUT')
            @include('admin.developers._form', ['submit' => 'Save changes'])
        </form>
    </x-panel.box>
@endsection
