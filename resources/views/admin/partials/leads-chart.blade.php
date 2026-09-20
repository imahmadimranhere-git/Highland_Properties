{{-- Included with: @include('admin.partials.leads-chart', ['series' => $monthly]) --}}
@php
    $max = max(1, collect($series)->max('value'));
    $barW = 100 / max(1, count($series));
@endphp

{{--
    Monthly leads chart drawn as inline SVG from server data.
    A charting library would be ~60KB of JavaScript for twelve bars, so the
    bars are plain <rect> elements scaled against the highest month.
--}}
<div class="chart">
    <svg viewBox="0 0 100 46" preserveAspectRatio="none" role="img"
         aria-label="Leads received per month over the last twelve months">
        @foreach ($series as $i => $point)
            @php
                $h = $point['value'] / $max * 38;
            @endphp
            <rect x="{{ $i * $barW + $barW * 0.22 }}"
                  y="{{ 40 - $h }}"
                  width="{{ $barW * 0.56 }}"
                  height="{{ max($h, 0.6) }}"
                  rx="0.4"
                  fill="{{ $i === count($series) - 1 ? 'var(--gold-500)' : 'var(--navy-800)' }}">
                <title>{{ $point['label'] }}: {{ $point['value'] }} leads</title>
            </rect>
        @endforeach
        <line x1="0" y1="40.4" x2="100" y2="40.4" stroke="var(--border)" stroke-width="0.4"/>
    </svg>

    <div class="chart__labels">
        @foreach ($series as $point)
            <span>{{ $point['label'] }}</span>
        @endforeach
    </div>
</div>
