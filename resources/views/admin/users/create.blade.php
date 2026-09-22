@extends('layouts.admin')

@section('title', 'Add user')

@section('content')
    <x-panel.page-head title="Add user" sub="Share the password with them in person or by phone, not by email." />

    <x-panel.box>
        <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
            @include('admin.users._form', ['submit' => 'Create account'])
        </form>
    </x-panel.box>
@endsection
