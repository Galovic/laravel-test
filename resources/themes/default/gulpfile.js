const elixir = require('laravel-elixir');
var symlink = require('gulp-symlink');

// theme assets path
elixir.config.assetsPath = './assets/';

// theme public path
elixir.config.publicPath = '../../../public/theme/default';

/**
 * Create symlinks to media directory
 */
gulp.task('media', function () {
    return gulp.src('./media')
        .pipe(symlink(elixir.config.publicPath + '/media'));
});

/**
 * Build scripts and styles
 */
elixir(function(mix) {
    // Main style
    mix.sass('style.scss');

    mix.webpack('app.js');

    // Version
    mix.version([
        // css
        'css/style.css',

        // js
        'js/app.js'
    ]);
});