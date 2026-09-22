{{--
    Generic inline SVG bar chart.
    @include('admin.partials.bar-chart', ['series' => [...], 'format' => 'money'|'count', 'label' => '...'])
--}}
@php
    $max = max(1, collect($series)->max('value'));
    $barW = 100 / max(1, count($series));
    $format = $format ?? 'count';
@endphp

<div class="chart">
    <svg viewBox="0 0 100 46" preserveAspectRatio="none" role="img" aria-label="{{ $label ?? 'Chart' }}">
        @foreach ($series as $i => $point)
            @php $h = $point['value'] / $max * 38; @endphp
            <rect x="{{ $i * $barW + $barW * 0.22 }}" y="{{ 40 - $h }}"
                  width="{{ $barW * 0.56 }}" height="{{ max($h, 0.6) }}" rx="0.4"
                  fill="{{ $i === count($series) - 1 ? 'var(--gold-500)' : 'var(--navy-800)' }}">
                <title>{{ $point['label'] }}: {{ $format === 'money' ? money($point['value']) : $point['value'] }}</title>
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
