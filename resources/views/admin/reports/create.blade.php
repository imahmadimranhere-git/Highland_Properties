@extends('layouts.admin')

@section('title', 'Add report')

@section('content')
    <x-panel.page-head title="Add report" />

    <x-panel.box>
        <form method="POST" action="{{ route('admin.reports.store') }}" enctype="multipart/form-data" novalidate>
            @include('admin.reports._form', ['submit' => 'Save report'])
        </form>
    </x-panel.box>
@endsection
