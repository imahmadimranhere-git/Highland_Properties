@extends('layouts.admin')

@section('title', 'Add project')

@section('content')
    <x-panel.page-head title="Add project" sub="Unit categories and payment plans come next." />

    <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" novalidate>
        @include('admin.projects._form', ['submit' => 'Create project'])
    </form>
@endsection
