<?php

namespace Modules\CareerPlugin\Http\Requests\Admin;

use App\Models\Web\Language;
use App\Models\Web\Url;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Http\FormRequest;
use Modules\CareerPlugin\Models\Career;

class CareerRequest extends FormRequest
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
            'title' => 'required|max:250',
            'url' => 'required|max:250|forbidden_url_characters',
            'perex' => 'required',
            'seo_title' => 'max:60',
            'seo_description' => 'max:160',
            'sort' => 'numeric',
            'image' => 'image'
        ];
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getValues(){

        return $this->only([
            'title', 'perex', 'salary', 'bound', 'offerings', 'requirements',
            'seo_title', 'seo_description', 'seo_keywords', 'sort', 'url'
        ]);

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
        $career = $this->route('career');

        $url = $this->input('url');
        $prefix = trans('module-careerplugin::options.url_prefix');
        $language = $career->language ?? $this->getLanguage();

        $fullUrl = ($language->language_code ?? '') . '/' . $prefix . '/' . $url;

        $urlModel = Url::findUrl($fullUrl);

        if (!$urlModel) {
            return true;
        }

        return $career && $urlModel->model_id === $career->id && $urlModel->model === Career::class;
    }


    /**
     * Return current language
     *
     * @return Language|null
     */
    public function getLanguage(){
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
