<?php
/**
 * Created by PhpStorm.
 * User: Barbora
 * Date: 11.08.2016
 * Time: 14:48
 */

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Settings extends Model
{
    /**
     * Settings table
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * For how many minutes store value in cache
     *
     * @var int
     */
    protected $cacheMinutes = 60;


    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [ 'name', 'value' ];


    /**
     * Find settings by name
     *
     * @param $name
     * @return mixed
     */
    static function findNamed($name){
        return self::where('name', $name)->first();
    }


    /**
     * Get setting value by name
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    static function get($name, $default = null){

        // Try to find in cache
        if(Cache::has($cacheKey = 'settings_' . $name)){
            return Cache::get($cacheKey);
        }

        $setting = self::findNamed($name);
        if(!$setting) return $default;

        // Store obtained value to cache
        Cache::put($cacheKey, $setting->value, with(new Settings)->cacheMinutes);

        return $setting->value;
    }


    /**
     * Set setting
     *
     * @param $name
     * @param $value
     */
    static function set($name, $value){
        $setting = self::findNamed($name);
        if($setting){
            $setting->update([
                'value' => $value
            ]);
        }else{
            self::create([
                'name' => $name,
                'value' => $value
            ]);
        }

        // Store value to cache
        Cache::put('settings_' . $name, $value, with(new Settings)->cacheMinutes);
    }


    /**
     * Unset setting
     *
     * @param $name
     */
    static function forget($name){
        self::where('name', '=', $name)->delete();
        Cache::forget('settings_' . $name);
    }
}