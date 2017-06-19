<?php

namespace App\Observers;


use App\Models\Interfaces\UrlInterface;
use App\Models\Web\Url;

class UrlObserver
{
    /**
     * Listen to the model created event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function created(UrlInterface $model)
    {
        if (method_exists($model, 'createUrl')) {
            $model->createUrl();
        } else {
            Url::create($model->getUrlData());
        }
    }


    /**
     * Listen to the model updated event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function updated(UrlInterface $model)
    {
        if ($model->isDirty('url')) {

            if (method_exists($model, 'updateUrl')) {
                $model->updateUrl();
            } else {
                $url = $model->getUrlScope()->first();

                if ($url) {
                    $url->update($model->getUrlData());
                } else {
                    Url::create($model->getUrlData());
                }
            }
        }
    }


    /**
     * Listen to the model deleting event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function deleted(UrlInterface $model)
    {
        if (method_exists($model, 'deleteUrl')) {
            $model->deleteUrl();
        } else {
            $model->getUrlScope()->delete();
        }
    }


    /**
     * Listen to the model restore event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function restored(UrlInterface $model)
    {
        if (method_exists($model, 'restoreUrl')) {
            $model->restoreUrl();
        } else {
            $urlData = $this->getUrlData();

            if (Url::findUrl($urlData['url'])) return;
            Url::create($urlData);
        }
    }
}