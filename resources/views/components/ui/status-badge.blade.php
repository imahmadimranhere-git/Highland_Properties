@props(['status'])

{{--
    Takes any of the project/lead/report enums from App\Enums.
    Colour and wording both come from the enum, so a status is never
    spelled out by hand in a view.
--}}
<span {{ $attributes->merge(['class' => 'badge ' . $status->badge()]) }}>{{ $status->label() }}</span>
