@php $tawkId = setting('tawk_id'); @endphp

@if ($tawkId)
    {{--
        Tawk.to live chat.

        Loaded on window.load, not with the page: the widget pulls roughly
        300KB and opens a socket, and doing that while the page is still
        painting delays what the visitor came to read. A second later nobody
        notices — the bubble simply appears.

        The id is validated on save (two ids separated by a slash), so nothing
        from the database is ever printed as raw HTML.
    --}}
    <script>
        window.addEventListener('load', function () {
            window.Tawk_API = window.Tawk_API || {};
            window.Tawk_LoadStart = new Date();

            var s = document.createElement('script');
            s.async = true;
            s.src = 'https://embed.tawk.to/{{ $tawkId }}';
            s.charset = 'UTF-8';
            s.setAttribute('crossorigin', '*');
            document.head.appendChild(s);
        });
    </script>
@endif
