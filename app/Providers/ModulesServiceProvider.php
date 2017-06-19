<?php

namespace App\Providers;


use App\Models\Module\Repository;
use Nwidart\Modules\LaravelModulesServiceProvider;

class ModulesServiceProvider extends LaravelModulesServiceProvider
{
    /**
     * Register the service provider.
     */
    protected function registerServices()
    {
        $this->app->singleton('modules', function ($app) {
            $path = $app['config']->get('modules.paths.modules');

            return new Repository($app, $path);
        });
    }
}