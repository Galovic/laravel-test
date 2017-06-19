<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Web\Url;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{

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
                $validator->errors()->add('url', 'Tato url je již použita u jiného článku ve stejné kategorii.');
            }
        });

        return $validator;
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|max:250',
            'type' => 'numeric|min:1|max:3',
            'url' => 'required|max:250|forbidden_url_characters',
            'parent_id' => 'exists:' . Category::getTableName() . ',id',
            'perex' => 'required',
            'seo_title' => 'max:60',
            'seo_description' => 'max:160',
            'categories_imploded' => 'required',
        ];

        // Creating new
        if($this->method() == 'POST'){
            $rules['image'] = 'required|image';
        }

        return $rules;
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
     * Check conflicts in url
     *
     * @return bool
     */
    private function checkUrl() {
        $url = $this->input('url');
        $categoriesIds = $this->getCategories();

        if (!$categoriesIds || !$url){
            return true;
        }

        /** @var Collection $newUrls */
        $newUrls = Url::where('model', Category::class)
            ->whereIn('model_id', $categoriesIds)
            ->select('url')->pluck('url')->map(function ($categoryUrl) use ($url) {
                return $categoryUrl . '/' . $url;
            });

        if ($newUrls->isEmpty()) {
            return true;
        }

        /** @var Collection $urlCheckScope */
        $urlCheck = Url::whereIn('url', $newUrls)
            ->select('model', 'model_id')
            ->get();

        if ($article = $this->route('article')) {
            $urlCheck = $urlCheck->filter(function($url) use ($article) {
                return !($url->model_id === $article->id && $url->model === Article::class);
            });
        }

        return $urlCheck->isEmpty();
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getValues(){

        $input = $this->only([
            'title', 'perex', 'text', 'seo_title', 'seo_description',
            'seo_keywords', 'type', 'url'
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
     * Return categories ids
     *
     * @return array
     */
    public function getCategories(){
        return array_map('intval', explode(',', $this->input('categories_imploded', '')));
    }


    /**
     * Return tags
     *
     * @return array
     */
    public function getTags(){
        return array_unique(array_map('trim', explode(',', $this->input('tags', ''))));
    }
}
