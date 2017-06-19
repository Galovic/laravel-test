<?php

namespace App\Http\Controllers\Admin\Filemanager;

use App\Http\Controllers\Controller;
use App\Models\Article\Article;
use App\Traits\FilemanagerHelpers;

/**
 * Class LfmController
 * @package App\Http\Controllers\Admin\Filemanager
 */
class FilemanagerController extends Controller
{
    use FilemanagerHelpers;

    public function __construct()
    {
        $this->middleware(config('filemanager.middlewares'));
        $this->applyIniOverrides();
    }

    /**
     * Show the filemanager
     *
     * @param string $model
     * @param int $id
     * @return mixed
     */
    public function show($model, $id = 0)
    {
        return view('admin.vendor.filemanager.index')
            ->with(compact('model', 'id'));
    }

    /**
     * Return configuration errors
     *
     * @return array
     */
    public function getErrors()
    {
        $arr_errors = [];

        if (! extension_loaded('gd') && ! extension_loaded('imagick')) {
            array_push($arr_errors, trans('filemanager.message-extension_not_found'));
        }

        $type_key = $this->currentLfmType();
        $mine_config = 'filemanager.valid_' . $type_key . '_mimetypes';
        $config_error = null;

        if (!is_array(config($mine_config))) {
            array_push($arr_errors, 'Config : ' . $mine_config . ' is not a valid array.');
        }

        return $arr_errors;
    }
}
