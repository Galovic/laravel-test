<?php

namespace App\Http\Requests\Admin;

use App\Models\Web\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
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
            'name' => 'required|max:255',
            'country_code' => 'required|max:3',
            'language_code' => [
                'required', 'max:3',
                Rule::unique(Language::getTableName(), 'language_code')
            ],
            'domain' => 'max:255'
        ];
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function getValues()
    {
        $all = $this->only([ 'name', 'country_code', 'language_code', 'enabled', 'domain' ]);
        $all['enabled'] = isset($all['enabled']) ? 1 : 0;
        return $all;
    }
}
