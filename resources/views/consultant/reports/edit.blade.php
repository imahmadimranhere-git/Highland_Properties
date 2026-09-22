@extends('layouts.consultant')

@section('title', 'Edit report')

@section('content')
    <x-panel.page-head :title="$report->title" sub="Editing draft" />
    <x-panel.box>
        <form method="POST" action="{{ route('consultant.reports.update', $report->id) }}" enctype="multipart/form-data" novalidate>
            @method('PUT')
            @include('consultant.reports._form', ['submit' => 'Save changes'])
        </form>
    </x-panel.box>
@endsection
