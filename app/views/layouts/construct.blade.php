<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Taiwan Slack logs @yield('page_subtitle')</title>
    <link rel="stylesheet" type="text/css" href="/css/all.css">
</head>
<body>

<header class="header">
    @yield('search-input')
    <a class="logo" href="http://laravel.com">
        <img src="/app/img/laravel.png">
    </a>

    <div class="links">
        Powered by Far · <a href="https://github.com/laravel-taiwan/slacklogs" target="_blank">Fork us on Github</a>
    </div>
</header>

<section class='container'>
    <main class='content'>
        @yield('content')
    </main>
    @yield('timeline')
</section>

<script type="text/javascript" src="/js/all.js"></script>
</body>
</html>
