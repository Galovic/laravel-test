<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Models\Page\Page;
use App\Models\Page\Type;
use App\Models\Web\Language;
use App\Models\Web\Url;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
            'url' => 'max:255|forbidden_url_characters',
            'parent_id' => 'exists:' . Page::getTableName() . ',id',
            'type' => 'exists:' . Type::getTableName() . ',id',
            'og_image' => 'image',
            'image' => 'image'
        ];
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getValues(){

        $input = $this->only([
            'name', 'type', 'parent_id', 'url', 'view',
            'seo_title', 'seo_keywords', 'seo_description',
            'og_title', 'og_type', 'og_url', 'og_description'
        ]);

        // Publish at
        $input['published_at'] = Functions::createDateFromFormat(
            'd.m.Y H:i',
            ($this->publish_at_date ?: date('d.m.Y'))
            . " " . ($this->publish_at_time ?: date('H:i'))
        );

        // Unpublish at
        if ($this->set_unpublished_at) {
            $input['unpublished_at'] = Functions::createDateFromFormat(
                'd.m.Y H:i',
                $this->input('unpublish_at_date')
                . " " . $this->input('unpublish_at_time')
            );
        }else{
            $input['unpublished_at'] = null;
        }

        // Is homepage
        $input['is_homepage'] = $this->input('is_homepage', 0);

        // Is listed
        $input['listed'] = $this->input('listed', 0);

        // Is listed
        $input['published'] = $this->input('published', 0);

        // SEO index
        $input['seo_index'] = $this->input('seo_index', 0);

        // SEO follow
        $input['seo_follow'] = $this->input('seo_follow', 0);

        // SEO sitemap
        $input['seo_sitemap'] = $this->input('seo_sitemap', 0);

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
            if (!$this->input('is_homepage') && !$this->checkUrl()) {
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
        $page = $this->route('page');

        $url = $this->input('url');
        $language = $page->language ?? $this->getLanguage();

        $fullUrl = ($language->language_code ?? '') . '/' . $url;

        $urlModel = Url::findUrl($fullUrl);

        if (!$urlModel) {
            return true;
        }

        return $page && $urlModel->model_id === $page->id && $urlModel->model === Page::class;
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
