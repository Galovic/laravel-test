<?php

namespace Modules\Photogallery\Http\Requests;

use App\Models\Photogallery\Photogallery;
use Illuminate\Foundation\Http\FormRequest;

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
            'view' => 'required',
            'photogallery_id' => 'required|exists:' . Photogallery::getTableName() . ',id',
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
