<?php

namespace App\Helpers;

use App\Models\Interfaces\UrlInterface;
use App\Models\Page\Page;
use App\Models\Web\Language;
use App\Models\Web\Settings;
use App\Models\Web\Url;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UrlFactory
{
    /** @var integer */
    private $languageDisplaySetting;

    /** @var boolean */
    private $showDefaultLanguageInUrl;

    /** @var Language */
    private $language;

    /** @var Collection|null */
    private $urls;

    /** @var array|null */
    private $mappedUrls;

    /** @var array|null */
    private $mappedModels;


    /**
     * UrlFactory constructor.
     */
    public function __construct()
    {
        $this->languageDisplaySetting = intval(
            Settings::get(
                'language_display',
                config('admin.language_url.directory')
            )
        );

        $this->showDefaultLanguageInUrl = false;

        if ($this->languageDisplaySetting === config('admin.language_url.directory')) {
            $this->showDefaultLanguageInUrl = !intval(
                Settings::get('default_language_hidden', false)
            );
        }

        $this->urls = null;
        $this->mappedUrls = null;
        $this->mappedModels = null;
    }


    /**
     * Get language.
     *
     * @return Language
     */
    public function getLanguage() {
        return $this->language;
    }


    /**
     * Ser language. Recommended only for administration.
     *
     * @param Language $language
     * @return $this
     */
    public function setLanguage(Language $language) {
        $this->language = $language;
        return $this;
    }


    /**
     * Get url of model
     *
     * @param UrlInterface $model
     * @return string
     */
    public function getObjectUrl(UrlInterface $model, $default = '#') {

        $this->resolveModelLanguage($model);

        $urlModel = $model->getUrlScope()->first();

        if ($urlModel) {
            return $urlModel->url;
        }

        return $default;
    }


    /**
     * Get url of model - class and id.
     *
     * @param string $class
     * @param int $id
     * @param string $default
     * @return string
     * @throws \Exception
     */
    public function getUrl($class, $id, $default = '#') {
        if (!$this->language) {
            throw new \Exception('Cannot get url. Language is not specified!');
        }

        if (is_null($this->mappedModels)) {
            $this->mapModels();
        }

        if (isset($this->mappedModels[$class]) && isset($this->mappedModels[$class][$id])) {
            /** @var Url $urlModel */
            $urlModel = $this->urls->get($this->mappedModels[$class][$id]);
            if ($urlModel) {
                return $this->getPublicUrl($urlModel->url);
            }
        }

        return $default;
    }


    /**
     * Get full url of model - class and id.
     *
     * @param string $class
     * @param int $id
     * @param string $default
     * @return string
     * @throws \Exception
     */
    public function getFullUrl($class, $id, $default = '#') {
        $url = $this->getUrl($class, $id, null);

        if (is_null($url)) {
            return $default;
        }

        return route('homepage', $url);
    }


    /**
     * Get model by url
     *
     * @param $url
     * @return Model|null
     */
    public function getModel($url) {
        $this->resolveUrlLanguage($url);

        if (is_null($this->mappedUrls)) {
            $this->mapUrls();
        }

        if ($this->isUrlHomepage($url)) {
            return Page::getHomepage($this->language);
        }

        $adjustedUrl = $this->adjustUrlForMatching($url);

        if (isset($this->mappedUrls[$adjustedUrl])) {
            /** @var Url $urlModel */
            $urlModel = $this->urls->get($this->mappedUrls[$adjustedUrl]);
            return $urlModel->getInstance();
        }

        return null;
    }


    /**
     * Resolve model language.
     *
     * @param UrlInterface $model
     */
    private function resolveModelLanguage(UrlInterface $model) {

        if (!$this->language || $this->language->id !== $model->language_id) {
            $this->language = $model->language;
        }

    }


    /**
     * Resolve language from url.
     *
     * @param string $url
     * @return boolean
     */
    public function resolveUrlLanguage($url) {

        if ($this->language) {
            return true;
        }

        switch ($this->languageDisplaySetting) {
            case config('admin.language_url.directory'):

                if ($url === '/' || $url === '') {
                    break;
                }

                $firstSlashPos = strpos($url, '/');

                if ($firstSlashPos === false) {
                    $this->language = Language::findByUrlCode($url);
                } else {
                    $languageCode = substr($url, 0, $firstSlashPos);
                    $this->language = Language::findByUrlCode($languageCode);
                }

                break;
            case config('admin.language_url.subdomain'):

                $parsedUrl = parse_url($_SERVER['SERVER_NAME']);
                $explodedHost = explode('.', $parsedUrl['host']);

                if (count($explodedHost)) {
                    $this->language = Language::findByUrlCode($explodedHost[0]);
                }

                break;
            case config('admin.language_url.domain'):
                $parsedUrl = parse_url($_SERVER['SERVER_NAME']);

                $this->language = Language::where('domain', $parsedUrl['host'])->first();

                break;
        }

        // Find default if language code is not required
        if (!$this->language && !$this->showDefaultLanguageInUrl) {
            $this->language = Language::findDefault();
        }

        return !!$this->language;
    }


    /**
     * Resolve urls. The language must be specified.
     */
    private function resolveUrls() {
        // already resolved
        if (!is_null($this->urls)) {
            return;
        }

        // check if language is specified
        if (!$this->language) {
            throw new \Exception('Cannot resolve url! Language is not specified!');
        }

        $this->urls = Url::whereLanguage($this->language)->get()->keyBy('id');
    }


    /**
     * Map urls by url address
     */
    private function mapUrls() {
        $this->resolveUrls();

        $this->mappedUrls = [];
        foreach ($this->urls as $urlModel) {
            $this->mappedUrls[$urlModel->url] = $urlModel->id;
        }
    }


    /**
     * Map urls by url address
     */
    private function mapModels() {
        $this->resolveUrls();

        $this->mappedModels = [];
        foreach ($this->urls as $urlModel) {

            if (!isset($this->mappedModels[$urlModel->model])) {
                $this->mappedModels[$urlModel->model] = [];
            }

            $this->mappedModels[$urlModel->model][$urlModel->model_id] = $urlModel->id;
        }
    }


    /**
     * Adjust url for matching. Adds language code to url if needed.
     *
     * @param $url
     * @return string
     */
    private function adjustUrlForMatching($url) {
        if ($this->languageDisplaySetting === config('admin.language_url.directory') && !$this->showDefaultLanguageInUrl && $this->language->default) {
            return $this->language->language_code . '/' . $url;
        }

        return $url;
    }


    /**
     * Get public format of url. Removes language code to url if needed.
     *
     * @param $url
     * @return string
     */
    public function getPublicUrl($url) {
        if ($this->languageDisplaySetting === config('admin.language_url.directory') && !$this->showDefaultLanguageInUrl && $this->language->default) {
            return substr($url, 1 + strpos($url, '/'));
        }

        if ($this->languageDisplaySetting === config('admin.language_url.directory')) {
            if(!$this->showDefaultLanguageInUrl && $this->language->default) {
                return substr($url, 1 + strpos($url, '/'));
            } else {
                $firstSlashPos = strpos($url, '/');

                if (!$firstSlashPos || substr($url, 0, $firstSlashPos) !== $this->language->language_code) {
                    return $this->language->language_code . '/' . $url;
                }
            }
        }

        return $url;
    }


    /**
     * Get full public url.
     *
     * @param $url
     * @return string
     */
    public function getFullPublicUrl($url) {
        return route('homepage', $this->getPublicUrl($url));
    }


    /**
     * Is homepage url?
     *
     * @param string $url
     * @return bool
     */
    public function isUrlHomepage($url) {
        if ($this->languageDisplaySetting === config('admin.language_url.directory')) {
            if ($url === $this->language->language_code) {
                return true;
            } else if ($this->showDefaultLanguageInUrl) {
                return false;
            }
        }

        return $url === '' || $url === '/';
    }
}