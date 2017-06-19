<?php

Route::group([
    'middleware' => 'web',
    'prefix' => 'admin/module/view',
    'namespace' => 'Modules\View\Http\Controllers'
], function() {

    Route::get('create', [
        'as' => 'module.view.create',
        'uses' => 'ModuleController@create',
    ]);

    Route::get('variables', [
        'as' => 'module.view.variables',
        'uses' => 'ModuleController@getVariables',
    ]);

});
