const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.webpackConfig({
    node: {
        fs: 'empty'
    }
});
mix.js('resources/assets/js/main.js', 'public/js')
        .extract([
            'jquery',
            'croppie',
            'handlebars/dist/cjs/handlebars',
        ])
        .autoload({
            jquery: ['$', 'window.jQuery', 'jQuery'],
            croppie: ['croppie'],
            handlebars: ['handlebars/dist/cjs/handlebars', 'Handlebars'],
        })
        .sass('resources/assets/sass/main.scss', 'public/css')
        .copy('resources/assets/images', 'public/images')
        .browserSync({
            proxy: 'www.drmouse.local'
        });
