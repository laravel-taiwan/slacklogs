var elixir = require('laravel-elixir');

require('laravel-elixir-browserify').init('bf');
require('laravel-elixir-sass-compass');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix
        .compass('app.sass', "public/css", {
            sass: "resources/assets/sass"
        })
        .bf('apps/app.jsx', {
            debug: true,
            transform: ['reactify'],
            output: 'public/js',
            rename: 'app.js'
        })
        .version(['css/app.css', 'js/app.js']);
});
