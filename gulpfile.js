var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.sass('public/app/sass/styles.scss' , "public/app/css/")
        .styles([
            elixir.config.bowerDir + 'normalize-css/normalize.css',
            'public/app/css/styles.css'
        ])
        .scripts([
            elixir.config.bowerDir + 'jquery/dist/jquery.js',
            elixir.config.bowerDir + 'jquery-waypoints/waypoints.js',
            elixir.config.bowerDir + 'jquery-linkify/jquery.linkify.js',
            elixir.config.assetsDir + 'js/scripts.js',
            elixir.config.assetsDir + 'js/infinite-scrolling.js'
        ]);

});
