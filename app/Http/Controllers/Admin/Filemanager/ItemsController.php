<?php
namespace App\Http\Controllers\Admin\Filemanager;

/**
 * Class ItemsController
 * @package App\Http\Controllers\Admin\Filemanager
 */
class ItemsController extends FilemanagerController
{
    /**
     * Get the images to load for a selected folder
     *
     * @param string $model
     * @param int $id
     * @return array
     */
    public function getItems($model, $id)
    {
        $sort_type = request('sort_type');
        $type = request('type');
        $workingDir = request('working_dir', '/');

        if ($type !== 'Images') {
            $workingDir = '/uploaded';
        }

        $model = $this->getModelByModelName($model, $id);

        $files = parent::sortFilesAndDirectories(
            $this->getFilesWithInfo($model->getFilemanagerFiles($workingDir), $type),
            $sort_type
        );

        $directories = parent::sortFilesAndDirectories(
            $this->getFilesWithInfo($model->getFilemanagerDirectories($workingDir), $type),
            $sort_type
        );

        return [
            'items' => array_merge($directories, $files)
        ];
    }
}
