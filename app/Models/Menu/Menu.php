<?php

namespace App\Models\Menu;

use App\Models\Web\Language;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{

    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name' ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = [ 'deleted_at' ];


    /**
     * Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(){
        return $this->hasMany('App\Models\Menu\Item', 'menu_id');
    }


    /**
     * Convert to json structure
     *
     * @return array
     */
    static function toJsonStructure(Language $language){
        $structure = [];

        foreach(self::all() as $menu){
            $structure[] = $menu->toJsonObject($language);
        }

        return $structure;
    }


    /**
     * Convert model to json object
     *
     * @param Language $language
     * @return object
     */
    public function toJsonObject(Language $language){
        $menuObject = (object)[
            'id' => $this->id,
            'name' => $this->name,
            'items' => [],
        ];

        /** @var Item $item */
        foreach($this->items()->whereLanguage($language)->sorted()->firstLevel()->get() as $item){
            $menuObject->items[] = $item->toJsonStructure();
        }

        return $menuObject;
    }
}
