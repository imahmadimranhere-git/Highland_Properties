@extends('layouts.admin')

@section('title', 'Edit society')

@section('content')
    <x-panel.page-head :title="$society->name" sub="Editing society">
        <x-slot:actions>
            <a href="{{ route('admin.societies.plots.index', $society) }}" class="btn btn--secondary btn--sm">Plot sizes</a>
        </x-slot:actions>
    </x-panel.page-head>

    <form method="POST" action="{{ route('admin.societies.update', $society) }}" enctype="multipart/form-data" novalidate>
        @method('PUT')
        @include('admin.societies._form', ['submit' => 'Save changes'])
    </form>
@endsection
