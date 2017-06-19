<?php

namespace App\Http\Requests\Admin;

use App\Models\Module\InstalledModule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'display_name' => 'required|max:100',
            'description' => 'max:200'
        ];
    }


    /**
     * Return values
     *
     * @return array
     */
    public function getValues(){
        $input = $this->only([ 'display_name', 'description', 'enabled' ]);
        $input['enabled'] = isset($input['enabled']);
        return $input;
    }


    /**
     * Return permissions
     *
     * @return array
     */
    public function getPermissions(){
        $output = [];
        $groups = config('permissions.groups');

        foreach($groups as $groupId => $group){

            $permissions = $group['permissions'];

            foreach($group['areas'] as $area){
                $highestPermissions = $this->getAreaHighestPermissions($area, $permissions);

                if($highestPermissions) {
                    $output = array_merge($output, $highestPermissions);
                }
            }
        }

        /** @var InstalledModule $installedModule */
        foreach (InstalledModule::enabled()->get() as $installedModule) {
            $moduleGroup = $installedModule->module->config('permissions.group');

            if ($moduleGroup && isset($groups[$moduleGroup])) {
                $highestPermissions = $this->getAreaHighestPermissions(
                    "module_{$installedModule->module->config('nickname')}",
                    $groups[$moduleGroup]['permissions']
                );

                if($highestPermissions) {
                    $output = array_merge($output, $highestPermissions);
                }
            }
        }

        return $output;
    }


    /**
     * Get only highest permissions for specified area.
     *
     * @param string $area
     * @param array $permissions
     * @return array|bool
     */
    private function getAreaHighestPermissions($area, $permissions) {

        $areaPermissions = $this->input($area);
        if(!$areaPermissions) return false;

        $highestPermissions = [];
        $highestWeight = 0;

        foreach($areaPermissions as $areaPermission => $_){
            if(!isset($permissions[$areaPermission])) continue;
            $weight = $permissions[$areaPermission];

            if($weight > $highestWeight){
                $highestPermissions = [ "$area-$areaPermission" ];
                $highestWeight = $weight;
            }
            elseif($weight == $highestWeight){
                $highestPermissions[] = "$area-$areaPermission";
            }
        }

        return $highestPermissions ?: false;
    }
}
