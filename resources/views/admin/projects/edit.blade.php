@extends('layouts.admin')

@section('title', 'Edit project')

@section('content')
    <x-panel.page-head :title="$project->name" sub="Editing project">
        <x-slot:actions>
            <a href="{{ route('admin.projects.categories.index', $project) }}" class="btn btn--secondary btn--sm">Unit categories</a>
            <a href="{{ route('admin.projects.updates.index', $project) }}" class="btn btn--secondary btn--sm">Development updates</a>
        </x-slot:actions>
    </x-panel.page-head>

    <form method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data" novalidate>
        @method('PUT')
        @include('admin.projects._form', ['submit' => 'Save changes'])
    </form>
@endsection
