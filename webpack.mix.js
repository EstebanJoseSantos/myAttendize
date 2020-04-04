let mix = require('laravel-mix');
let assets = 'public/assets/';
Mix.manifest.refresh = _ => void 0;

mix
    .js(assets + "src/js/app.js", assets + "dist/js/app.js")
    .sass(assets + "src/sass/application.scss", assets + "dist/css")
    .options({
        processCssUrls: false
    });