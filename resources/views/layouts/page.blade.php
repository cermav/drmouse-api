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
        <header>
            <div class="container headerContainer">
                <a href="/" class="logo headerLogo"><p class="logoImg"></p><span class="logoText">veterinář s přehledem</span></a>
                <nav class="menu">
                    <ul>
                        <li><a href="/">Úvod</a></li>
                        <li><a href="/">Veterináři</a></li>
                        <li><a href="{{route('add-doctor')}}">Přidat ordinaci</a></li>
                        <li><a href="/">Články</a></li>
                        <li><a href="/">O Dr. Mouseovi</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <div class="hero blueBg @yield('hero-class')">
            <div class="container">
                @yield('hero')                
            </div>
        </div>
        <main class="@yield('page-class')">
            @yield('content')
        </main>
        <footer>
            <div class="container">
                <div>
                    <a href="/" class="logo"><p class="logoImg"></p></a>                    
                    <div class="copyright">
                        <a href="{{route('faq')}}">Často kladené otázky</a> -
                        <a href="{{route('contact')}}">Kontakt</a></div>
                    <span>&copy; Dr. Mouse {{date("Y")}}</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
