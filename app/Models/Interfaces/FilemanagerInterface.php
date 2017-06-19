<?php

namespace App\Models\Interfaces;

interface FilemanagerInterface {

    /**
     * Get files for given path
     *
     * @param string $path
     * @return array
     */
    public function getFilemanagerFiles($path);

    /**
     * Get directories for given path
     *
     * @param string $path
     * @return array
     */
    public function getFilemanagerDirectories($path);

    /**
     * Get file from given path
     *
     * @param string $path
     * @param bool $preview
     * @return string|null
     */
    public function getFile($path, $preview = false);

}