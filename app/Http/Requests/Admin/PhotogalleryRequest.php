<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Models\Photogallery\Photogallery;
use App\Models\Web\Language;
use App\Models\Web\Url;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Http\FormRequest;

class PhotogalleryRequest extends FormRequest
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
            'title' => 'required|max:250',
            'url' => 'required|max:250|forbidden_url_characters',
            'order' => 'numeric|min:0',
            'seo_title' => 'max:60',
            'seo_description' => 'max:160'
        ];

        return $rules;
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getValues(){

        $input = $this->only([
            'title', 'text', 'seo_title', 'seo_description',
            'seo_keywords', 'sort', 'url'
        ]);

        // Publish at
        $input['publish_at'] = Functions::createDateFromFormat(
            'd.m.Y H:i',
            ($this->publish_at_date ?: date('d.m.Y'))
            . " " . ($this->publish_at_time ?: date('H:i'))
        );

        // Unpublish at
        $input['unpublish_at'] = Functions::createDateFromFormat(
            'd.m.Y H:i',
            $this->input('unpublish_at_date')
            . " " . $this->input('unpublish_at_time')
        );

        return $input;
    }


    /**
     * Create validator for request
     *
     * @param Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Factory $factory)
    {
        $validator = $factory->make(
            $this->validationData(), $this->container->call([$this, 'rules']),
            $this->messages(), $this->attributes()
        );

        $validator->after(function($validator)
        {
            if (!$this->checkUrl()) {
                $validator->errors()->add('url', 'Tato url je již obsazena.');
            }
        });

        return $validator;
    }


    /**
     * Check conflicts in url
     *
     * @return bool
     */
    private function checkUrl() {
        $photogallery = $this->route('photogallery');

        $url = $this->input('url');
        $language = $photogallery->language ?? $this->getLanguage();

        $fullUrl = ($language->language_code ?? '') . '/' . $url;

        $urlModel = Url::findUrl($fullUrl);

        if (!$urlModel) {
            return true;
        }

        return $photogallery && $urlModel->model_id === $photogallery->id && $urlModel->model === Photogallery::class;
    }


    /**
     * Return current language
     *
     * @return Language|null
     */
    private function getLanguage(){
        $language = null;
        $languageId = \Session::get('language', null);

        if($languageId){
            $language = Language::enabled()->where('id', $languageId)->first();
        }

        if(!$language){
            $language = Language::enabled()->orderBy('default', 'desc')->first();
        }

        return $language;
    }
}
