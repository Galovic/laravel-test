<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as Controller;
use App\Models\Module\InstalledModule;
use App\Models\User;
use App\Models\Web\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Session;
use Menu;

class AdminController extends Controller
{
    /**
     * @var string Page title
     */
    private $title;

    /**
     * @var string Page description
     */
    private $description;

    /**
     * @var Language
     */
    private $language;


    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        $this->middleware('admin');

        $this->middleware(function (Request $request, $next) {
            $this->createMenu(auth()->check() && auth()->user()->hasRole([ 'administrator', 'programmer' ]));

            return $next($request);
        });

        // All
        view()->composer('*', function ($view) {
            $view->currentLanguage = $this->getLanguage();
        });

        // Layout
        view()->composer(['admin.layouts.master','admin.layouts.blank'], function ($view) {
            $view->pageTitle = $this->title;
            $view->pageDescription = $this->description;
        });

        // Navbar
        view()->composer('admin.vendor._main_navbar', function ($view) {
            $view->languages = Language::enabled()->get();
        });

    }

    private function createMenu($isAdmin = false){
        Menu::make('MyNavBar', function ($menu) use ($isAdmin) {
            /** @var User $user */
            $user = auth()->user();
            $canShowArticles = $user->can('articles-show');
            $canShowCategories = $user->can('article-categories-show');

            $menu->add('Dashboard', [
                'route' => 'admin',
                'icon' => 'fa fa-home'
            ]);

            if ($canShowArticles || $canShowCategories) {
                $menu->add('Články', [
                    'icon' => 'fa fa-newspaper-o'
                ]);

                if ($canShowArticles) {
                    $menu->item('clanky')->add('Články', [
                        'route' => ['admin.articles.index']
                    ]);
                }

                if ($canShowCategories) {
                    $menu->item('clanky')->add('Kategorie', [
                        'route' => ['admin.categories.index']
                    ]);
                }
            }

//            $menu->add('Služby', [
//                'route' => 'admin.services.index',
//                'icon' => 'fa fa-book'
//            ]);


            if ($user->can('pages-show')) {
                $menu->add('Stránky', [
                    'route' => 'admin.pages.index',
                    'icon' => 'fa fa-file-text'
                ]);
            }

            if ($user->can('photogalleries-show')) {
                $menu->add('Fotogalerie', [
                    'route' => 'admin.photogalleries',
                    'icon' => 'fa fa-picture-o'
                ]);
            }

            if ($user->can('menu-show')) {
                $menu->add('Menu', [
                    'route' => 'admin.menu',
                    'icon' => 'fa fa-trello'
                ]);
            }

            foreach(InstalledModule::enabled()->get() as $installedModule){
                $module = $installedModule->module;
                $nickname = $module->config('nickname');
                $moduleMenuFields = $module->config('admin.menu', []);
                $addNewLinksTo = $menu;

                if (count($moduleMenuFields) > 1) {
                    $addNewLinksTo = $menu->add($module->trans('admin.menu.group_text'), [
                        'icon' => 'fa fa-trello'
                    ]);
                }

                foreach ($moduleMenuFields as $index => $moduleMenuField) {
                    if(isset($moduleMenuField['route']) && $nickname && $user->can("module_{$nickname}-show")){
                        $addNewLinksTo->add($module->trans("admin.menu.{$index}"), [
                            'route' => $moduleMenuField['route'],
                            'icon' => $moduleMenuField['icon'] ?? null
                        ]);
                    }
                }
            }

            if($isAdmin) {
                $menu->add('Uživatelé', ['icon' => 'fa fa-user']);
                $menu->item('uzivatele')->add('Uživatelé', ['route' => 'admin.users']);
                $menu->item('uzivatele')->add('Role', ['route' => 'admin.roles']);

                $menu->add('Nastavení', ['icon' => 'fa fa-cogs']);
                $menu->item('nastaveni')->add('Jazyky', ['route' => 'admin.languages.index']);
                $menu->item('nastaveni')->add('Šablona', ['route' => 'admin.theme']);
                $menu->item('nastaveni')->add('Moduly', ['route' => 'admin.modules']);
            }
        });
    }


    /**
     * Return current language
     *
     * @return Language|null
     */
    public function getLanguage(){
        if(!$this->language) {

            $languageId = Session::get('language', null);

            if ($languageId) {
                $this->language = Language::enabled()->where('id', $languageId)->first();
            }

            if (!$languageId || !$this->language) {
                $this->language = Language::enabled()->orderBy('default', 'desc')->first();
            }
        }

        \UrlFactory::setLanguage($this->language);

        return $this->language;
    }


    /**
     * Refresh page
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function refresh(){
        if(request()->ajax() || request()->acceptsJson()){
            return response()->json([
                'refresh' => true
            ]);
        }

        return redirect()->back();
    }


    /**
     * Set page title and description
     *
     * @param string $title
     * @param string $description
     */
    public function setTitleDescription($title, $description = ""){
        $this->title = $title;
        $this->description = $description;
    }


    /**
     * Request to switch language
     *
     * @param Language $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage(Language $language){
        if(!$language->exists || !$language->enabled){
            flash('Tento jazyk není povolen!', 'warning');
            return redirect()->back();
        }

        request()->session()->set('language', $language->id);
        flash(trans('general.status.language-web'), 'info');
        return redirect()->back();
    }


    /**
     * Redirect when model's language does not match of active language.
     * @param Model $model
     * @param string $route - redirect to.
     */
    public function redirectWhenLanguageNotMatch ($model, $route) {
        if ($model->language_id && $model->language_id !== $this->getLanguage()->id) {
            header("Location: " . route($route));
        }
    }
}