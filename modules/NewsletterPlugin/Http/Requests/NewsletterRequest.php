<?php

namespace Modules\NewsletterPlugin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\NewsletterPlugin\Models\Newsletter;

class NewsletterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                Rule::unique(Newsletter::getTableName(), 'email')
                    ->whereNull('deleted_at')
            ]
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


    /**
     * Get form values
     *
     * @return array
     */
    public function getValues(){
        return $this->only([ 'email' ]);
    }
}
