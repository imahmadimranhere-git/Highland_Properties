{{-- Project pagination view. Registered in AppServiceProvider as the default. --}}
@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">
        <span class="pagination__info">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </span>

        @if ($paginator->onFirstPage())
            <span class="pagination__link is-disabled" aria-hidden="true">Previous</span>
        @else
            <a class="pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination__link is-disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination__link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pagination__link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @else
            <span class="pagination__link is-disabled" aria-hidden="true">Next</span>
        @endif
    </nav>
@endif
