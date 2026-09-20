@extends('layouts.admin')

@section('title', $title)

@section('content')
    <x-panel.page-head :title="$title" sub="Module is built in step 4." />

    <x-panel.box>
        <x-ui.empty-state
            title="Not built yet"
            text="The route, sidebar entry and layout are ready. The controller, views and validation come next." />
    </x-panel.box>
@endsection
