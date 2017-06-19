<?php
namespace App\Http\Controllers\Admin\Filemanager;

use App\Traits\FilemanagerHelpers;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Events\Filemanager\ImageIsUploading;
use App\Events\Filemanager\ImageWasUploaded;

/**
 * Class UploadController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class UploadController extends FilemanagerController
{
    /**
     * Upload an image/file and (for images) create thumbnail
     *
     * @param string $model
     * @param int $id
     * @return string|array
     */
    public function upload($model, $id)
    {
        $model = $this->getModelByModelName($model, $id);
        $uploadPath = self::getSharedPath();

        parent::createFolderByPath($uploadPath);

        $files = request()->file('upload');
        $error_bag = [];
        foreach (is_array($files) ? $files : [] as $file) {
            $validationMessage = $this->uploadValidator($file);

            if ($validationMessage !== 'pass') {
                array_push($error_bag, $validationMessage);
                continue;
            } else {
                $newFilename = $this->proceedSingleUpload($file, $uploadPath);
            }

            if ($newFilename === parent::error('invalid')) {
                array_push($error_bag, $newFilename);
            }
        }

        if ($error_bag) {
            return response($error_bag, 500);
        }

        return null;
    }


    /**
     * Save file
     *
     * @param $file
     * @param $uploadPath
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    private function proceedSingleUpload($file, $uploadPath)
    {
        $newFilename  = $this->getNewName($file);
        try {
            if (parent::fileIsImage($file) && !parent::imageShouldNotHaveThumb($file)) {
                Image::make($file->getRealPath())
                    ->orientate() //Apply orientation from exif data
                    ->save($uploadPath . '/' . $newFilename, 100);

                $this->makeThumb($newFilename, $uploadPath);
            } else {
                chmod($file->path(), 0644); // TODO configurable
                File::move($file->path(), $uploadPath . '/' . $newFilename);
            }
        } catch (\Exception $e) {
            dump($e);
            return parent::error('invalid');
        }

        return $newFilename;
    }

    /**
     * Validate file
     *
     * @param $file
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    private function uploadValidator($file)
    {
        $is_valid = false;
        $force_invalid = false;

        if (empty($file)) {
            return parent::error('file-empty');
        } elseif (!$file instanceof UploadedFile) {
            return parent::error('instance');
        } elseif ($file->getError() == UPLOAD_ERR_INI_SIZE) {
            $max_size = ini_get('upload_max_filesize');
            return parent::error('file-size', ['max' => $max_size]);
        } elseif ($file->getError() != UPLOAD_ERR_OK) {
            return 'File failed to upload. Error code: ' . $file->getError();
        }

        $new_filename = $this->getNewName($file);

        if (File::exists(parent::getCurrentPath($new_filename))) {
            return parent::error('file-exist');
        }

        $mimetype = $file->getMimeType();

        // size to kb unit is needed
        $file_size = $file->getSize() / 1000;
        $type_key = parent::currentLfmType();

        if (config('filemanager.should_validate_mime', false)) {
            $mine_config = 'filemanager.valid_' . $type_key . '_mimetypes';
            $valid_mimetypes = config($mine_config, []);
            if (false === in_array($mimetype, $valid_mimetypes)) {
                return parent::error('mime') . $mimetype;
            }
        }

        if (config('filemanager.should_validate_size', false)) {
            $max_size = config('filemanager.max_' . $type_key . '_size', 0);
            if ($file_size > $max_size) {
                return parent::error('size') . $mimetype;
            }
        }

        return 'pass';
    }

    /**
     * Create new file name
     *
     * @param $file
     * @return string
     */
    private function getNewName($file)
    {
        $new_filename = parent::translateFromUtf8(trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));

        if (config('filemanager.rename_file') === true) {
            $new_filename = uniqid();
        } elseif (config('filemanager.alphanumeric_filename') === true) {
            $new_filename = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_filename);
        }

        return $new_filename . '.' . $file->getClientOriginalExtension();
    }

    private function makeThumb($new_filename, $uploadPath)
    {
        // create thumb folder
        parent::createFolderByPath($uploadPath . '/thumbnails');

        // create thumb image
        Image::make($uploadPath . '/' . $new_filename)
            ->fit(config('filemanager.thumb_img_width', 200), config('filemanager.thumb_img_height', 200))
            ->save($uploadPath . '/thumbnails/' . $new_filename);
    }

    private function useFile($new_filename)
    {
        $file = parent::getFileUrl($new_filename);

        return "<script type='text/javascript'>

        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);
            return ( match && match.length > 1 ) ? match[1] : null;
        }

        var funcNum = getUrlParam('CKEditorFuncNum');

        var par = window.parent,
            op = window.opener,
            o = (par && par.CKEDITOR) ? par : ((op && op.CKEDITOR) ? op : false);

        if (op) window.close();
        if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, '$file');
        </script>";
    }
}
