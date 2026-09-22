@extends('layouts.consultant')

@section('title', 'New report')

@section('content')
    <x-panel.page-head title="New report" />
    <x-panel.box>
        <form method="POST" action="{{ route('consultant.reports.store') }}" enctype="multipart/form-data" novalidate>
            @include('consultant.reports._form', ['submit' => 'Save report'])
        </form>
    </x-panel.box>
@endsection
