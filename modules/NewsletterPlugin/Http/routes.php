<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin/module/newsletter', 'namespace' => 'Modules\NewsletterPlugin\Http\Controllers\Admin'], function()
{
    // Index - table
    Route::get('/', [
        'as' => 'admin.module.newsletter_plugin',
        'uses' => 'NewsletterController@index'
    ]);

    // Example
    Route::get('example', [
        'as' => 'admin.module.newsletter_plugin.example',
        'uses' => 'NewsletterController@example'
    ]);

    // Export
    Route::get('export', [
        'as' => 'admin.module.newsletter_plugin.export',
        'uses' => 'NewsletterController@export',
    ]);

    // Edit
    Route::get('{newsletter}/edit', [
        'as' => 'admin.module.newsletter_plugin.edit',
        'uses' => 'NewsletterController@showEditForm',
    ]);
    Route::post('{newsletter}/edit', [
        'uses' => 'NewsletterController@update',
    ]);

    // Delete
    Route::delete('{newsletter}/delete', [
        'as' => 'admin.module.newsletter_plugin.delete',
        'uses' => 'NewsletterController@delete',
    ]);
});

Route::group(['middleware' => 'web', 'prefix' => 'newsletter', 'namespace' => 'Modules\NewsletterPlugin\Http\Controllers'], function()
{
    Route::post('submit', [
        'as' => 'module.newsletter_plugin.submit',
        'uses' => 'NewsletterController@newsletterSubmit'
    ]);
});
