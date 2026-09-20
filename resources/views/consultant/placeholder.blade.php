@extends('layouts.consultant')

@section('title', $title)

@section('content')
    <x-panel.page-head :title="$title" sub="Module is built in step 5." />

    <x-panel.box>
        <x-ui.empty-state title="Not built yet" text="Route and layout are ready." />
    </x-panel.box>
@endsection
