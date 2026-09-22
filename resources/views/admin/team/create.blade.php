@extends('layouts.admin')

@section('title', 'Add team member')

@section('content')
    <x-panel.page-head title="Add team member" />
    <x-panel.box>
        <form method="POST" action="{{ route('admin.team.store') }}" enctype="multipart/form-data" novalidate>
            @include('admin.team._form', ['submit' => 'Add member'])
        </form>
    </x-panel.box>
@endsection
