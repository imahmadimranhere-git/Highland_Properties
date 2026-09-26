@extends('layouts.admin')

@section('title', 'Announcement ticker')

@section('content')
    <x-panel.page-head
        title="Announcement ticker"
        sub="The red strip between the menu and the banner. Each announcement carries its own link." />

    @if ($errors->any())
        <div class="alert alert--danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <x-panel.box title="Add an announcement">
        <form method="POST" action="{{ route('admin.tickers.store') }}" novalidate>
            @include('admin.tickers._fields', [
                'ticker' => new \App\Models\Ticker(['is_active' => true]),
                'prefix' => 'new',
                'submit' => 'Add announcement',
            ])
        </form>
    </x-panel.box>

    @forelse ($tickers as $ticker)
        <x-panel.box :title="$ticker->message">
            <x-slot:actions>
                <span class="badge {{ $ticker->is_active ? 'badge-success' : 'badge-muted' }}">
                    {{ $ticker->is_active ? 'Live' : 'Hidden' }}
                </span>

                <form method="POST" action="{{ route('admin.tickers.toggle', $ticker) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button class="btn btn--secondary btn--sm">{{ $ticker->is_active ? 'Hide' : 'Show' }}</button>
                </form>

                <x-ui.delete-form
                    :action="route('admin.tickers.destroy', $ticker)"
                    :confirm="'Delete this announcement?'" />
            </x-slot:actions>

            <form method="POST" action="{{ route('admin.tickers.update', $ticker) }}" novalidate>
                @method('PUT')
                @include('admin.tickers._fields', [
                    'ticker' => $ticker,
                    'prefix' => 'tk' . $ticker->id,
                    'submit' => 'Save',
                ])
            </form>
        </x-panel.box>
    @empty
        <x-panel.box>
            <x-ui.empty-state
                title="No announcements yet"
                text="Add one above — a launch, a price revision, a booking deadline. The strip only appears on the website while at least one is live." />
        </x-panel.box>
    @endforelse
@endsection
