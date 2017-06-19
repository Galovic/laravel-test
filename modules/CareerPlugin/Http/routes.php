<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin/module/career', 'namespace' => 'Modules\CareerPlugin\Http\Controllers'], function()
{
    Route::get('/', [
        'as' => 'admin.module.career.index',
        'uses' => 'Admin\CareerController@index'
    ]);
    Route::post('/', [
        'as' => 'admin.module.career.store',
        'uses' => 'Admin\CareerController@store',
    ]);

    Route::get('create', [
        'as' => 'admin.module.career.create',
        'uses' => 'Admin\CareerController@create',
    ]);

    Route::get('{career}/edit', [
        'as' => 'admin.module.career.edit',
        'uses' => 'Admin\CareerController@edit',
    ]);

    Route::patch('{career}', [
        'as' => 'admin.module.career.update',
        'uses' => 'Admin\CareerController@update',
    ]);

    Route::delete('{career}/delete', [
        'as' => 'admin.module.career.delete',
        'uses' => 'Admin\CareerController@delete',
    ]);

    Route::get('entity/create', [
        'as' => 'module.careerplugin.create',
        'uses' => 'ModuleController@create',
    ]);
});
