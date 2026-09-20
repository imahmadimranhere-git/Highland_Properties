<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Sign in') &middot; {{ setting('site_name', 'Highland Properties') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap">

    {{-- Login has its own tiny bundle: no sidebar, table or panel CSS. --}}
    @vite(['resources/scss/auth.scss'])
</head>
<body class="auth">
    @yield('content')
</body>
</html>
