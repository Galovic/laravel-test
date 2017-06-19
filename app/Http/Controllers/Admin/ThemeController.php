<?php namespace App\Http\Controllers\Admin;

use App\Models\ContextBase;
use App\Models\Web\Settings;
use App\Models\Web\Theme;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeController extends AdminController
{

    /**
     * @var ContextBase
     */
    private $themeContext;


    /**
     * ThemesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        view()->composer('theme::config.form', function (View $view) {
            if(method_exists($this->themeContext, 'viewConfig')){
                $this->themeContext->viewConfig($view);
            }
        });
    }

    /**
     * Request: theme
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription('Šablona', 'nastavení šablony');

        $themes = Theme::all();

        $defaultTheme = Theme::getDefault();
        $this->themeContext = $defaultTheme->getContextInstance();
        $this->themeContext->setLanguage($this->getLanguage());

        return view('admin.theme.index', compact('themes', 'defaultTheme'));
    }


    /**
     * Switch template
     *
     * @param $name
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function switchTheme($name){
        $theme = Theme::find($name);

        if(!$theme){
            flash('Tato šablona není k dispozici!', 'error');
            return $this->refresh();
        }

        Settings::set('theme', $theme->id);
        flash('Šablona úspěšně změněna!', 'success');
        return $this->refresh();
    }


    /**
     * Change setting
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function changeSetting(Request $request){

        $validator = \Validator::make($request->all(), [
            'key' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => true
            ]);
        }

        $defaultTheme = Theme::getDefault();
        $defaultTheme->set($request->input('key'), $request->input('value'));

        return response()->json([
            'success' => true
        ]);
    }

}