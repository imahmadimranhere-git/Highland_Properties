@extends('layouts.admin')

@section('title', 'New post')

@section('content')
    <x-panel.page-head title="New post" />
    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" novalidate>
        @include('admin.posts._form', ['submit' => 'Save post'])
    </form>
@endsection
