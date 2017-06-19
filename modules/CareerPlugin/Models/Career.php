<?php

namespace Modules\CareerPlugin\Models;

use App\Models\Interfaces\UrlInterface;
use App\Models\Web\Url;
use App\Models\Web\ViewData;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\FilemanagerHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Image;
use Intervention\Image\Constraint;

class Career extends Model implements UrlInterface
{
    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'module_careers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'url', 'perex', 'salary', 'bound', 'offerings', 'requirements',
        'seo_title', 'seo_description', 'seo_keywords', 'sort', 'status'
    ];

    /**
     * Full url attribute
     * @var string
     */
    private $fullUrl;

    /**
     * The attributes that are dates
     *
     * @var array
     */
    public $dates = [ 'deleted_at' ];

    /**
     * The attributes that are set to null when the value is empty
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'salary', 'bound', 'offerings', 'requirements',
        'seo_title', 'seo_description', 'seo_keywords',
    ];


    /**
     * Sort career opportunities
     */
    public function scopeSort($query)
    {
        $query->orderBy('sort', 'ASC')
            ->orderBy('created_at', 'DESC');
    }


    /**
     * Relation with user
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
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
            'description' => $this->seo_description ?: $this->perex,
            'keywords' => $this->seo_keywords?: $this->title,
        ]);
    }


    /**
     * Find service by url
     *
     * @param $url
     * @return self|null
     */
    static function findByUrl($url){
        return self::where('url', $url)->first();
    }


    /**
     * Get URL record
     *
     * @return mixed
     */
    public function getUrlScope(){
        return Url::where('model', self::class)->where('model_id', $this->id);
    }


    /**
     * Save service
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        // set sort attribute
        if(!isset($this->attributes['sort']) || !$this->attributes['sort'] || $this->attributes['sort'] < 1){
            $highestSortService = Career::whereLanguage($this->language_id)
                ->sort()->first();
            $this->attributes['sort'] = $highestSortService ? $highestSortService->sort + 1 : 1;
        }

        return parent::save($options);
    }


    /**
     * Get name of image directory
     *
     * @return string
     */
    public function getImagesDirAttribute(){
        return md5($this->created_at->format('Y-m-d') . '-' . $this->id);
    }


    /**
     * Return path to image directory of this career
     *
     * @return string
     */
    public function getImagesPathAttribute() {
        if($this->exists){
            return public_path( config('admin.path_upload') ) . '/module_career/' . $this->images_dir;
        }else{
            return self::getTempPath();
        }
    }


    /**
     * Get thumbnail file name
     *
     * @return string|null
     */
    public function getThumbnailAttribute(){
        if (!$this->image) return null;
        return str_replace('image', 'thumbnail', $this->image);
    }


    /**
     * Get path to image
     *
     * @return string|null
     */
    public function getImagePathAttribute(){
        return $this->images_path . "/" . $this->image;
    }


    /**
     * Get path to thumbnail
     *
     * @return string|null
     */
    public function getThumbnailPathAttribute(){
        return $this->images_path . "/" . $this->thumbnail;
    }


    /**
     * Return image url
     *
     * @return string|null
     */
    public function getImageUrlAttribute(){
        if(!$this->image) return null;

        return url('/') . '/' . config('admin.path_upload') . '/module_career/' . $this->images_dir . '/' . $this->image;
    }


    /**
     * Return thumbnail url
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute(){
        if(!$this->image){
            return asset('media/admin/images/thumbnail100x100.png');
        }

        return url('/') . '/' . config('admin.path_upload') . '/module_career/' . $this->images_dir . '/' . $this->thumbnail;
    }


    /**
     * Create thumbnail from image
     */
    public function createThumbnail(){
        if(!$this->image) return;

        Image::make($this->image_path)
            ->fit(100, 100, function (Constraint $constraint){
                $constraint->upsize();
            })
            ->save($this->thumbnail_path);
    }


    /**
     * Temporary path for careers
     *
     * @return string
     */
    static function getTempPath(){
        return public_path( config('admin.path_upload') ) . '/module_career/temp/' . self::getTempDir();
    }


    /**
     * Temporary directory for careers
     *
     * @return string
     */
    static function getTempDir(){
        return request('_token') ?: request()->header('X-CSRF-TOKEN', csrf_token());
    }


    /**
     * Get files for career
     *
     * @param $path
     * @return array
     */
    public function getFilemanagerFiles($path) {
        $files = [];

        if (($path === '' || $path === '/') && $this->image) {
            $routeProperties = [
                'model' => 'career',
                'id' => $this->id ?: 0,
                'path' => 'image'
            ];

            $files[] = (object)[
                'name' => 'Obrázek kariéry',
                'file' => $this->image_path,
                'thumb' => route('storage.preview', $routeProperties),
                'url' => route('storage.fullView', $routeProperties),
                'download' => route('storage.download', $routeProperties)
            ];
        } else if ($path === '/uploaded') {
            return array_map(function ($file) {
                $name = \File::name($file);
                $extension = \File::extension($file);

                $routeProperties = [
                    'model' => 'career',
                    'id' => $this->id ?: 0,
                    'path' => $name . '.' . $extension
                ];

                return (object)[
                    'name' => $name,
                    'file' => $file,
                    'thumb' => route('storage.preview', $routeProperties),
                    'url' => route('storage.fullView', $routeProperties),
                    'download' => route('storage.download', $routeProperties)
                ];
            }, \File::files(FilemanagerHelpers::getSharedPath()));
        }

        return $files;
    }


    /**
     * Get directories for career
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
                    'name' => 'Nahrané',
                    'path' => '/uploaded'
                ];
                break;
        }

        return $directories;
    }


    /**
     * Get file of career using given path
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
            } else {
                $subDirectory = $preview ? '/thumbnails/' : '/';
                $fileName = str_replace('..', '', $pathParts[0]);
                $file = FilemanagerHelpers::getSharedPath() . $subDirectory . $fileName;
            }
        }

        if ($file && \File::exists($file)) {
            return $file;
        }

        return null;
    }


    /**
     * Fix urls in text
     */
    public function fixUrlsInTexts() {
        $search = '/storage/career/0/';
        $replace = '/storage/career/' . $this->id . '/';
        $this->perex = str_replace($search, $replace, $this->perex);
        $this->offerings = str_replace($search, $replace, $this->offerings);
        $this->requirements = str_replace($search, $replace, $this->requirements);
    }

    /**
     * Get data for Url model
     *
     * @return array
     */
    public function getUrlData()
    {
        $newUrl = $this->language->language_code . '/' .
            trans('module-careerplugin::options.url_prefix') . '/' . $this->url;

        return [
            'url' => $newUrl,
            'model' => self::class,
            'model_id' => $this->id
        ];
    }
}
