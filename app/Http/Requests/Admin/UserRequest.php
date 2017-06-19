<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $rules = [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'username' => [ 'required', 'max:50' ],
            'email' => [ 'required', 'email' ],
            'role.*' => 'exists:roles,id'
        ];

        // Creating new
        if($this->method() == 'POST'){
            $rules['email'][] = Rule::unique('users', 'email');
            $rules['username'][] = Rule::unique('users', 'username');
            $rules['password'] = 'required|min:6|confirmed';
        }else{
            $rules['email'][] = Rule::unique('users', 'email')
                ->ignore($this->route('user')->id);

            $rules['username'][] = Rule::unique('users', 'username')
                ->ignore($this->route('user')->id);
        }

        return $rules;
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function getValues()
    {
        $all = $this->only([
            'firstname', 'lastname', 'username', 'email', 'password', 'enabled'
        ]);

        if(isset($all['password'])){
            if(strlen($all['password'])){
                $all['password'] = bcrypt($all['password']);
            }else{
                unset($all['password']);
            }
        }

        $all['enabled'] = isset($all['enabled']) ? 1 : 0;
        return $all;
    }


    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles(){
        return $this->role ? : [];
    }
}
