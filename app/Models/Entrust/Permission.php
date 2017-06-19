<?php namespace App\Models\Entrust;

use App\Models\Module\InstalledModule;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission {

    /**
     * @var array
     */
    protected $fillable = [ 'name', 'display_name', 'description', 'enabled' ];


    /**
     * Find permission by its name
     *
     * @param string $name
     * @return self|null
     */
    static function findNamed($name){
        return self::where('name', $name)->first();
    }


    /**
     * Does include given permission?
     *
     * @param $permission
     * @return bool
     */
    public function includes($permission) {
        $start = strrpos($this->name, '-');

        if ( !str_is(substr($this->name, 0, $start), substr($permission, 0, $start))) {
            return false;
        }

        $permissionGroup = $this->getPermissionGroup();

        $start++;
        $thisArea = substr($this->name, $start, strlen($this->name) - $start);
        $givenArea = substr($permission, $start, strlen($permission) - $start);

        $group = config('permissions.groups.' . $permissionGroup);

        if (!isset($group['permissions']) || !isset($group['permissions'][$thisArea]) || !isset($group['permissions'][$givenArea])) {
            return false;
        }

        return $group['permissions'][$thisArea] >= $group['permissions'][$givenArea];
    }


    /**
     * Get permission group from permission name
     *
     * @param string $permission
     * @return bool|int|string
     */
    private function getPermissionGroup($permission = null) {
        $permission = $permission ?: $this->name;

        if (substr( $permission, 0, 7 ) === "module_") {
            $moduleName = substr($permission, 7, strrpos($permission, '-') - 7);

            $isEnabled = InstalledModule::enabled()->where('name', $moduleName)->exists();
            if (!$isEnabled) {
                return false;
            }

            $module = \Module::find($moduleName);
            $permissionGroup = $module->config('permissions.group');
            return is_null($permissionGroup) ? false : $permissionGroup;
        }

        foreach (config('permissions.groups') as $groupId => $group) {
            if (!isset($group['areas'])) {
                continue;
            }

            $area = substr($permission, 0, strrpos($permission, '-'));
            if (in_array($area, $group['areas'])) {
                return $groupId;
            }
        }

        return false;
    }
}
