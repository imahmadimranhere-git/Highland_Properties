@extends('layouts.admin')

@section('title', 'Add society')

@section('content')
    <x-panel.page-head title="Add society" sub="Plot sizes come next." />

    <form method="POST" action="{{ route('admin.societies.store') }}" enctype="multipart/form-data" novalidate>
        @include('admin.societies._form', ['submit' => 'Create society'])
    </form>
@endsection
