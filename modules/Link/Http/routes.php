<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin/module/link', 'namespace' => 'Modules\Link\Http\Controllers'], function()
{
    Route::get('create', [
        'as' => 'module.link.create',
        'uses' => 'ModuleController@create',
    ]);

});
