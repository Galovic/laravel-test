<?php
Route::group(['middleware' => 'web', 'prefix' => 'admin/reference', 'namespace' => 'Modules\Reference\Http\Controllers\Admin'], function()
{
    Route::get('/', [
        'as' => 'admin.module.reference.index',
        'uses' => 'ReferenceController@index'
    ]);
    Route::post('/', [
        'as' => 'admin.module.reference.store',
        'uses' => 'ReferenceController@store',
    ]);

    Route::get('create', [
        'as' => 'admin.module.reference.create',
        'uses' => 'ReferenceController@create',
    ]);

    Route::get('{reference}/edit', [
        'as' => 'admin.module.reference.edit',
        'uses' => 'ReferenceController@edit',
    ]);

    Route::patch('{reference}', [
        'as' => 'admin.module.reference.update',
        'uses' => 'ReferenceController@update',
    ]);

    Route::delete('{reference}/delete', [
        'as' => 'admin.module.reference.delete',
        'uses' => 'ReferenceController@delete',
    ]);
});

Route::group(['middleware' => 'web', 'prefix' => 'reference', 'namespace' => 'Modules\Reference\Http\Controllers'], function()
{
    Route::get('create', [
        'as' => 'module.reference.create',
        'uses' => 'ReferenceController@create',
    ]);

    Route::post('create', [
        'uses' => 'ReferenceController@store',
    ]);
});
