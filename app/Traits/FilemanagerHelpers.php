<?php

namespace App\Traits;

use App\Models\Article\Article;
use App\Models\Photogallery\Photogallery;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FilemanagerHelpers
{
    /*****************************
     ***       Path / Url      ***
     *****************************/

    private $ds = '/';

    public function getThumbPath($image_name = null)
    {
        return $this->getCurrentPath($image_name, 'thumb');
    }

    public function getCurrentPath($file_name = null, $is_thumb = null)
    {
        $path = $this->composeSegments('dir', $is_thumb, $file_name);

        $path = $this->translateToOsPath($path);

        return base_path($path);
    }

    public function getThumbUrl($image_name = null)
    {
        return $this->getFileUrl($image_name, 'thumb');
    }

    public function getFileUrl($image_name = null, $is_thumb = null)
    {
        return url($this->composeSegments('url', $is_thumb, $image_name));
    }

    private function composeSegments($type, $is_thumb, $file_name)
    {
        $full_path = implode($this->ds, [
            $this->getPathPrefix($type),
            $this->getFormatedWorkingDir(),
            $this->appendThumbFolderPath($is_thumb),
            $file_name
        ]);

        $full_path = $this->removeDuplicateSlash($full_path);
        $full_path = $this->translateToLfmPath($full_path);

        return $this->removeLastSlash($full_path);
    }

    public function getPathPrefix($type)
    {
        $default_folder_name = 'files';
        if ($this->isProcessingImages()) {
            $default_folder_name = 'photos';
        }

        $prefix = config('filemanager.' . $this->currentLfmType() . 's_folder_name', $default_folder_name);
        $base_directory = config('filemanager.base_directory', 'public');

        if ($type === 'dir') {
            $prefix = $base_directory . '/' . $prefix;
        }

        if ($type === 'url' && $base_directory !== 'public') {
            $prefix = 'laravel-filemanager/' . $prefix;
        }

        return $prefix;
    }

    private function getFormatedWorkingDir()
    {
        $working_dir = request('working_dir');

        if (empty($working_dir)) {
            $default_folder_type = 'share';
            if ($this->allowMultiUser()) {
                $default_folder_type = 'user';
            }

            $working_dir = $this->rootFolder($default_folder_type);
        }

        return $this->removeFirstSlash($working_dir);
    }

    private function appendThumbFolderPath($is_thumb)
    {
        if (!$is_thumb) {
            return;
        }

        $thumb_folder_name = config('filemanager.thumb_folder_name');
        // if user is inside thumbs folder, there is no need
        // to add thumbs substring to the end of url
        $in_thumb_folder = str_contains($this->getFormatedWorkingDir(), $this->ds . $thumb_folder_name);

        if (!$in_thumb_folder) {
            return $thumb_folder_name . $this->ds;
        }
    }

    public function rootFolder($type)
    {
        if ($type === 'user') {
            $folder_name = $this->getUserSlug();
        } else {
            $folder_name = config('filemanager.shared_folder_name');
        }

        return $this->ds . $folder_name;
    }

    public function getRootFolderPath($type)
    {
        return base_path($this->getPathPrefix('dir') . $this->rootFolder($type));
    }

    public function getName($file)
    {
        $lfm_file_path = $this->getInternalPath($file);

        $arr_dir = explode($this->ds, $lfm_file_path);
        $file_name = end($arr_dir);

        return $file_name;
    }

    public function getInternalPath($full_path)
    {
        $full_path = $this->translateToLfmPath($full_path);
        $full_path = $this->translateToUtf8($full_path);
        $lfm_dir_start = strpos($full_path, $this->getPathPrefix('dir'));
        $working_dir_start = $lfm_dir_start + strlen($this->getPathPrefix('dir'));
        $lfm_file_path = $this->ds . substr($full_path, $working_dir_start);

        return $this->removeDuplicateSlash($lfm_file_path);
    }

    private function translateToOsPath($path)
    {
        if ($this->isRunningOnWindows()) {
            $path = str_replace($this->ds, '\\', $path);
        }
        return $path;
    }

    private function translateToLfmPath($path)
    {
        if ($this->isRunningOnWindows()) {
            $path = str_replace('\\', $this->ds, $path);
        }
        return $path;
    }

    private function removeDuplicateSlash($path)
    {
        return str_replace($this->ds . $this->ds, $this->ds, $path);
    }

    private function removeFirstSlash($path)
    {
        if (starts_with($path, $this->ds)) {
            $path = substr($path, 1);
        }

        return $path;
    }

    private function removeLastSlash($path)
    {
        // remove last slash
        if (ends_with($path, $this->ds)) {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    public function translateFromUtf8($input)
    {
        if ($this->isRunningOnWindows()) {
            $input = iconv('UTF-8', 'BIG5', $input);
        }

        return $input;
    }

    public function translateToUtf8($input)
    {
        if ($this->isRunningOnWindows()) {
            $input = iconv('BIG5', 'UTF-8', $input);
        }

        return $input;
    }


    /****************************
     ***   Config / Settings  ***
     ****************************/

    public function isProcessingImages()
    {
        return lcfirst(str_singular(request('type'))) === 'image';
    }

    public function isProcessingFiles()
    {
        return !$this->isProcessingImages();
    }

    public function currentLfmType()
    {
        $file_type = 'file';
        if ($this->isProcessingImages()) {
            $file_type = 'image';
        }

        return $file_type;
    }

    public function allowMultiUser()
    {
        return config('filemanager.allow_multi_user') === true;
    }

    public function allowShareFolder()
    {
        if (!$this->allowMultiUser()) {
            return true;
        }

        return config('filemanager.allow_share_folder') === true;
    }

    public function applyIniOverrides()
    {
        if (count(config('filemanager.php_ini_overrides')) == 0) {
            return;
        }

        foreach (config('filemanager.php_ini_overrides') as $key => $value) {
            if ($value && $value != 'false') {
                ini_set($key, $value);
            }
        }
    }


    /****************************
     ***     File System      ***
     ****************************/

    public function getDirectories($path)
    {
        return array_map(function ($directory) {
            return $this->objectPresenter($directory);
        }, array_filter(File::directories($path), function ($directory) {
            return $this->getName($directory) !== config('filemanager.thumb_folder_name');
        }));
    }

    /**
     * Get files of given model
     * @return array
     */
    public function getFilesWithInfo($files, $type)
    {
        $filesAll = array_map(function ($file) use ($type) {
            return $this->objectPresenter($file, $type);
        }, $files);

        return array_filter($filesAll);
    }

    public function objectPresenter($item, $type)
    {
        $editable = false;
        $is_file = isset($item->file) && is_file($item->file);
        $url = '';

        if (!$is_file) {
            $file_type = trans('filemanager.type-folder');
            $icon = 'fa-folder-o';
            $thumb_url = asset('media/images/filemanager/folder.png');
        } elseif ($this->fileIsImage($item->file)) {
            $file_type = $this->getFileType($item->file);
            $icon = 'fa-image';
            $thumb_url = $item->thumb;
            $url = $item->url;
        } else if ($type === 'Images') {
            return null;
        } else {
            $extension = strtolower(File::extension($item->name));
            $file_type = config('filemanager.file_type_array.' . $extension) ?: 'File';
            $icon = config('filemanager.file_icon_array.' . $extension) ?: 'fa-file';
            $thumb_url = null;
            $url = $item->download;
        }

        return (object)[
            'name'    => $item->name,
            'url'     => $url,
            'size'    => $is_file ? $this->humanFilesize(File::size($item->file)) : '',
            'updated' => $is_file ? filemtime($item->file) : null,
            'path'    => $item->path ?? null,
            'time'    => $is_file ? date("Y-m-d h:m", filemtime($item->file)) : '',
            'type'    => $file_type,
            'icon'    => $icon,
            'thumb'   => $thumb_url,
            'is_file' => $is_file,
            'editable' => $editable
        ];
    }

    public function createFolderByPath($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }

    public function directoryIsEmpty($directory_path)
    {
        return count(File::allFiles($directory_path)) == 0;
    }

    public function fileIsImage($file)
    {
        $mime_type = $this->getFileType($file);

        return starts_with($mime_type, 'image');
    }

    public function imageShouldNotHaveThumb($file)
    {
        $mine_type = $this->getFileType($file);
        $noThumbType = ['image/gif', 'image/svg+xml'];

        return in_array($mine_type, $noThumbType);
    }

    public function getFileType($file)
    {
        if ($file instanceof UploadedFile) {
            $mime_type = $file->getMimeType();
        } else {
            $mime_type = File::mimeType($file);
        }

        return $mime_type;
    }

    public function sortFilesAndDirectories($arr_items, $sort_type)
    {
        if ($sort_type == 'time') {
            $key_to_sort = 'updated';
        } elseif ($sort_type == 'alphabetic') {
            $key_to_sort = 'name';
        } else {
            $key_to_sort = 'updated';
        }

        uasort($arr_items, function ($a, $b) use ($key_to_sort) {
            return strcmp($a->{$key_to_sort}, $b->{$key_to_sort});
        });

        return $arr_items;
    }


    /****************************
     ***    Miscellaneouses   ***
     ****************************/

    public function getUserSlug()
    {
        if (is_callable(config('filemanager.user_field'))) {
            $slug_of_user = call_user_func(config('filemanager.user_field'));
        } else {
            $old_slug_of_user = config('filemanager.user_field');
            $slug_of_user = empty(auth()->user()) ? '' : auth()->user()->$old_slug_of_user;
        }

        return $slug_of_user;
    }

    public function error($error_type, $variables = [])
    {
        return trans('filemanager.error-' . $error_type, $variables);
    }

    public function humanFilesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    public function isRunningOnWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Get model
     *
     * @param string $modelName
     * @param int $id
     * @return mixed
     */
    protected function getModelByModelName($modelName, $id) {
        $model = null;
        $class = null;

        switch ($modelName) {
            case 'article':
                $class = Article::class;
                break;
            case 'photogallery':
                $class = Photogallery::class;
                break;
            case 'career':
                if (class_exists('\Modules\CareerPlugin\Models\Career')) {
                    $class = '\Modules\CareerPlugin\Models\Career';
                }
                break;
            case 'module-text':
                if (class_exists('\Modules\Text\Models\Configuration')) {
                    $class = '\Modules\Text\Models\Configuration';
                }
                break;
            default: return abort(404);
        }

        if ($class) {
            if (intval($id) === 0) {
                $model = new $class();
            } else {
                $model = $class::findOrFail($id);
            }
        }

        return $model;
    }


    /**
     * Get shared directory path
     * @return string
     */
    static function getSharedPath() {
        return public_path( config('admin.path_upload') ) . '/' . config('admin.shared_directory');
    }
}
