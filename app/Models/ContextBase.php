<?php

namespace App\Models;

use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Page\Page;
use App\Models\Web\Language;
use App\Models\Web\Theme;
use App\Models\Web\ViewData;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class ContextBase
{
    /**
     * @var Theme
     */
    public $theme;

    /**
     * @var mixed
     */
    protected $object;

    /**
     * @var ViewData
     */
    protected $viewData;

    /**
     * @var Language
     */
    protected $language;


    /**
     * ContextBase constructor.
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
        $this->viewData = new ViewData([
            'theme' => $theme
        ]);

        View::addNamespace('theme', base_path('resources/themes/' . $this->getDir() . '/view'));
        Lang::addNamespace('theme', base_path('resources/themes/' . $this->getDir() . '/lang'));
    }


    /**
     * Get template directory.
     * @return string
     */
    protected function getDir() {
        $reflector = new \ReflectionClass(get_class($this));
        return basename(dirname($reflector->getFileName()));
    }


    /**
     * Render homepage.
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderHomepage(Page $page){
        $this->object = $page;
        $gridContent = $page->getContent();
        return $this->view('homepage.index', compact('gridContent'));
    }


    /**
     * Render page.
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderPage(Page $page){

        if ($page->is_homepage) {
            return $this->renderHomepage($page);
        }

        $this->object = $page;
        $gridContent = $page->getContent();
        return view($page->view ?: 'theme::pages.default', compact('gridContent', 'page'));
    }


    /**
     * Render article.
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderArticle(Article $article){
        $this->object = $article;

        if (!View::exists('theme::pages.article')) {
            return abort(404);
        }

        return $this->view('pages.article', compact('article'));
    }


    /**
     * Render article category.
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderCategory(Category $category){
        $this->object = $category;
        return $this->view('homepage.index');
    }


    /**
     * Render service.
     * @param Service $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderService(Service $service){
        $this->object = $service;
        return $this->view('homepage.index');
    }


    /**
     * Render 404 error.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderError(){
        return view('errors.404');
    }


    /**
     * Any view
     *
     * @param \Illuminate\View\View $view
     */
    public function viewAny(\Illuminate\View\View $view){
        if($this->object) {
            $view->data = $this->object->getViewData($this->viewData);
        }
        $view->context = $this;
    }


    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function view($view = null, $data = [], $mergeData = [])
    {
        return view('theme::' . $view, $data, $mergeData);
    }


    /**
     * Set language
     *
     * @param Language $language
     */
    public function setLanguage(Language $language){
        $this->language = $language;
        $this->viewData->language = $this->language;
    }


    /**
     * Get language
     *
     * @return Language
     */
    public function getLanguage(){
        return $this->language;
    }


    /**
     * Elixir
     *
     * @param $path
     * @return string
     */
    public function elixir($path){
        return elixir($path, 'theme/' . $this->getDir() . '/build');
    }


    /**
     * Media
     *
     * @param $path
     * @return string
     */
    public function media($path){
        return url('theme/' . $this->getDir() . '/media/' . $path);
    }


    /**
     * Media
     *
     * @param $path
     * @return string
     */
    public function asset($path){
        return url('theme/' . $this->getDir() . '/' . $path);
    }


    /**
     * Translate the given message.
     *
     * @param  string  $id
     * @param  array   $parameters
     * @param  string  $domain
     * @param  string  $locale
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */
    public function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        return trans('theme::' . $id, $parameters, $domain, $locale ?: $this->language->language_code);
    }


    /**
     * Translates the given message based on a count.
     *
     * @param  string  $id
     * @param  int|array|\Countable  $number
     * @param  array   $parameters
     * @param  string  $domain
     * @param  string  $locale
     * @return string
     */
    public function trans_choice($id, $number, array $parameters = [], $domain = 'messages', $locale = null)
    {
        return trans_choice('theme::' . $id, $number, $parameters, $domain, $locale ?: $this->language->language_code);
    }


    /**
     * Get url from config
     *
     * @param $key
     * @param $class
     * @return string
     */
    public function getConfigUrl($key, $class = Page::class){
        $id = $this->theme->get($this->getLanguage()->language_code . '_' . $key);
        return \UrlFactory::getFullUrl($class, $id);
    }


    /**
     * Get module. If module is not installed, returns null.
     *
     * @param string $name
     * @return \Module|null
     */
    protected function getModule ($name) {
        $installedModule = \App\Models\Module\InstalledModule::findNamed($name);
        return $installedModule ? $installedModule->module : null;
    }


    /**
     * Creates basic menu.
     *
     * @param string $key - Key to load menu.
     * @param string $name - Name of menu / variable.
     */
    protected function createMenu($key, $name) {
        $menu = \App\Models\Menu\Menu::find($this->theme->get($key));

        \Menu::make($name, function ($mainMenu) use ($menu) {

            if (!$menu) return;

            foreach($menu->toJsonObject($this->getLanguage())->items as $item){
                $menuItem = $mainMenu->add($item->name, [
                    'url' => $item->full_url,
                ]);

                if(!$item->children) continue;

                $active = false;

                foreach($item->children as $subitem){
                    $menuSubitem = $menuItem->add($subitem->name, [
                        'url' => $subitem->full_url,
                    ]);
                    $active = $active || $menuSubitem->isActive;
                }

                if($active){
                    $menuItem->active();
                }
            }

        });
    }
}