<?php

namespace App\Models\Article;

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
    protected $table = 'article_photos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'author', 'image', 'sort'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $casts = [
        'size' => 'float'
    ];


    /**
     * Article
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article()
    {
        return $this->belongsTo('App\Models\Article\Article', 'article_id');
    }


    /**
     * Image url
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->article->photos_url . '/big/' . $this->image;
    }


    /**
     * Image url
     *
     * @return string
     */
    public function getThumbnailAttribute()
    {
        return $this->article->photos_url . '/small/' . $this->image;
    }


    /**
     * Format all results for views
     *
     * @return Array
     */
    public function formatResults($result)
    {
        $return = [];

        foreach ($result as $r) {
            $data = $r->toArray();

            $images = [];

            foreach (config('admin.image_crop.photogallery') as $crop => $c) {
                $image_path = config('admin.path_upload') . '/articles/photogallery/' . $crop . '/' . $data['image'];
                $images[$crop] = (!empty($data['image']) && file_exists($image_path) ? asset($image_path) : asset(
                    'media/images/empty_' . $crop . '.png'
                ));
            }

            $data['image'] = $images;

            array_push($return, $data);
        }

        return $return;
    }
}
