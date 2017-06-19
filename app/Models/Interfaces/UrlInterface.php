<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface UrlInterface
 * @package App\Models\Interfaces
 */
interface UrlInterface {

    /**
     * Get data for Url model
     *
     * @return array
     */
    public function getUrlData();

    /**
     * Get URL record
     *
     * @return Builder
     */
    public function getUrlScope();

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null);

    /**
     * Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function language();
}