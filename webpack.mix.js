const mix = require('laravel-mix');
const webpack = require('webpack');
const path = require('path');

// for trumbowyg plugin
mix.webpackConfig({
    resolve: {
      alias: {
        'jquery$': path.resolve(path.join(__dirname, 'node_modules', 'jquery')),
      }
    },
    plugins: [
      new webpack.ProvidePlugin({
        jQuery: 'jquery',
        $: 'jquery',
        'window.jQuery': 'jquery',
      }),
    ],
  });

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/admin/admin.js', 'public/js')
    .sass('resources/sass/admin/admin.scss', 'public/css')
    .js('resources/js/webportal/webportal.js', 'public/js')
    .sass('resources/sass/webportal/webportal.scss', 'public/css')
    .js('resources/js/tablet/tablet.js', 'public/js')
    .version();
