<?php

namespace App\Helpers;

use App\Models\Web\Theme;

abstract class ViewHelper
{
    /**
     * Return all available views
     *
     * @return array
     */
    static function getAllViews(){
        $views =  [
            'Veřejné' => self::getFrontendViews()
        ];

        $theme = Theme::getDefault();
        $views["Šablona " . $theme->name] = self::getThemeViews($theme);

        return $views;
    }


    /**
     * Get all views for specific purpose
     *
     * @param $type
     * @return array
     */
    static function getDemarcatedViews($type){
        $views = [];

        $systemViews = config('admin.demarcated_views.' . $type);
        if($systemViews){
            $views['Veřejné'] = $systemViews;
        }

        $theme = Theme::getDefault();
        $themeViews = $theme->demarcated_views[$type] ?? null;
        if($themeViews){
            $views["Šablona " . $theme->name] = $themeViews;
        }

        return $views;
    }


    /**
     * Check if given view is demarcated within given type.
     * @param string $type
     * @param string $view
     * @return bool
     */
    static function isViewDemarcated($type, $view) {
        $systemViews = config('admin.demarcated_views.' . $type);
        if ($systemViews && isset($systemViews[$view])) {
            return true;
        }

        $theme = Theme::getDefault();
        $themeViews = $theme->demarcated_views[$type] ?? null;
        return $themeViews && isset($themeViews[$view]);
    }


    /**
     * Get name of the specified view.
     * @param string $type
     * @param string $view
     * @return string|null
     */
    static function getViewName($type, $view) {
        $systemViews = config('admin.demarcated_views.' . $type);
        if ($systemViews && isset($systemViews[$view])) {
            return $systemViews[$view];
        }

        $theme = Theme::getDefault();
        $themeViews = $theme->demarcated_views[$type] ?? null;
        return $themeViews && isset($themeViews[$view]) ? $themeViews[$view] : null;
    }


    /**
     * Get all frontend views
     *
     * @return array
     */
    static function getFrontendViews(){
        $views = [];

        $files = \File::allFiles($viewsPath = resource_path('views'));
        foreach ($files as $file)
        {
            if(!ends_with($file, '.blade.php')) continue;
            $file = str_replace($viewsPath, '', $file);
            if(starts_with($file, '/admin')) continue;

            $viewName = str_replace('/', '.', substr($file, 1, strlen($file) - strlen('.blade.php') - 1));

            $views[$viewName] = $viewName;
        }

        return $views;
    }


    /**
     * Get all theme views
     *
     * @param Theme $theme
     * @return array
     */
    static function getThemeViews(Theme $theme){
        $views = [];

        $files = \File::allFiles($viewsPath = $theme->view_path);
        foreach ($files as $file)
        {
            if(!ends_with($file, '.blade.php')) continue;
            $file = str_replace($viewsPath, '', $file);
            if(starts_with($file, '/admin')) continue;

            $viewName = str_replace('/', '.', substr($file, 1, strlen($file) - strlen('.blade.php') - 1));

            $views['theme::' . $viewName] = $viewName;
        }

        return $views;
    }


    /**
     * Get view variables.
     * @param $view
     * @return array|mixed
     */
    static function getViewVariables($view) {

        // Register theme namespace with context instantiation.
        if (substr($view,0,5) === "theme") {
            $theme = Theme::getDefault();
            $theme->getContextInstance();
        }

        $variables = [];

        ob_start();
        $GET_VARIABLES = true;
        try {
            $variables = include view($view)->getPath();
        } catch (\Exception $e) {}
        ob_get_clean();

        return is_array($variables) ? $variables : [];
    }
}
