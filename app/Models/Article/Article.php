<?php

namespace App\Models\Article;

use App\Models\Interfaces\FilemanagerInterface;
use App\Models\Interfaces\UrlInterface;
use App\Models\Web\Url;
use App\Models\Web\ViewData;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\FilemanagerHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model implements FilemanagerInterface, UrlInterface
{
    use SoftDeletes, AdvancedEloquentTrait;

    protected $searchable = [
        'columns' => [
            'articles.title' => 10,
            'articles.perex' => 8,
            'articles.text' => 7,
            'articles.seo_title' => 3,
            'articles.seo_description' => 2,
            'users.first_name' => 2,
            'users.last_name' => 2,
        ],
        'joins' => [
            'users' => ['articles.user_id', 'users.id'],
        ],
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'articles';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'type', 'url', 'perex', 'text', 'image', 'thumbnail',
        'seo_title', 'seo_description', 'seo_keywords', 'publish_at', 'unpublish_at'
    ];


    /**
     * The attributes that are set to null when the value is empty
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'perex', 'text', 'image', 'thumbnail',
        'seo_title', 'seo_description', 'seo_keywords',
    ];


    /**
     * @var array
     */
    public $dates = [ 'deleted_at', 'publish_at', 'unpublish_at' ];


    /**
     * Need to update urls?
     *
     * @var bool
     */
    private $updateUrls = false;


    /**
     * Adds +1 to view count
     */
    public function addView()
    {
        $agent = new Agent();
        if (!$agent->isRobot())
        {
            $this->views++;
            $this->update();
        }
    }


    // --- NEW ---


    /**
     * Return image url
     *
     * @return string
     */
    public function getImageUrlAttribute(){
        if(!$this->image) return null;

        return $this->photos_url . '/' . $this->image;
    }


    /**
     * Return image url
     *
     * @return string
     */
    public function getImagePathAttribute(){
        if(!$this->image) return null;

        return $this->photogallery_path . '/' . $this->image;
    }


    /**
     * Return thumbnail url
     *
     * @return string
     */
    public function getThumbnailUrlAttribute(){
        if(!$this->image) return null;

        return $this->photos_url . '/' . str_replace('image', 'thumb', $this->image);
    }


    /**
     * Return thumbnail path
     *
     * @return string
     */
    public function getThumbnailPathAttribute(){
        if(!$this->image) return null;

        return $this->photogallery_path . '/' . str_replace('image', 'thumb', $this->image);
    }


    /**
     * Select only published articles
     *
     * @param $query
     */
    public function scopePublished($query)
    {
        $query->where('status', 1)
            ->where('publish_at', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('unpublish_at', '>=', Carbon::now())
                    ->orWhere('unpublish_at', null);
            });
    }


    /**
     * Is article public?
     *
     * @return bool
     */
    public function getIsPublicAttribute() {
        if (!$this->exists) {
            return false;
        }

        if (!$this->publish_at || $this->publish_at->gt(Carbon::now())) {
            return false;
        }

        if ($this->unpublish_at && $this->unpublish_at->le(Carbon::now())) {
            return false;
        }

        return true;
    }


    /**
     * User who created article
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }


    /**
     * Photos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos(){
        return $this->hasMany('\App\Models\Article\Photo', 'article_id');
    }


    /**
     * Categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(){
        return $this->belongsToMany(
            'App\Models\Article\Category',
            'articles_categories',
            'article_id',
            'category_id'
        );
    }


    /**
     * Find article by url
     *
     * @param $url
     * @return Article|null
     */
    static function findByUrl($url){
        return self::where('url', $url)->first();
    }


    /**
     * Return photogallery dir name
     *
     * @return string
     */
    public function getPhotogalleryDirAttribute(){
        return md5($this->created_at->format('Y-m-d H:i:s') . '-' . $this->id);
    }


    /**
     * Return photogallery dir name
     *
     * @return string
     */
    public function getPhotogalleryPathAttribute(){
        if($this->exists){
            return public_path( config('admin.path_upload') ) . '/articles/' . $this->photogallery_dir;
        }else{
            return self::getPhotosTempPath() . '/done';
        }
    }


    /**
     * Return photogallery dir name
     *
     * @return string
     */
    public function getPhotosUrlAttribute(){
        if($this->exists){
            return url('/') . '/' . config('admin.path_upload') . '/articles/' . $this->photogallery_dir;
        }else{
            return url('/') . '/' . config('admin.path_upload') . '/articles/temp/' . self::getPhotosTempDir() . '/done';
        }
    }


    /**
     * Temporary path for photos
     *
     * @return string
     */
    static function getPhotosTempPath(){
        return public_path( config('admin.path_upload') ) . '/articles/temp/' . self::getPhotosTempDir();
    }


    /**
     * Temporary directory for photos
     *
     * @return string
     */
    static function getPhotosTempDir(){
        return request('_token') ?: request()->header('X-CSRF-TOKEN', csrf_token());
    }


    /**
     * Join category
     *
     * @param $query
     */
    public function scopeJoinCategories($query){
        $query->join(
            $articleCategoriesTable = 'articles_categories',
            "{$articleCategoriesTable}.article_id", '=', "{$this->table}.id"
        )->join(
            $categoriesTable = Category::getTableName(),
            "{$categoriesTable}.id", '=', "{$articleCategoriesTable}.category_id"
        );
    }


    /**
     * Select articles only
     *
     * @param $query
     */
    public function scopeArticlesOnly($query){
        $query->joinCategories()
            ->where(Category::getTableName() . ".flag", 'articles');
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
     * Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function language(){
        return $this->hasOne('App\Models\Web\Language', 'id', 'language_id');
    }


    /**
     * Return view data
     *
     * @param $data
     * @return ViewData
     */
    public function getViewData($data){
        if(!$data) {
            $data = new ViewData();
        }

        return $data->fill([
            'title' => $this->seo_title ?: $this->title,
            'description' => $this->seo_description,
            'keywords' => $this->seo_keywords,
        ]);
    }


    /**
     * Select articles only
     *
     * @param $query
     * @param string $dir
     */
    public function scopeOrderPublish($query, $dir = 'desc'){
        $query->orderBy("{$this->table}.publish_at", $dir);
    }


    /**
     * Get main category and set full url for article
     */
    public function renewMainCategory()
    {
        // TODO: necessary?

        $categories = $this->categories;
        $allCategories = Category::all()->toHierarchy();
        $url = '';

        foreach ($allCategories as $cat) {
            if ($cat->id == $categories->first()->id) {
                $url = $cat->url;
                break;
            }

            if (isset($cat->children) && is_array($cat->children) && !empty($cat->children)) {
                foreach ($cat->children as $cat_child) {
                    if ($cat_child->id == $categories->first()->id) {
                        $url = $cat_child->url_nested;
                        break;
                    }
                }
            }
        }

        if (isset($url)) {
            $this->url_full = $url . '/' . $this->url;
            $this->category_id = $categories->first()->id;
            $this->save();
        }
    }


    /**
     * Create photo for article
     *
     * @param string $fileName
     * @return Photo
     */
    public function createPhoto($fileName)
    {
        $photo = new Photo([
            'image' => $fileName,
            'sort' => 0,
            'user_id' => auth()->id()
        ]);

        if($this->exists) {
            $photo->article_id = $this->id;
        }else{
            $photo->temporary_id = request('_token') ?: request()->header('X-CSRF-TOKEN');
        }

        return $photo;
    }


    /**
     * Return scope of article photos
     *
     * @return mixed
     */
    public function getPhotosScope(){
        if($this->exists){
            return Photo::where('article_id', $this->id);
        }
        return Photo::where('temporary_id', self::getPhotosTempDir())->whereNull('article_id');
    }


    /**
     * Update article categories
     *
     * @param array $categories
     */
    public function syncCategories(array $categories)
    {
        $result = $this->categories()->sync($categories);

        // Generate URLs
        if($this->updateUrls || $result['attached'] || $result['detached']){

            $this->updateUrls($categories);

        }
    }


    /**
     * Updates URL addresses for specified categories
     *
     * @param array $categoryIds
     */
    private function updateUrls(array $categoryIds){
        $this->getUrlScope()->delete();

        foreach($categoryIds as $categoryId){
            $categoryUrl = Url::where('model', Category::class)->where('model_id', $categoryId)->first();

            Url::create($this->getUrlData($categoryUrl->url));
        }
    }


    /**
     * Create article Url.
     */
    public function createUrl() {
        $this->updateUrls = true;
    }


    /**
     * Update article Url.
     */
    public function updateUrl() {
        $this->updateUrls = true;
    }


    /**
     * Restore article Url.
     */
    public function restoreUrl() {
        $categoryIds = $this->categories()->pluck('id')->toArray();

        $this->updateUrls($categoryIds);
    }


    /**
     * Get files for article.
     *
     * @param $path
     * @return array
     */
    public function getFilemanagerFiles($path) {
        $files = [];

        switch ($path) {
            case '':
            case '/':
                if($this->image) {
                    $routeProperties = [
                        'model' => 'article',
                        'id' => $this->id ?: 0,
                        'path' => 'image'
                    ];

                    $files[] = (object)[
                        'name' => 'Obrázek článku',
                        'file' => $this->image_path,
                        'thumb' => route('storage.preview', $routeProperties),
                        'url' => route('storage.fullView', $routeProperties),
                        'download' => route('storage.download', $routeProperties)
                    ];
                }
                break;

            case '/photogallery':
                return $this->getPhotosScope()->get()->map(function (Photo $photo, $index) {
                    $routeProperties = [
                        'model' => 'article',
                        'id' => $this->id ?: 0,
                        'path' => $photo->id
                    ];

                    return (object)[
                        'name' => $photo->title ?: 'Obrázek ' . $index,
                        'file' => $this->photogallery_path . '/big/' . $photo->image,
                        'thumb' => route('storage.preview', $routeProperties),
                        'url' => route('storage.fullView', $routeProperties),
                        'download' => route('storage.download', $routeProperties)
                    ];
                })->toArray();

                break;

            case '/uploaded':
                return array_map(function ($file) {
                    $name = \File::name($file);
                    $extension = \File::extension($file);

                    $routeProperties = [
                        'model' => 'article',
                        'id' => $this->id ?: 0,
                        'path' => 'uploaded/' . $name . '.' . $extension
                    ];

                    return (object)[
                        'name' => $name,
                        'file' => $file,
                        'thumb' => route('storage.preview', $routeProperties),
                        'url' => route('storage.fullView', $routeProperties),
                        'download' => route('storage.download', $routeProperties)
                    ];
                }, \File::files(FilemanagerHelpers::getSharedPath()));

                break;
        }

        return $files;
    }


    /**
     * Get directories for article.
     *
     * @param $path
     * @return array
     */
    public function getFilemanagerDirectories($path) {
        $directories = [];

        switch ($path) {
            case '':
            case '/':
                $directories[] = (object)[
                    'name' => 'Fotogalerie',
                    'path' => '/photogallery'
                ];
                $directories[] = (object)[
                    'name' => 'Nahrané',
                    'path' => '/uploaded'
                ];
                break;
        }

        return $directories;
    }

    /**
     * Get file of article using given path.
     *
     * @param $path
     * @return null|string
     */
    public function getFile($path, $preview = false) {
        if (!$path || (!$this->is_public && !auth()->check())) return null;

        $pathParts = explode('/', $path);
        $file = null;

        if (count($pathParts) === 1) {
            if ($pathParts[0] === 'image') {
                $file = $preview ? $this->thumbnail_path : $this->image_path;
            } else if ($photoId = intval($pathParts[0])) {
                /** @var Photo $photo */
                $photo = $this->getPhotosScope()->find($photoId);

                if ($photo) {
                    $subDirectory = $preview ? '/small/' : '/big/';
                    $file = $this->photogallery_path . $subDirectory . $photo->image;
                }
            }
        } else if (count($pathParts) === 2 && $pathParts[0] === 'uploaded') {
            $subDirectory = $preview ? '/thumbnails/' : '/';
            $file = FilemanagerHelpers::getSharedPath() . $subDirectory . $pathParts[1];
        }

        if ($file && \File::exists($file)) {
            return $file;
        }

        return null;
    }


    /**
     * Fix urls in text
     */
    public function fixUrlsInText() {
        $this->text = str_replace(
            '/storage/article/0/',
            '/storage/article/' . $this->id . '/',
            $this->text
        );
    }


    /**
     * Get data for Url model
     *
     * @param string $categoryUrl
     * @return array
     */
    public function getUrlData($categoryUrl = '')
    {
        return [
            'url' => $categoryUrl . '/' . $this->url,
            'model' => self::class,
            'model_id' => $this->id
        ];
    }


    /**
     * Tags
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags() {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id');
    }


    /**
     * Update article tags
     *
     * @param array $tagNames
     */
    public function syncTags(array $tagNames)
    {
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tag = Tag::findNamed($tagName);

            if (!$tag) {
                $tag = Tag::create([
                    'name' => $tagName
                ]);
            }

            $tagIds[] = $tag->id;
        }

        $this->tags()->sync($tagIds);
    }
}
