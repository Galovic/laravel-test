<?php

namespace Modules\Link\Models;

use App\Models\Article\Article;
use App\Models\Interfaces\ModuleConfigurationInterface;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use App\Models\Service;
use App\Models\Web\Url;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model implements ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_link_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'text', 'model', 'model_id', 'url', 'view', 'tags' ];


    /**
     * Get model type to class translation table
     *
     * @var array
     */
    static $modelTypeClass = [
        'page' => Page::class,
        'article' => Article::class,
        'service' => Service::class,
        'photogallery' => Photogallery::class,
    ];


    /**
     * Get default configuration
     *
     * @return Configuration
     */
    static function getDefault(){
        return new self([
            'view' => ''
        ]);
    }


    /**
     * Get type of model
     *
     * @return string|null
     */
    public function getModelTypeAttribute(){
        if(!$this->model) return null;

        $classesToTypes = array_flip(self::$modelTypeClass);
        return isset($classesToTypes[$this->model]) ? $classesToTypes[$this->model] : null;
    }


    /**
     * Get page id
     *
     * @return int|null
     */
    public function getPageIdAttribute(){
        return $this->model == Page::class ? $this->model_id : null;
    }


    /**
     * Get article id
     *
     * @return int|null
     */
    public function getArticleIdAttribute(){
        return $this->model == Article::class ? $this->model_id : null;
    }


    /**
     * Get service id
     *
     * @return int|null
     */
    public function getServiceIdAttribute(){
        return $this->model == Service::class ? $this->model_id : null;
    }


    /**
     * Get photogallery id
     *
     * @return int|null
     */
    public function getPhotogalleryIdAttribute(){
        return $this->model == Photogallery::class ? $this->model_id : null;
    }


    /**
     * Get full url
     *
     * @return string
     */
    public function getFullUrlAttribute() {
        if(!$this->model){
            return strlen($this->url) ? $this->url : "#url_not_found";
        }

        return resolve('UrlFactory')->getFullUrl($this->model, $this->model_id);
    }


    /**
     * Get attributes string
     *
     * @return string
     */
    public function mergeTagsReturnString($outerTags){
        $tags = $this->tags;

        // Add outer tags to all tags
        foreach($outerTags as $key => $value){
            // Merge class tag
            if($key == 'class' && isset($tags[$key])){
                $outerTags[$key] .= ' ' . $tags[$key];
            }
            $tags[$key] = $outerTags[$key];
        }

        if(!$tags) return '';

        $stringTags = [];
        foreach($tags as $key => $value){
            $stringTags[] = strlen($value) ? "$key=\"{$value}\"" : $key;
        }

        return join(' ', $stringTags);
    }


    /**
     * Get tags
     *
     * @return mixed|null
     */
    public function getTagsAttribute($value){
        return $value ? (array)json_decode($value) : null;
    }


    /**
     * Get javascript tags
     *
     * @return array|null
     */
    public function getJavascriptTagsAttribute(){
        if(!$this->tags) return [];

        $tags = [];
        foreach($this->tags as $tag => $value){
            $tags[] = [
                'name' => $tag,
                'value' => $value
            ];
        }

        return $tags;
    }


    /**
     * Render module
     *
     * @return string
     */
    public function render(){
        if (!\View::exists($this->view)) {
            return null;
        }

        return view($this->view, [
            'configuration' => $this
        ])->render();
    }


    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        $fill = array_only($inputs, ['view', 'url', 'text']);

        $fill['model'] = null;
        $fill['model_id'] = null;

        // Model
        if(!($inputs['custom_url'] ?? false)){
            $fill['url'] = null;
            $model = $inputs['model_type'] ?? null;
            $fill['model'] = self::$modelTypeClass[$model];
            $fill['model_id'] = $inputs[$model . '_id'];
        }

        // View
        if(isset($fill['view']) && !strlen($fill['view'])){
            $fill['view'] = null;
        }

        // Attributes
        $tags = [];
        $tagKeys = $inputs['attribute_key'] ?? [];
        $tagValues = $inputs['attribute_value'] ?? [];
        foreach($tagKeys as $i => $tagKey){
            if(!$tagKey) continue;
            $tags[$tagKey] = htmlspecialchars($tagValues[$i]);
        }

        $fill['tags'] = $tags ? json_encode($tags) : null;

        return $this->fill($fill);
    }
}
