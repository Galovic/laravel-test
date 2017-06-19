<?php

namespace App\Models\Photogallery;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;


class Photo extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'photogallery_photos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'title', 'author', 'image', 'sort' ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $dates = [ 'deleted_at' ];


    /**
     * Photogallery
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photogallery(){
        return $this->belongsTo('App\Models\Photogallery\Photogallery', 'photogallery_id');
    }


    /**
     * Image url
     *
     * @return string
     */
    public function getUrlAttribute(){
        return $this->photogallery->photos_url . '/big/' . $this->image;
    }


    /**
     * Image url
     *
     * @return string
     */
    public function getThumbnailAttribute(){
        return $this->photogallery->photos_url . '/small/' . $this->image;
    }
}
