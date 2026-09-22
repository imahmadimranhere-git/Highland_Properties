@extends('layouts.admin')

@section('title', 'Edit team member')

@section('content')
    <x-panel.page-head :title="$member->name" sub="Editing team profile" />
    <x-panel.box>
        <form method="POST" action="{{ route('admin.team.update', $member) }}" enctype="multipart/form-data" novalidate>
            @method('PUT')
            @include('admin.team._form', ['submit' => 'Save changes'])
        </form>
    </x-panel.box>
@endsection
