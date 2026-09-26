@php
    use App\Models\Ticker;
    use App\Support\PublicCache;
    use Illuminate\Support\Facades\Cache;

    $items = Cache::remember(PublicCache::TICKER, PublicCache::TTL, fn () => Ticker::active()
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['message', 'link']));

    /*
     * The strip moves at a steady reading speed instead of a fixed number of
     * seconds: with a fixed duration, two announcements would crawl and eight
     * would race past. Roughly 18 characters a second, never under 20s.
     */
    $characters = $items->sum(fn ($item) => mb_strlen($item->message) + 4);
    $duration = max(20, (int) round($characters / 9));
@endphp

@if ($items->isNotEmpty())
    {{--
        The messages are printed twice: as the first copy leaves the screen
        the second is already in place, so the loop never shows a gap. No
        script and no library is involved.
    --}}
    <aside class="ticker-wrap" aria-label="Announcements">
        <div class="u-container">
            <div class="ticker" style="--ticker-duration: {{ $duration }}s;">
                <div class="ticker__track">
                    @for ($copy = 0; $copy < 2; $copy++)
                        <span class="ticker__group" @if ($copy) aria-hidden="true" @endif>
                            @foreach ($items as $item)
                                @if ($item->link)
                                    <a class="ticker__item" href="{{ $item->link }}">{{ $item->message }}</a>
                                @else
                                    <span class="ticker__item">{{ $item->message }}</span>
                                @endif

                                <span class="ticker__sep" aria-hidden="true">&#9670;</span>
                            @endforeach
                        </span>
                    @endfor
                </div>
            </div>
        </div>
    </aside>
@endif
