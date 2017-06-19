<?php namespace App\Models\Entrust;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description', 'enabled'
    ];


    /**
     * Automatically set name of role
     */
    public function setAutomaticName(){
        $this->name = str_slug($this->display_name, '-') . time();
    }


    /**
     * Is editable? Protects roles programmer and administrator
     *
     * @return bool
     */
    public function isEditable()
    {
        return !($this->name == 'programmer' || $this->name == 'administrator');
    }


    /**
     * Enabled roles only
     *
     * @param $query
     */
    public function scopeEnabled($query){
        $query->where('enabled', 1);
    }


    /**
     * Save the inputted permissions.
     *
     * @param array $permissions
     *
     * @return void
     */
    public function saveNamedPermissions(array $permissions)
    {
        $permissionsIds = [];

        foreach($permissions as $permissionName){
            $permission = Permission::findNamed($permissionName);
            if(!$permission || !$permission->exists){
                $permission = Permission::create([
                    'name' => $permissionName
                ]);
            }

            $permissionsIds[] = $permission->id;
        }

        $this->savePermissions($permissionsIds);
    }


    public function getAllPermissionsNames(){
        $rolePermissions = $this->perms->pluck(null, 'name');
        $searchedAreas = [];
        $permissionsNames = [];

        // there are saved only permissions with highest weight
        foreach($rolePermissions as $rolePermissionName => $_) {
            list($area, $permission) = preg_split('~-(?=[^-]*$)~', $rolePermissionName);
            $permissionsNames[$rolePermissionName] = true;

            // if was already searched for lower permissions,
            // it can be sure, that all lower permissions were added to the list
            if(isset($searchedAreas[$area])) continue;
            $searchedAreas[$area] = true;

            // find permissions with lower weight and add them to the list
            foreach (config('permissions.groups') as $groupId => $group) {
                if(!isset($group['permissions'][$permission]) || !in_array($area, $group['areas'])) continue;

                $permissionWeight = $group['permissions'][$permission];
                foreach ($group['permissions'] as $groupPermissionName => $groupPermissionWeight){
                    if($permissionWeight > $groupPermissionWeight){
                        $permissionsNames["$area-$groupPermissionName"] = true;
                    }
                }
            }
        }

        return $permissionsNames;
    }
}
