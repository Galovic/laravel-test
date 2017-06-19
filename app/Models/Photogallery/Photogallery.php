<?php

namespace App\Models\Photogallery;

use App\Models\Interfaces\FilemanagerInterface;
use App\Models\Interfaces\UrlInterface;
use App\Models\Web\Language;
use App\Models\Web\Url;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\FilemanagerHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photogallery extends Model implements FilemanagerInterface, UrlInterface
{
    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'photogalleries';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'url', 'text', 'sort', 'seo_title', 'seo_description',
        'seo_keywords', 'publish_at', 'unpublish_at'
    ];


    /**
     * The attributes that are set to null when the value is empty
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'text', 'seo_title', 'seo_description', 'seo_keywords',
    ];


    /**
     * @var array
     */
    public $dates = [ 'deleted_at', 'publish_at', 'unpublish_at' ];


    /**
     * Select only published articles
     *
     * @param $query
     */
    public function scopePublished($query)
    {
        $query->where('publish_at', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('unpublish_at', '>=', Carbon::now())
                    ->orWhere('unpublish_at', null);
            });
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
        return $this->hasMany('\App\Models\Photogallery\Photo', 'photogallery_id');
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
     * Find article by url
     *
     * @param $url
     * @return Photogallery|null
     */
    static function findByUrl($url){
        return self::where('url', $url)->first();
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
            return public_path( config('admin.path_upload') ) . '/photogalleries/' . $this->photogallery_dir;
        }else{
            return self::getTempPath() . '/done';
        }
    }


    /**
     * Return photogallery dir name
     *
     * @return string
     */
    public function getPhotosUrlAttribute(){
        if($this->exists){
            return url('/') . '/' . config('admin.path_upload') . '/photogalleries/' . $this->photogallery_dir;
        }else{
            return url('/') . '/' . config('admin.path_upload') . '/photogalleries/temp/' . self::getTempDir() . '/done';
        }
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
     * Select articles only
     *
     * @param $query
     * @param string $dir
     */
    public function scopeOrderPublish($query, $dir = 'desc'){
        $query->orderBy("{$this->table}.publish_at", $dir);
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
            $photo->photogallery_id = $this->id;
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
            return Photo::where('photogallery_id', $this->id);
        }
        return Photo::where('temporary_id', self::getTempDir())->whereNull('photogallery_id');
    }


    /**
     * Is published?
     *
     * @return bool
     */
    public function getIsPublishedAttribute(){
        return Carbon::now()->greaterThanOrEqualTo($this->publish_at)
            && (is_null($this->unpublish_at) || Carbon::now()->lessThanOrEqualTo($this->unpublish_at));
    }


    /**
     * Temporary path for photo galleries
     *
     * @return string
     */
    static function getTempPath(){
        return public_path( config('admin.path_upload') ) . '/photogallery/temp/' . self::getTempDir();
    }


    /**
     * Temporary directory for photo galleries
     *
     * @return string
     */
    static function getTempDir(){
        return request('_token') ?: request()->header('X-CSRF-TOKEN', csrf_token());
    }


    /**
     * Get files for photo gallery
     *
     * @param $path
     * @return array
     */
    public function getFilemanagerFiles($path) {
        $files = [];

        if ($path === '/uploaded') {
            $files = array_map(function ($file) {
                $name = \File::name($file);
                $extension = \File::extension($file);

                $routeProperties = [
                    'model' => 'photogallery',
                    'id' => $this->id ?: 0,
                    'path' => 'uploaded/' . $name . '.' . $extension
                ];

                return (object)[
                    'name' => $name . '.' . $extension,
                    'file' => $file,
                    'thumb' => route('storage.preview', $routeProperties),
                    'url' => route('storage.fullView', $routeProperties),
                    'download' => route('storage.download', $routeProperties)
                ];
            }, \File::files(FilemanagerHelpers::getSharedPath()));
        } else if ($path === '/photogallery') {
            return $this->getPhotosScope()->get()->map(function (Photo $photo, $index) {

                $extension = \File::extension($photo->image);

                $routeProperties = [
                    'model' => 'photogallery',
                    'id' => $this->id ?: 0,
                    'path' => $photo->id . '.' . $extension
                ];

                return (object)[
                    'name' => $photo->title ?: 'Obrázek ' . $index,
                    'file' => $this->photogallery_path . '/big/' . $photo->image,
                    'thumb' => route('storage.preview', $routeProperties),
                    'url' => route('storage.fullView', $routeProperties),
                    'download' => route('storage.download', $routeProperties)
                ];
            })->toArray();
        }

        return $files;
    }


    /**
     * Get directories for photo gallery
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
     * Get file of photo gallery using given path
     *
     * @param $path
     * @return null|string
     */
    public function getFile($path, $preview = false) {
        if (!$path || (!$this->is_public && !auth()->check())) return null;

        $pathParts = explode('/', $path);
        $file = null;

        if (count($pathParts) === 1 && $photoId = intval($pathParts[0])) {
            /** @var Photo $photo */
            $photo = $this->getPhotosScope()->find($photoId);

            if ($photo) {
                $subDirectory = $preview ? '/small/' : '/big/';
                $file = $this->photogallery_path . $subDirectory . $photo->image;
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
        $search = '/storage/photogallery/0/';
        $replace = '/storage/photogallery/' . $this->id . '/';
        $this->text = str_replace($search, $replace, $this->text);
    }


    /**
     * Get data for Url model
     *
     * @return array
     */
    public function getUrlData()
    {
        return [
            'url' => $this->language->language_code . '/' . $this->getAttribute('url'),
            'model' => self::class,
            'model_id' => $this->id
        ];
    }
}
