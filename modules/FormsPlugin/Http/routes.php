<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin/module/forms-plugin', 'namespace' => 'Modules\FormsPlugin\Http\Controllers'], function()
{
    Route::get('/', [
        'as' => 'admin.module.forms_plugin',
        'uses' => 'Admin\FormsController@index'
    ]);
    Route::post('/', [
        'as' => 'admin.module.forms_plugin.store',
        'uses' => 'Admin\FormsController@store',
    ]);

    Route::get('create', [
        'as' => 'admin.module.forms_plugin.create',
        'uses' => 'Admin\FormsController@create',
    ]);

    Route::get('{form}/edit', [
        'as' => 'admin.module.forms_plugin.edit',
        'uses' => 'Admin\FormsController@edit',
    ]);

    Route::patch('{form}', [
        'as' => 'admin.module.forms_plugin.update',
        'uses' => 'Admin\FormsController@update',
    ]);

    Route::get('{form}/responses', [
        'as' => 'admin.module.forms_plugin.responses',
        'uses' => 'Admin\FormsController@responses',
    ]);

    Route::get('{response}/download-file/{field}', [
        'as' => 'admin.module.forms_plugin.download-file',
        'uses' => 'Admin\FormsController@downloadFile',
    ]);

    Route::delete('{form}/delete', [
        'as' => 'admin.module.forms_plugin.delete',
        'uses' => 'Admin\FormsController@delete',
    ]);

    Route::get('entity\create', [
        'as' => 'module.formsplugin.create',
        'uses' => 'ModuleController@create',
    ]);
});

Route::post('forms-plugin/{form}/submit', [
    'as' => 'module.formsplugin.submit',
    'uses' => 'Modules\FormsPlugin\Http\Controllers\FormController@formSubmit',
    'middleware' => 'web'
]);
