<?php

namespace Modules\FormsPlugin\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FormsPluginRequest extends FormRequest
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
            'name' => 'required|max:250',
            'submit_text' => 'required|max:100',
            'send_to_email' => 'email',
            'fields' => 'required|json'
        ];
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getValues(){

        return $this->only([
            'name', 'send_to_email'
        ]);

    }


    /**
     * Return localised input values
     *
     * @return array
     */
    public function getLocalised(){

        return $this->only([
            'submit_text', 'success_message'
        ]);

    }


    /**
     * Return input form fields
     *
     * @return array
     */
    public function getFields(){

        $fields = $this->input('fields');

        if(!$fields) return [];

        $fieldsDecoded = json_decode($fields);

        if(!is_array($fieldsDecoded)) return [];

        return $fieldsDecoded;
    }
}
