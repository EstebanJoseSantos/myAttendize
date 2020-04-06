let mix = require('laravel-mix');
let assets = 'public/assets/';
Mix.manifest.refresh = _ => void 0;

mix
    .webpackConfig({
        module: {
            rules: [
                {
                    test: /\.m?js$/,
                    exclude: /(node_modules|bower_components)/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env']
                        }
                    }
                }
            ]
        }
    })
    .copyDirectory(
        assets + "src/sass/icons/",
        assets + "dist/css/icons"
    )
    .js(assets + "src/js/app.js", assets + "dist/js/app.js")
    .js(assets + "src/js/backend.js", assets + "dist/js/backend.js")
    .js(assets + "src/js/frontend.js", assets + "dist/js/frontend.js")
    .sass(assets + "src/sass/application.scss", assets + "dist/css")
    .options({
        processCssUrls: false
    });
