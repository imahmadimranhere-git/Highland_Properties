@extends('layouts.admin')

@section('title', 'Edit report')

@section('content')
    <x-panel.page-head :title="$report->title" sub="Editing report" />

    <x-panel.box>
        <form method="POST" action="{{ route('admin.reports.update', $report) }}" enctype="multipart/form-data" novalidate>
            @method('PUT')
            @include('admin.reports._form', ['submit' => 'Save changes'])
        </form>
    </x-panel.box>
@endsection
