<?php
/**
 * Created by PhpStorm.
 * User: Barbora
 * Date: 11.08.2016
 * Time: 14:48
 */

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [ 'id', 'name', 'menu_locations', 'demarcated_views' ];

    public $incrementing = false;

    /**
     * Find theme by name
     *
     * @param $name
     * @return mixed
     */
    static function find($name){
        $path = base_path('resources/themes/' . $name);

        if(!file_exists($path)) return null;

        $config = include $path . '/config.php';

        return new Theme([
            'id' => $name
        ] + $config);
    }


    /**
     * Get all themes
     *
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    static function all($columns = ['*'])
    {
        $themes = collect([]);

        $themeIterator = new \DirectoryIterator(base_path('resources/themes'));
        foreach ($themeIterator as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $theme = self::find($fileinfo->getFilename());
                $themes->push($theme);
            }
        }

        return $themes;
    }


    /**
     * Return default template
     *
     * @return self
     */
    static function getDefault(){
        $defaultTheme = Theme::find(Settings::get('theme'));

        if(!$defaultTheme){
            $defaultTheme = self::find('default');

            if(!$defaultTheme){
                $defaultTheme = self::all()->first();
            }

            if($defaultTheme){
                Settings::set('theme', $defaultTheme->id);
            }
        }

        return $defaultTheme;
    }


    /**
     * Get menu locations
     *
     * @return array
     */
    public function getMenuLocationsAttribute(){
        return $this->attributes['menu_locations'] ?? [];
    }


    /**
     * Get theme path
     *
     * @return array
     */
    public function getPathAttribute(){
        return base_path('resources/themes/' . $this->id);
    }

    /**
     * Get theme path
     *
     * @return array
     */
    public function getViewPathAttribute(){
        return $this->path . '/view';
    }


    /**
     * Assign menu by its ID to location in theme
     *
     * @param $location
     * @param $id
     * @return bool
     */
    public function setMenuLocation($location, $id){
        if(!isset($this->menu_locations[$location])) return false;

        $this->set("menu_$location", $id);

        return true;
    }


    /**
     * Get menu locations with settings
     *
     * @return array
     */
    public function getMenuLocationsSettingsAttribute(){
        $result = [];
        foreach($this->menu_locations as $menuLocation => $title){
            $result[$menuLocation] = $this->get("menu_$menuLocation");
        }

        return $result;
    }


    /**
     * Set setting
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value){
        Settings::set('theme_' . $this->id . '_' . $name, $value);
    }


    /**
     * Get setting
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null){
        return Settings::get('theme_' . $this->id . '_' . $name, $default);
    }


    /**
     * Get instance of the theme context
     *
     * @return null
     */
    public function getContextInstance(){
        $class = 'Context';

        $path = base_path('resources/themes/' . $this->id . '/' . $class);
        require_once $path . '.php';

        if (class_exists($class))
        {
            return new $class($this);
        }

        return null;
    }

}