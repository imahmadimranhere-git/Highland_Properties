@extends('layouts.admin')

@section('title', 'Edit post')

@section('content')
    <x-panel.page-head :title="$post->title" sub="Editing post" />
    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" novalidate>
        @method('PUT')
        @include('admin.posts._form', ['submit' => 'Save changes'])
    </form>
@endsection
