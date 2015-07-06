<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>PHP Taiwan Slack logs @yield('page_subtitle')</title>
    <link rel="stylesheet" type="text/css" href="{{ elixir('css/app.css') }}">
</head>
<body>
    <div id="mainSection">
        <header class="header">
            @yield('search-input')
            <a class="logo" href="http://laravel.tw">
                <img src="/img/laravel.png">
            </a>
            <div class="links">
                Powered by <a href="https://github.com/farrrr">Far Tseng</a> · <a href="https://github.com/laravel-taiwan/slacklogs" target="_blank">Fork us on Github</a>
            </div>
        </header>

        <section class='container'>
            <main class='content'>
                @yield('content')
            </main>
            <aside>
            @yield('channel')
            @yield('timeline')
            </aside>
        </section>
    </div>

<script type="text/javascript" src="{{ elixir('js/app.js') }}"></script>
</body>
</html>
