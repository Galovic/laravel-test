<?php

namespace App\Models;

use App\Models\Entrust\Permission;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait, AdvancedEloquentTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'password', 'enabled', 'position', 'about'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * Get full name
     *
     * @return string
     */
    public function getNameAttribute(){
        return "{$this->firstname} {$this->lastname}";
    }


    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool         $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function can($permission, $requireAll = false)
    {
        if($this->hasRole([ 'administrator', 'programmer' ])){
            return true;
        }

        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->can($permName);

                if ($hasPerm && !$requireAll) {
                    return true;
                } elseif (!$hasPerm && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->cachedRoles() as $role) {
                // Validate against the Permission table
                /** @var Permission $perm */
                foreach ($role->cachedPermissions() as $perm) {
                    if ($perm->includes($permission)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get user images directory path
     *
     * @return string
     */
    static function getImagesDirectory() {
        return public_path( config('admin.path_upload') ) . '/users';
    }


    /**
     * Get user image path
     *
     * @return string
     */
    public function getImagePathAttribute() {
        $dir = self::getImagesDirectory();
        $imageName = md5($this->id);

        return $dir . '/' . $imageName . '.jpg';
    }


    /**
     * Get user image url
     *
     * @return string
     */
    public function getImageUrlAttribute() {
        if ($this->hasCustomImage()) {
            return url( config('admin.path_upload') . '/users/' . md5($this->id) . '.jpg' );
        }
        return \Gravatar::get($this->email);
    }


    /**
     * Has user custom image?
     *
     * @return bool
     */
    public function hasCustomImage() {
        return \File::exists($this->image_path);
    }
}
