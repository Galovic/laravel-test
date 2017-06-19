<?php

namespace App\Traits;

use App\Models\Web\Url;
use Illuminate\Database\Eloquent\Builder;

trait AdvancedEloquentTrait
{

    /**
     * Array of used scopes
     * @var array
     */
    protected $scopeUsages = [];

    /**
     * Model full url
     * @var string
     */
    private $fullUrl;


    /**
     * Returns model table name
     *
     * @return mixed
     */
    public static function getTableName()
    {
        return ((new self)->getTable());
    }


    /**
     * Returns true if scope was not used and marks scope as used
     *
     * @param $name
     * @return bool
     */
    private function uniqueScope($name){
        if(isset($this->scopeUsages[$name])){
            return false;
        }
        return $this->scopeUsages[$name] = true;
    }


    /**
     * Pass joins
     *
     * @param $query
     */
    public function scopePassJoins($query){
        if(!$this->uniqueScope('passJoins')) return;

        $query->select([ $this->getTable() . '.*' ]);
    }


    /**
     * Advanced attribute trait
     *
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        if(isset($this->nullIfEmpty) && empty($value) && in_array($key, $this->nullIfEmpty)){
            $value = null;
        }

        if(property_exists(self::class, 'setAttribute')) {
            $this::setAttribute($key, $value);
        }else{
            parent::setAttribute($key, $value);
        }
    }


    /**
     * Get full url
     *
     * @return string
     */
    public function getFullUrlAttribute(){
        if($this->fullUrl) return $this->fullUrl;
        return $this->fullUrl = \UrlFactory::getFullUrl(get_class($this), $this->id);
    }


    /**
     * Get URL record
     *
     * @return Builder
     */
    public function getUrlScope(){
        return Url::where('model', self::class)->where('model_id', $this->id);
    }

}