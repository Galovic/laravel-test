<?php

namespace App\Http\Requests\Admin;

use App\Models\Article\Category;
use App\Models\Web\Language;
use App\Models\Web\Url;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory;

class CategoryRequest extends FormRequest
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
            'url' => 'required|max:255|forbidden_url_characters',
            'parent_id' => 'exists:' . Category::getTableName() . ',id',
            'seo_title' => 'max:60',
            'seo_description' => 'max:160'
        ];
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function all()
    {
        $all = parent::all();
        $all['show'] = isset($all['show']) ? 1 : 0;
        return $all;
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
        $category = $this->route('category');

        $url = $this->input('url');
        $flag = $category->flag ?? 'articles';
        $language = $category->language ?? $this->getLanguage();

        $fullUrl = ($language->language_code ?? '') . '/' . $flag . '/' . $url;

        $urlModel = Url::findUrl($fullUrl);

        if (!$urlModel) {
            return true;
        }

        return $category && $urlModel->model_id === $category->id && $urlModel->model === Category::class;
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
