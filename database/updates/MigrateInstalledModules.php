<?php

use \App\Models\Module\InstalledModule;

class MigrateInstalledModules extends DatabaseUpdator
{

    /**
     * Run the database update.
     *
     * @return void
     */
    public function run()
    {
        InstalledModule::all(['name'])
            ->map(function ($module) {
                Artisan::call('module:migrate', [
                    'module' => $module->name
                ]);
            });
    }
}