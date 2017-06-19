<?php

namespace App\Models\Module;

use Nwidart\Modules\Json;

class Repository extends \Nwidart\Modules\Repository
{
    /**
     * Get & scan all modules.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();
        $modules = [];
        foreach ($paths as $key => $path) {
            $manifests = $this->app['files']->glob("{$path}/module.json");
            is_array($manifests) || $manifests = [];
            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');
                $modules[$name] = new Module($this->app, $name, dirname($manifest));
            }
        }
        return $modules;
    }


    /**
     * Find installed module.
     *
     * @param string $name
     * @return self|null
     */
    static function findInstalled($name) {
        $installed = InstalledModule::findNamed($name);
        return $installed ? $installed->module : null;
    }


    /**
     * Is module enabled?
     *
     * @param string $name
     * @return boolean
     */
    static function isEnabled($name) {
        $installed = InstalledModule::findNamed($name);
        return $installed ? $installed->enabled : false;
    }
}
