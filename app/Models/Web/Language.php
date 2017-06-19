<?php

namespace App\Models\Web;

use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use AdvancedEloquentTrait;

    /**
     * @var string Model table
     */
    protected $table = 'languages';

    /**
     * @var bool Don't use timestamps
     */
    public $timestamps = false;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'name', 'country_code', 'language_code', 'enabled', 'default', 'domain'
    ];


    /**
     * Find language by code in url
     *
     * @param $code
     * @return Language|null
     */
    static function findByUrlCode($code){
        return Language::enabled()->where('language_code', $code)->first();
    }


    /**
     * Select only allowed
     *
     * @param $query
     */
    public function scopeEnabled($query){
        $query->where('enabled', 1);
    }


    /**
     * Find default language
     *
     * @return Language|null
     */
    static function findDefault() {
        return Language::enabled()->where('default', 1)->first();
    }
}
