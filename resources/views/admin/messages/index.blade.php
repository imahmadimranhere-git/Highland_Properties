@extends('layouts.admin')

@section('title', 'Inbox')

@section('content')
    <x-panel.page-head title="Inbox" :sub="$unreadCount . ' unread · messages from the Contact Us page'">
        <x-slot:actions>
            @if (request()->boolean('unread'))
                <a href="{{ route('admin.messages.index') }}" class="btn btn--secondary btn--sm">Show all</a>
            @else
                <a href="{{ route('admin.messages.index', ['unread' => 1]) }}" class="btn btn--secondary btn--sm">Unread only</a>
            @endif
        </x-slot:actions>
    </x-panel.page-head>

    @forelse ($messages as $message)
        <x-panel.box :title="($message->subject ?: 'No subject') . ' — ' . $message->name">
            <x-slot:actions>
                <span class="text-muted-hp" style="font-size:.8125rem;">{{ $message->created_at->format('d M Y, h:i A') }}</span>
                <form method="POST" action="{{ route('admin.messages.toggle', $message) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn--secondary btn--sm">{{ $message->is_read ? 'Mark unread' : 'Mark read' }}</button>
                </form>
                <x-ui.delete-form :action="route('admin.messages.destroy', $message)" confirm="Delete this message?" />
            </x-slot:actions>

            <p class="u-mb-24" style="{{ $message->is_read ? '' : 'font-weight:600;' }}">{!! nl2br(e($message->message)) !!}</p>

            <div class="u-flex u-gap-8 u-wrap">
                @if ($message->phone)
                    <a href="tel:{{ $message->phone }}" class="btn btn--secondary btn--sm"><x-ui.icon name="phone" :size="14" /> {{ $message->phone }}</a>
                @endif
                @if ($message->email)
                    <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . ($message->subject ?: 'Your message')) }}" class="btn btn--secondary btn--sm">
                        <x-ui.icon name="mail" :size="14" /> {{ $message->email }}
                    </a>
                @endif
            </div>
        </x-panel.box>
    @empty
        <x-panel.box><x-ui.empty-state title="No messages" /></x-panel.box>
    @endforelse

    {{ $messages->links() }}
@endsection
