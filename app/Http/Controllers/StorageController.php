<?php

namespace App\Http\Controllers;


use App\Traits\FilemanagerHelpers;

class StorageController extends BaseController
{
    use FilemanagerHelpers;

    /**
     * Download a file
     *
     * @return mixed
     */
    public function download($model, $id, $path)
    {
        $object = $this->getModelByModelName($model, $id);
        $file = $object->getFile($path);

        if (!$file) {
            abort(404);
        }

        return response()->download($file);
    }

    /**
     * Preview an image
     *
     * @return mixed
     */
    public function preview($model, $id, $path)
    {
        $object = $this->getModelByModelName($model, $id);
        $file = $object->getFile($path, true);

        if (!$file) {
            abort(404);
        }

        if ($this->fileIsImage($file) && !$this->imageShouldNotHaveThumb($file)) {
            return \Image::make($file)->response()
                ->header('Cache-Control', 'max-age=3600');
        }

        return null;
    }


    /**
     * Full view of an image
     *
     * @return mixed
     */
    public function fullView($model, $id, $path)
    {
        $object = $this->getModelByModelName($model, $id);
        $file = $object->getFile($path, false);

        if (!$file) {
            abort(404);
        }

        if ($this->fileIsImage($file) && !$this->imageShouldNotHaveThumb($file)) {
            return \Image::make($file)->response()
                ->header('Cache-Control', 'max-age=3600');
        }

        return null;
    }
}
