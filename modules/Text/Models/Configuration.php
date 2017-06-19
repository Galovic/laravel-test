<?php

namespace Modules\Text\Models;

use App\Models\Interfaces\FilemanagerInterface;
use App\Models\Interfaces\ModuleConfigurationInterface;
use App\Traits\FilemanagerHelpers;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model implements FilemanagerInterface, ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_text_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'content' ];


    /**
     * Render module
     *
     * @return string
     */
    public function render(){
        return $this->content;
    }

    /**
     * Get files for given path
     *
     * @param string $path
     * @return array
     */
    public function getFilemanagerFiles($path)
    {
        return array_map(function ($file) {
            $name = \File::name($file);
            $extension = \File::extension($file);

            $routeProperties = [
                'model' => 'module-text',
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

    /**
     * Get directories for given path
     *
     * @param string $path
     * @return array
     */
    public function getFilemanagerDirectories($path)
    {
        return [];
    }

    /**
     * Get file from given path
     *
     * @param string $path
     * @param bool $preview
     * @return string|null
     */
    public function getFile($path, $preview = false)
    {
        if (!$path) return null;

        $pathParts = explode('/', $path);
        $file = null;

        if (count($pathParts) === 1) {
            $subDirectory = $preview ? '/thumbnails/' : '/';
            $file = FilemanagerHelpers::getSharedPath() . $subDirectory . $pathParts[0];
        }

        if ($file && \File::exists($file)) {
            return $file;
        }

        return null;
    }

    /**
     * Fix urls in text
     */
    public function fixUrlsInContent() {
        $this->content = str_replace(
            '/storage/module-text/0/',
            '/storage/module-text/' . $this->id . '/',
            $this->content
        );
    }

    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        return $this->fill($inputs);
    }
}
