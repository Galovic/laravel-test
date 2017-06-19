<?php

namespace App\Http\Controllers;

use App\Models\Article\Article;
use App\Models\Page\Page;
use App\Models\Web\Language;
use App\Models\Web\Url;
use Illuminate\Http\Response;

class SiteController extends BaseController
{
    /**
     * Display robots.txt for environments
     *
     * @return Response
     */
    public function robots()
    {
        return response()->view('site.robots', [
            'isProduction' => \App::environment() === 'production'
        ])->header('Content-Type', 'text/plain');
    }


    /**
     * Create sitemap index
     * @return Response
     */
    public function sitemapIndex() {
        $languages = Language::enabled()->get();

        return response()->view('site.sitemapindex', [
            'languages' => $languages
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Sitemap for concrete language
     *
     * @param $languageCode
     * @return Response
     */
    public function sitemap($languageCode)
    {
        $language = Language::findByUrlCode($languageCode);

        if (!$language) {
            return abort(404);
        }

        $urls = Url::whereLanguage($language)->get();
        $urlSet = collect([]);
        $homepage = Page::getHomepage($language);

        /** @var Url $url */
        foreach ($urls as $url) {
            $model = $url->getInstance();

            if (!$model) {
                continue;
            }

            $priority = 0.6;

            switch ($url->model) {
                case Page::class:
                    $priority = 0.8;
                    break;
                case Article::class:
                    $priority = 0.7;
                    break;
            }

            /**
             * http://michalkubicek.cz/jak-na-prioritu-a-frekvenci-v-sitemap-xml/
             */
            $urlSet->push((object)[
                'loc' => $url->url,
                'lastmod' => $model->updated_at ?: $homepage->updated_at,
                'changefreq' => 'weekly',
                'priority' => $priority
            ]);
        }

        $alternateLanguages = Language::enabled()->where('language_code', '<>', $languageCode)->get();

        return response()->view('site.sitemap', [
            'urlSet' => $urlSet,
            'language' => $language,
            'alternateLanguages' => $alternateLanguages
        ])->header('Content-Type', 'text/xml');
    }

}
