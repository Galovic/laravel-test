process.env.DISABLE_NOTIFIER = true;

const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

// Fonts //
gulp.task('fonts', function() {
    return gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('public/build/fonts'))
});

elixir(function(mix) {
    // Main core
    mix.sass('admin.scss')
        .webpack('admin.js')
        .sass('errors.scss')
        .sass('grideditor.scss');

    /**
     * Forms
     */

    // Form: articles
    mix.scripts('forms/articles.js', 'public/js/articles.form.js');

    // Form: photogalleries
    mix.scripts('forms/photogallery.js', 'public/js/photogallery.form.js');

    // Form: categories
    mix.scripts('forms/categories.js', 'public/js/categories.form.js');

    // Form: pages
    mix.scripts('forms/pages.js', 'public/js/pages.form.js');


    /**
     * Pages
     */

    // Page: menu
    mix.scripts('pages/menu.js', 'public/js/menu.page.js');

    // Page: account
    mix.scripts('pages/account.js', 'public/js/account.page.js');

    // Page: dashboard
    mix.scripts('pages/dashboard.js', 'public/js/dashboard.page.js');

    /**
     * PLUGINS
     */

    // Plugin: Datatables.net
    mix.scripts([
        '../lib/datatables.net/jquery.dataTables.js',
        '../lib/datatables.net/dataTables.bootstrap.min.js',
        'vendor/datatables_sorting.js'
    ], 'public/js/datatables.js');

    // Plugin: bootstrap-tagsinput
    mix.scripts('../lib/bootstrap-tagsinput/bootstrap-tagsinput.js', 'public/js/bootstrap-tagsinput.js');

    // Plugin: bootstrap-maxlength
    mix.scripts('../lib/bootstrap-maxlength/bootstrap-maxlength.js', 'public/js/bootstrap-maxlength.js');

    // Plugin: pickadate
    mix.scripts([
        '../lib/pickadate/picker.js',
        '../lib/pickadate/picker.date.js',
        '../lib/pickadate/picker.time.js',
        '../lib/pickadate/legacy.js'
    ], 'public/js/pickadate.js');
    mix.styles('../lib/pickadate/themes/classic.css', 'public/css/pickadate.css');

    // Plugin: fancytree
    mix.scripts([
        '../lib/jquery-ui/jquery-ui.js',
        '../lib/fancytree/jquery.fancytree-all.js'
    ], 'public/js/fancytree.js');

    // Plugin: bootstrap-editable
    mix.scripts([
        '../lib/editable/editable.min.js',
        '../lib/editable/config.js'
    ], 'public/js/editable.js');

    // Plugin: plupload
    mix.scripts([
        '../lib/plupload/plupload.full.min.js',
        '../lib/plupload/plupload.queue.min.js'
    ], 'public/js/plupload.js');

    // Plugin: switchery
    mix.scripts('../lib/switchery/switchery.min.js', 'public/js/switchery.js');

    // Plugin: grideditor
    mix.scripts('../lib/grideditor/jquery.grideditor.js', 'public/js/grideditor.js');

    // Plugin: jquery-ui
    mix.scripts('../lib/jquery-ui/jquery-ui.full.js', 'public/js/jquery-ui.js');

    // Plugin: uniform
    mix.scripts('../lib/uniform/uniform.min.js', 'public/js/uniform.js');

    // Plugin: select2
    mix.scripts('../lib/select2/select2.min.js', 'public/js/select2.js');

    // Plugin: typeahead
    mix.scripts('../lib/typeahead/bootstrap-typeahead.js', 'public/js/typeahead.js');

    // Plugin: beautify
    mix.scripts([
        '../lib/beautify/beautify.js',
        '../lib/beautify/beautify-html.js'
    ], 'public/js/beautify.js');

    // Filemanager:
    mix.scripts([
        'filemanager/*.js'
    ], 'public/js/filemanager.js')
        .sass('filemanager/filemanager.scss', 'public/css/filemanager.css');

    // Version
    mix.version([
        // css
        'css/admin.css',
        'css/errors.css',
        'css/grideditor.css',
        'css/filemanager.css',

        // js
        'js/admin.js',
        'public/js/grideditor.js',

        // pages
        'js/menu.page.js',
        'js/account.page.js',
        'js/dashboard.page.js',

        // forms
        'js/articles.form.js',
        'js/photogallery.form.js',
        'js/categories.form.js',
        'js/pages.form.js',

        // Filemanager
        'js/filemanager.js',

        // Grideditor
        'public/js/grideditor.js'

    ], 'public/build');
});