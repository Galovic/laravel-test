<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin/module/text', 'namespace' => 'Modules\Text\Http\Controllers'], function()
{
    Route::get('create', [
        'as' => 'module.text.create',
        'uses' => 'ModuleController@create',
    ]);

    Route::post('validate-and-preview', [
        'uses' => 'ModuleController@validateAndPreview',
    ]);
});
