{{--
    Flash messages are rendered as hidden nodes and picked up by toast.js.
    Keeping the text in the HTML means it is available even if JS fails.
--}}
@foreach (['success' => 'success', 'error' => 'danger', 'status' => 'default'] as $key => $variant)
    @if (session($key))
        <div data-toast data-variant="{{ $variant }}" hidden>{{ session($key) }}</div>
    @endif
@endforeach
