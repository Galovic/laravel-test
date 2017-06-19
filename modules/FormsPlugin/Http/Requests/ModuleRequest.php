<?php

namespace Modules\FormsPlugin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\FormsPlugin\Models\Form;

class ModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'form_id' => 'required|exists:' . Form::getTableName() . ',id'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
