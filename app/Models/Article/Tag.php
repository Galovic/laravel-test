<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;


class Tag extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name' ];


    /**
     * Find tag by name
     *
     * @param string $name
     * @return Tag|null
     */
    static function findNamed($name) {
        return self::where('name', $name)->first();
    }


    /**
     * Articles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function articles() {
        return $this->belongsToMany(Article::class, 'article_tags', 'tag_id', 'article_id');
    }
}