<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('meta_group_name') Dr. Mouse</title>

        <link rel="shortcut icon" href="{{ asset('images/favicons/favicon.ico') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/favicons/favicon-16x16.png') }}" sizes="16x16">
        <link rel="icon" type="image/png" href="{{ asset('images/favicons/favicon-32x32.png') }}" sizes="32x32">

        <!-- Styles -->
        <link href="{{ asset('css/main.css') }}" rel="stylesheet" type="text/css">

        <script src="{{ asset('js/manifest.js') }}"></script>
        <script src="{{ asset('js/vendor.js') }}"></script>
        <script src="{{ asset('js/main.js') }}" /></script>

</head>
<body>
    <div class='mainContainer'>
        <main class="@yield('page-class')">
            @yield('content')
        </main>
    </div>
</body>
</html>
