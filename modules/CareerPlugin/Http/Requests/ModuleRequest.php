<?php

namespace Modules\CareerPlugin\Http\Requests;

use App\Models\Article\Article;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use App\Models\Service;
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
            'show_number' => 'numeric|min:0'
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
