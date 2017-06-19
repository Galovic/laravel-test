<?php

namespace Modules\NewsletterPlugin\Models;

use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Newsletter extends Model
{
    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'module_newsletter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    public $dates = [ 'deleted_at' ];

    /**
     * Key for flash messages.
     *
     * @var string
     */
    static $flashKey = 'module-newsletter-';


    /**
     * Flash success message.
     */
    static function flashSuccess() {
        \Session::flash(self::$flashKey . 'success', 1);
    }


    /**
     * Flash success message.
     */
    static function wasSuccessful() {
        return \Session::get(self::$flashKey . 'success', false);
    }
}
