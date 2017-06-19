<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InstalledModule
 * @property Module $module
 */
class InstalledModule extends Model
{
    /**
     * Model table
     *
     * @var string
     */
    protected $table = 'installed_modules';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'name', 'enabled' ];


    /**
     * Find named module
     *
     * @param $name
     * @return mixed
     */
    static function findNamed($name) {
        return self::where('name', $name)->first();
    }


    /**
     * Get module instance
     *
     * @return Module|null
     */
    public function getModuleAttribute() {
        return \Module::find($this->name);
    }


    /**
     * Only enabled modules
     *
     * @param Builder $query
     */
    public function scopeEnabled(Builder $query) {
        $query->where('enabled', '=',1);
    }
}
