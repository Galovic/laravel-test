<?php

namespace App\Models\Menu;

use App\Models\Article\Category;
use App\Models\Page\Page;
use App\Models\Web\Language;
use App\Models\Web\Url;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{

    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menu_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'order', 'parent_id', 'class',
        'open_new_window', 'page_id', 'category_id', 'language_id'
    ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = [ 'deleted_at' ];

    protected $nullIfEmpty = [ 'url', 'class', 'page_id', 'parent_id' ];


    /**
     * Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function menu(){
        return $this->hasOne('App\Models\Menu\Menu', 'menu_id');
    }


    /**
     * Order items
     *
     * @param $query
     */
    public function scopeSorted($query){
        $query->orderBy("{$this->table}.order");
    }


    /**
     * First level items only
     *
     * @param $query
     */
    public function scopeFirstLevel($query){
        $query->whereNull("{$this->table}.parent_id");
    }


    /**
     * Child items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('App\Models\Menu\Item', 'parent_id', 'id');
    }


    /**
     * Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function language(){
        return $this->hasOne(Language::class, 'id', 'language_id');
    }


    /**
     * Convert to json structure
     *
     * @return object
     */
    public function toJsonStructure(){
        $object = (object)[
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'full_url' => $this->full_url,
            'order' => $this->order,
            'parentId' => $this->parent_id,
            'class' => $this->class,
            'openNewWindow' => $this->open_new_window,
            'pageId' => $this->page_id,
            'categoryId' => $this->category_id,
            'children' => []
        ];

        foreach($this->children()->sorted()->get() as $child){
            $object->children[] = $child->toJsonStructure();
        }

        return $object;
    }


    /**
     * Select only language mutations
     *
     * @param $query
     * @param mixed $language
     */
    public function scopeWhereLanguage($query, $language){
        $query->where("{$this->table}.language_id", is_scalar($language) ? $language : $language->id);
    }


    /**
     * Get full url
     *
     * @return mixed|null
     */
    public function getFullUrlAttribute(){
        if($this->url){
            return $this->url;
        }

        $url = '#';

        if ($this->page_id) {
            $url = resolve('UrlFactory')->getUrl(Page::class, $this->page_id);
        } else if ($this->category_id) {
            $url = resolve('UrlFactory')->getUrl(Category::class, $this->category_id);
        }

        return $url;
    }
}
