<?php

namespace App\Models\Module;

class Module extends \Nwidart\Modules\Module
{
    /**
     * Get view
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = []){
        return view($this->getViewName($view), $data, $mergeData);
    }


    /**
     * Get view name
     *
     * @param $view
     * @return string
     */
    public function getViewName($view){
        return $this->getNamespace() . "::" . $view;
    }


    /**
     * Get module namespace
     *
     * @return string
     */
    public function getNamespace(){
        return "module-" . strtolower($this->name);
    }


    /**
     * Generate the URL to a named route.
     *
     * @param  string  $name
     * @param  array   $parameters
     * @param  bool    $absolute
     * @return string
     */
    function route($name, $parameters = [], $absolute = true)
    {
        return route('module.' . strtolower($this->name) . '.' . $name, $parameters, $absolute);
    }


    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if(is_array($key)) {
            foreach ($key as $k => $v) {
                $k = $this->getNamespace() . "." . $k;
            }
        }else{
            $key = $this->getNamespace() . "." . $key;
        }

        return config($key, $default);
    }


    /**
     * Translate the given message.
     *
     * @param  string  $id
     * @param  array   $parameters
     * @param  string  $domain
     * @param  string  $locale
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */
    function trans($id = null, $parameters = [], $domain = 'messages', $locale = null){
        return trans($this->getNamespace() . "::" . $id, $parameters, $domain, $locale);
    }


    /**
     * Configuration class.
     * @return string|null Returns null if class does not exist.
     */
    public function getConfigurationClass() {
        $class = config('modules.namespace') . "\\" .
            $this->getName() . "\\" .
            config('modules.paths.generator.model') ."\\Configuration";

        return class_exists($class) ? $class : null;
    }


    /**
     * Get instance of specified model.
     *
     * @param string $modelName
     * @return string|null Returns null if class does not exist.
     */
    public function getModel($modelName) {
        $class = config('modules.namespace') . "\\" .
            $this->getName() . "\\" .
            config('modules.paths.generator.model') ."\\" . $modelName;

        return class_exists($class) ? new $class : null;
    }


    /**
     * Get module configuration
     *
     * @return null
     */
    public function getConfiguration($id) {
        $class = $this->getConfigurationClass();

        if(class_exists($class)){
            return $class::findOrFail($id);
        }

        return null;
    }


    /**
     * Get module default configuration
     *
     * @return null
     */
    public function getDefaultConfiguration(){
        $class = $this->getConfigurationClass();

        if(class_exists($class) && method_exists($class, 'getDefault')){
            return $class::getDefault();
        }

        return null;
    }


    /**
     * Install module
     *
     * @return int
     */
    public function install() {
        return \Artisan::call('module:migrate', [ 'module' => $this->getName() ]);
    }


    /**
     * Uninstall module
     */
    public function uninstall() {
        Entity::where('module', '=', $this->getName())->delete();

        \Artisan::call('module:migrate-reset', [ 'module' => $this->getName() ]);

        InstalledModule::where('name', '=', $this->getName())->delete();
    }


    /**
     * If is module designed for including to pages.
     *
     * @return boolean
     */
    public function hasPageModule () {
        return $this->config('has-page-module', true);
    }
}
