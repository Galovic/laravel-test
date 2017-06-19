<?php

namespace App\Models\Web;

use App\Models\Article\Category;
use App\Models\Page\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Url
 * @package App\Models\Web
 * @property string url
 * @property string model
 * @property int model_id
 */
class Url extends Model
{
    /**
     * @var string Model table
     */
    protected $table = 'urls';

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'url', 'model', 'model_id'
    ];


    /**
     * Find URL
     *
     * @param $url
     * @return mixed
     */
    static function findUrl($url){
        return Url::where('url', $url)->first();
    }


    /**
     * Find page
     *
     * @param int $pageId
     * @return mixed
     */
    static function findPage($pageId){
        return Url::where('model', Page::class)
            ->where('model_id', $pageId)->first();
    }


    /**
     * Find page
     *
     * @param int $categoryId
     * @return mixed
     */
    static function findCategory($categoryId){
        return Url::where('model', Category::class)
            ->where('model_id', $categoryId)->first();
    }


    /**
     * Find model
     *
     * @param string $model
     * @param int $id
     * @return mixed
     */
    static function findModel($model, $id){
        return Url::where('model', $model)
            ->where('model_id', $id)->first();
    }


    /**
     * Get instance of model
     *
     * @return mixed
     */
    public function getInstance(){
        return ($this->model)::find($this->model_id);
    }


    /**
     * Filter urls by specified language
     *
     * @param Builder $query
     * @param Language $language
     */
    public function scopeWhereLanguage(Builder $query, Language $language) {
        $query->where('url', 'like', $language->language_code . '/%');
    }
}
