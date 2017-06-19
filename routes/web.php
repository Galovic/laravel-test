<?php

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {

    // Root
    Route::get('/', [
        'as' => 'admin',
        'uses' => 'DashboardController@index'
    ]);

    // Account
    Route::group(['prefix' => 'dashboard'], function () {

        Route::get('/', [
            'as' => 'admin.dashboard',
            'uses' => 'DashboardController@index'
        ]);

        // get chart data
        Route::get('/chart-data', [
            'as' => 'admin.dashboard.chartData',
            'uses' => 'DashboardController@getChartData'
        ]);

        // log off from ga
        Route::post('/off', [
            'as' => 'admin.dashboard.off',
            'uses' => 'DashboardController@logOff'
        ]);

        // authorization
        Route::get('/authorization', [
            'as' => 'admin.dashboard.authorization',
            'uses' => 'DashboardController@showAuthorizationForm'
        ]);
        Route::post('/authorization', [
            'uses' => 'DashboardController@saveGAToken',
        ]);

        // choose profile
        Route::get('/profiles', [
            'as' => 'admin.dashboard.profiles',
            'uses' => 'DashboardController@showProfilesList'
        ]);
        Route::post('/profiles', [
            'uses' => 'DashboardController@saveSelectedProfile',
        ]);
    });

    // Language switch
    Route::get('switch/{language}', [
        'as' => 'admin.switch',
        'uses' => 'AdminController@switchLanguage'
    ]);

    // Authentification
    Route::group( [ 'namespace' => 'Auth' ] ,function () {
        Route::post('logout', [
            'as' => 'admin.auth.logout',
            'uses' => 'LoginController@logout'
        ]);

        // login
        Route::get('login', [
            'as' => 'admin.auth.login',
            'uses' => 'LoginController@showLoginForm'
        ]);

        Route::post('login', [
            'uses' => 'LoginController@login'
        ]);

        Route::group([ 'prefix' => 'password' ], function (){
            // forgot password
            Route::post('email', [
                'as' => 'admin.password.email',
                'uses' => 'ForgotPasswordController@sendResetLinkEmail'
            ]);

            // Set password
//            Route::get('set/{code}', [
//                'as' => 'admin.password.set',
//                'uses' => 'SetPasswordController@showForm'
//            ]);
//
//            Route::post('set/{code}', [
//                'uses' => 'SetPasswordController@set'
//            ]);

            // forgot password
            Route::get('forgot', [
                'as' => 'admin.password.forgot',
                'uses' => 'ForgotPasswordController@showLinkRequestForm'
            ]);

            Route::post('forgot', [
                'uses' => 'ForgotPasswordController@sendResetLinkEmail'
            ]);

            // reset password
            Route::get('reset/{token}', [
                'uses' => 'ResetPasswordController@showResetForm'
            ]);

            Route::post('reset/{token}', [
                'uses' => 'ResetPasswordController@reset'
            ]);

        });
    });

    // Account
    Route::group(['prefix' => 'account'], function () {
        Route::get('/edit', [
            'as' => 'admin.account.edit',
            'uses' => 'AccountController@edit',
        ]);

        Route::post('/edit', [
            'uses' => 'AccountController@update',
        ]);

        Route::post('/password-change', [
            'as' => 'admin.account.password.change',
            'uses' => 'AccountController@changePassword',
        ]);
    });

    // Articles
    Route::group(['prefix' => 'articles'], function () {
        Route::get('/', [
            'as' => 'admin.articles.index',
            'uses' => 'ArticlesController@index',
        ]);

        Route::get('create', [
            'as' => 'admin.articles.create',
            'uses' => 'ArticlesController@create',
        ]);

        Route::get(
            'categories-tree/{article?}', [
            'as' => 'admin.articles.categories_tree',
            'uses' => 'ArticlesController@categoriesTree',
        ]);

        Route::post('upload-photo/{article?}', [
            'as' => 'admin.articles.upload_photo',
            'uses' => 'ArticlesController@uploadPhoto',
        ]);

        Route::get('{article}/edit', [
            'as' => 'admin.articles.edit',
            'uses' => 'ArticlesController@edit',
        ]);

        Route::post('/', [
            'as' => 'admin.articles.store',
            'uses' => 'ArticlesController@store',
        ]);

        Route::patch('{article}', [
            'as' => 'admin.articles.update',
            'uses' => 'ArticlesController@update',
        ]);

        Route::delete('{article}', [
            'as' => 'admin.articles.delete',
            'uses' => 'ArticlesController@delete',
        ]);

        // Photo

        Route::get('photo/list/{article?}', [
            'as' => 'admin.articles.photo.list',
            'uses' => 'ArticlesController@photoList',
        ]);

        Route::post('photo/update/{article?}', [
            'as' => 'admin.articles.photo.update',
            'uses' => 'ArticlesController@updatePhoto',
        ]);

        Route::delete('photo/{photo}/delete', [
            'as' => 'admin.articles.photo.delete',
            'uses' => 'ArticlesController@deletePhoto',
        ]);
    });

    // Categories
    Route::group(['prefix' => 'categories'], function ($route) {

        // index
        Route::get('/', [
            'as' => 'admin.categories.index',
            'uses' => 'CategoryController@index',
            'desc' => trans('admin/routes/general.desc.categories.index')
        ]);

        // forms
        Route::get('create', [
            'as' => 'admin.categories.create',
            'uses' => 'CategoryController@create',
            'desc' => trans('admin/routes/general.desc.categories.create')
        ]);
        Route::get('{category}/edit', [
            'as' => 'admin.categories.edit',
            'uses' => 'CategoryController@edit',
            'desc' => trans('admin/routes/general.desc.categories.edit')
        ]);

        // actions
        Route::post('/', [
            'as' => 'admin.categories.store',
            'uses' => 'CategoryController@store',
            'desc' => trans('admin/routes/general.desc.categories.store')
        ]);
        Route::patch('{category}', [
            'as' => 'admin.categories.update',
            'uses' => 'CategoryController@update',
            'desc' => trans('admin/routes/general.desc.categories.update')
        ]);
        Route::delete('{category}', [
            'as' => 'admin.categories.delete',
            'uses' => 'CategoryController@delete',
            'desc' => trans('admin/routes/general.desc.categories.delete')
        ]);
    });


    // Services
    Route::group(['prefix' => 'services'], function () {
        Route::get('/', [
            'as' => 'admin.services.index',
            'uses' => 'ServicesController@index'
        ]);
        Route::post('/', [
            'as' => 'admin.services.store',
            'uses' => 'ServicesController@store',
        ]);

        Route::get('create', [
            'as' => 'admin.services.create',
            'uses' => 'ServicesController@create',
        ]);

        Route::get('{service}/edit', [
            'as' => 'admin.services.edit',
            'uses' => 'ServicesController@edit',
        ]);

        Route::patch('{service}', [
            'as' => 'admin.services.update',
            'uses' => 'ServicesController@update',
        ]);

        Route::delete('{service}/delete', [
            'as' => 'admin.services.delete',
            'uses' => 'ServicesController@delete',
        ]);
    });

    // Pages
    Route::group(['prefix' => 'pages'], function () {
        Route::get('/', [
            'as' => 'admin.pages.index',
            'uses' => 'PagesController@index',
        ]);
        Route::post('/', [
            'as' => 'admin.pages.store',
            'uses' => 'PagesController@store',
        ]);

        Route::get('create', [
            'as' => 'admin.pages.create',
            'uses' => 'PagesController@create',
        ]);

        Route::get('{page}/edit', [
            'as' => 'admin.pages.edit',
            'uses' => 'PagesController@edit',
        ]);
        Route::post('{page}/edit', [
            'as' => 'admin.pages.update',
            'uses' => 'PagesController@update',
        ]);

        Route::post('{page}/duplicate', [
            'as' => 'admin.pages.duplicate',
            'uses' => 'PagesController@duplicate',
        ]);

        Route::delete('{page}/delete', [
            'as' => 'admin.pages.delete',
            'uses' => 'PagesController@delete',
        ]);

        Route::get('{page}/switch-version/{version?}', [
            'as' => 'admin.pages.switch_version',
            'uses' => 'PagesController@switchVersion',
        ]);

        // Modules
        Route::group(['prefix' => 'entities'], function () {

            Route::get('edit/{entity?}', [
                'as' => 'admin.pages.modules.edit',
                'uses' => 'ModulesController@edit',
            ]);

            Route::post('validate-and-preview/{entity?}', [
                'as' => 'admin.pages.modules.validateAndPreview',
                'uses' => 'ModulesController@validateAndPreview',
            ]);

            Route::post('load',[
                'as' => 'admin.pages.modules.load',
                'uses' => 'ModulesController@loadContents',
            ]);

        });
    });

    // Menu
    Route::group(['prefix' => 'menu'], function () {
        Route::get('/', [
            'as' => 'admin.menu',
            'uses' => 'MenuController@index'
        ]);

        Route::get('pages-tree', [
            'as' => 'admin.menu.pages',
            'uses' => 'MenuController@pagesTree'
        ]);

        Route::get('categories-tree', [
            'as' => 'admin.menu.categories',
            'uses' => 'MenuController@categoriesTree'
        ]);

        Route::post('/', [
            'as' => 'admin.menu.store',
            'uses' => 'MenuController@store'
        ]);

        Route::post('ulozit', [
            'as' => 'admin.menu.update',
            'uses' => 'MenuController@update'
        ]);

        Route::delete('/', [
            'as' => 'admin.menu.delete',
            'uses' => 'MenuController@delete'
        ]);
    });

    // Photogallery
    Route::group(['prefix' => 'photogalleries'], function () {
        Route::get('/', [
            'as' => 'admin.photogalleries',
            'uses' => 'PhotogalleriesController@index',
        ]);

        Route::get('create', [
            'as' => 'admin.photogalleries.create',
            'uses' => 'PhotogalleriesController@create',
        ]);

        Route::post('upload-photo/{photogallery?}', [
            'as' => 'admin.photogalleries.upload_photo',
            'uses' => 'PhotogalleriesController@uploadPhoto',
        ]);

        Route::get('{photogallery}/edit', [
            'as' => 'admin.photogalleries.edit',
            'uses' => 'PhotogalleriesController@edit',
        ]);

        Route::post('/', [
            'as' => 'admin.photogalleries.store',
            'uses' => 'PhotogalleriesController@store',
        ]);

        Route::patch('{photogallery}', [
            'as' => 'admin.photogalleries.update',
            'uses' => 'PhotogalleriesController@update',
        ]);

        Route::delete('{photogallery}', [
            'as' => 'admin.photogalleries.delete',
            'uses' => 'PhotogalleriesController@delete',
        ]);

        // Photo

        Route::get('photo/list/{photogallery?}', [
            'as' => 'admin.photogalleries.photo.list',
            'uses' => 'PhotogalleriesController@photoList',
        ]);

        Route::post('photo/update/{photogallery?}', [
            'as' => 'admin.photogalleries.photo.update',
            'uses' => 'PhotogalleriesController@updatePhoto',
        ]);

        Route::delete('photo/{photo}/delete', [
            'as' => 'admin.photogalleries.photo.delete',
            'uses' => 'PhotogalleriesController@deletePhoto',
        ]);
    });

    // Users
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [
            'as' => 'admin.users',
            'uses' => 'UsersController@index',
        ]);

        // Create
        Route::get('create', [
            'as' => 'admin.users.create',
            'uses' => 'UsersController@create'
        ]);
        Route::post('', [
            'as' => 'admin.users.store',
            'uses' => 'UsersController@store',
        ]);

        // Edit
        Route::get('{user}/edit', [
            'as' => 'admin.users.edit',
            'uses' => 'UsersController@edit',
        ]);
        Route::patch('{user}', [
            'as' => 'admin.users.update',
            'uses' => 'UsersController@update',
        ]);

        // Toggle
        Route::post('{user}/toggle', [
            'as' => 'admin.users.toggle',
            'uses' => 'UsersController@toggle',
        ]);

        // Delete
        Route::delete('{user}', [
            'as' => 'admin.users.delete',
            'uses' => 'UsersController@delete',
        ]);

    });

    // Roles
    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [
            'as' => 'admin.roles',
            'uses' => 'RolesController@index',
        ]);

        Route::get('create', [
            'as' => 'admin.roles.create',
            'uses' => 'RolesController@create',
        ]);
        Route::post('', [
            'as' => 'admin.roles.store',
            'uses' => 'RolesController@store',
        ]);

        Route::get('{role}/edit', [
            'as' => 'admin.roles.edit',
            'uses' => 'RolesController@edit',
        ]);
        Route::patch('{role}', [
            'as' => 'admin.roles.update',
            'uses' => 'RolesController@update',
        ]);

        Route::delete('{role}', [
            'as' => 'admin.roles.delete',
            'uses' => 'RolesController@delete',
        ]);

        Route::get('{role}/toggle', [
            'as' => 'admin.roles.toggle',
            'uses' => 'RolesController@toggle',
        ]);

    });

    // Languages
    Route::group(['prefix' => 'languages'], function () {

        Route::get('/', [
            'as' => 'admin.languages.index',
            'uses' => 'LanguagesController@index'
        ]);

        // Create
        Route::get('create', [
            'as' => 'admin.languages.create',
            'uses' => 'LanguagesController@create'
        ]);
        Route::post('store', [
            'as' => 'admin.languages.store',
            'uses' => 'LanguagesController@store',
        ]);

        // Edit
        Route::get('{language}/edit', [
            'as' => 'admin.languages.edit',
            'uses' => 'LanguagesController@edit'
        ]);
        Route::post('{language}/update', [
            'as' => 'admin.languages.update',
            'uses' => 'LanguagesController@update',
        ]);

        // Delete
        Route::delete('{language}/delete', [
            'as' => 'admin.languages.delete',
            'uses' => 'LanguagesController@delete'
        ]);

        // Actions
        Route::post('{language}/switch', [
            'as' => 'admin.languages.toggle',
            'uses' => 'LanguagesController@toggleEnabled'
        ]);

        Route::post('{language}/set-default', [
            'as' => 'admin.languages.default',
            'uses' => 'LanguagesController@toggleDefault'
        ]);

        // language settings
        Route::post('settings', [
            'as' => 'admin.languages.settings',
            'uses' => 'LanguagesController@updateSettings'
        ]);
    });

    // Theme
    Route::group(['prefix' => 'theme'], function () {

        Route::get('/',[
            'as' => 'admin.theme',
            'uses' => 'ThemeController@index',
        ]);

        Route::post('switch/{name}',[
            'as' => 'admin.theme.switch',
            'uses' => 'ThemeController@switchTheme',
        ]);

        Route::post('config',[
            'as' => 'admin.theme.config',
            'uses' => 'ThemeController@changeSetting',
        ]);

    });

    // Modules
    Route::group(['prefix' => 'modules'], function () {
        Route::get('/', [
            'as' => 'admin.modules',
            'uses' => 'ModulesController@index'
        ]);

        Route::post('{module}/toggle', [
            'as' => 'admin.modules.toggle',
            'uses' => 'ModulesController@toggleEnabled'
        ]);

        Route::post('{name}/install', [
            'as' => 'admin.modules.install',
            'uses' => 'ModulesController@install'
        ]);

        Route::post('{module}/uninstall', [
            'as' => 'admin.modules.uninstall',
            'uses' => 'ModulesController@uninstall'
        ]);
    });

    // Filemanager
    Route::group([
        'prefix' => 'filemanager',
        'as' => 'admin.filemanager.',
        'namespace' => 'Filemanager'
    ], function () {

        // Show integration error messages
        Route::get('/errors', [
            'uses' => 'FilemanagerController@getErrors',
            'as' => 'getErrors'
        ]);

        // Show
        Route::get('{model}/{id?}', [
            'uses' => 'FilemanagerController@show',
            'as' => 'show'
        ]);

        // upload
        Route::any('{model}/{id}/upload', [
            'uses' => 'UploadController@upload',
            'as' => 'upload'
        ]);

        // list images & files
        Route::get('{model}/{id}/items', [
            'uses' => 'ItemsController@getItems',
            'as' => 'items'
        ]);
    });

});

// Storage
Route::group([
    'prefix' => 'storage',
    'as' => 'storage.'
], function () {

    // download file
    Route::get('{model}/{id}/download/{path}', [
        'uses' => 'StorageController@download',
        'as' => 'download'
    ])->where('path', '(.*)');

    // image preview
    Route::get('{model}/{id}/preview/{path}', [
        'uses' => 'StorageController@preview',
        'as' => 'preview'
    ])->where('path', '(.*)');

    // image full view
    Route::get('{model}/{id}/full/{path}', [
        'uses' => 'StorageController@fullView',
        'as' => 'fullView'
    ])->where('path', '(.*)');
});

// Robots
Route::get('robots.txt', 'SiteController@robots');

// Sitemap
Route::get('sitemap/index.xml', [
    'as' => 'sitemap.index',
    'uses' => 'SiteController@sitemapIndex'
]);

// Sitemap
Route::get('sitemap/{languageCode}.xml', [
    'as' => 'sitemap',
    'uses' => 'SiteController@sitemap'
]);

// Homepage
Route::get('/{url?}', [
    'as' => 'homepage',
    'uses' => 'MainController@index'
])->where('url', '(.*)');