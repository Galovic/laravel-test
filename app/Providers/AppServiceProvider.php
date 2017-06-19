<?php

namespace App\Providers;

use App\Helpers\Functions;
use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use App\Models\Service;
use App\Observers\UrlObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('forbidden_url_characters', function ($attribute, $value, $parameters, $validator) {
            $badChars = Functions::getForbiddenUrlCharacters($value);
            if ($badChars) {
                $validator->errors()->add('url', 'Url obsahuje nepovolené znaky: "' . join('", "', $badChars) . '".');
            }
            return true;
        });

        Article::observe(UrlObserver::class);
        Service::observe(UrlObserver::class);
        Category::observe(UrlObserver::class);
        Page::observe(UrlObserver::class);
        Photogallery::observe(UrlObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
