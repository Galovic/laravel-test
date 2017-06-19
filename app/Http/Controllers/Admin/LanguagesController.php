<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LanguageRequest;
use App\Models\Web\Language;
use App\Models\Web\Settings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguagesController extends AdminController
{

    /**
     * LanguagesController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:languages-show')->only('index');
        $this->middleware('permission:languages-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:languages-edit')->only([ 'edit', 'update', 'toggleEnabled', 'toggleDefault' ]);
        $this->middleware('permission:languages-delete')->only('delete');
    }


    /**
     * List languages
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Jazyky", "přehled jazyků");

        $languages = Language::all();
        $languageDisplay = intval(Settings::get('language_display', config('admin.language_url.directory')));
        $defaultLanguageHidden = !!Settings::get('default_language_hidden', false);

        return view('admin.languages.index', compact('languages', 'defaultLanguageHidden', 'languageDisplay'));
    }


    /**
     * Show form to create new language
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription("Jazyky", "Vytvořit jazyk");
        $language = new Language([
            'enabled' => true
        ]);

        return view('admin.languages.create', compact('language'));
    }


    /**
     * Store new language
     *
     * @param LanguageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LanguageRequest $request)
    {
        Language::create($request->getValues());

        flash('Jazyk byl uspěšně přidán!', 'success');
        return redirect()->route('admin.languages.index');
    }


    /**
     * Show form to edit specified language
     *
     * @param Language $language
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Language $language)
    {
        $this->setTitleDescription("Jazyky", "Upravit jazyk");

        return view('admin.languages.edit', compact('language'));
    }


    /**
     * Update specified language
     *
     * @param LanguageRequest $request
     * @param Language $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LanguageRequest $request, Language $language)
    {
        $language->update($request->getValues());

        flash('Jazyk byl uspěšně upraven!', 'success');
        return redirect()->route('admin.languages.index');
    }


    /**
     * Toggle default specified language
     *
     * @param Language $language
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleDefault(Language $language)
    {
        if(!$language->default) {
            Language::findDefault()->update([
                'default' => 0
            ]);

            $language->update([
                'default' => 1
            ]);
        }

        flash("Jazyk {$language->name} nastaven jako výchozí.", 'success');
        return $this->refresh();
    }


    /**
     * Toggle enabled specified language
     *
     * @param Language $language
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleEnabled(Language $language)
    {
        $language->update([
            'enabled' => !$language->enabled
        ]);

        flash("Jazyk {$language->name} úspěšně " . ( $language->enabled ? 'povolen' : 'zakázán' ) . '.', 'success');
        return $this->refresh();
    }


    /**
     * Delete specified language
     *
     * @param Language $language
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Language $language)
    {
        if ($language->default == 1){
            $newDefault = Language::where('enabled', 1)->where('default', 0)->first();
            if(!$newDefault){
                flash('Nelze smazat výchozí jazyk.', 'warning');
                return $this->refresh();
            }

            $newDefault->default = 1;
            $newDefault->save();
        }

        $language->delete();

        flash('Jazyk byl smazán.', 'success');
        return $this->refresh();
    }

    /**
     * Update language settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request) {
        $this->validate($request, [
            'language_display' => [ 'required', Rule::in(config('admin.language_url')) ],
            'default_language_hidden' => 'boolean'
        ]);

        $display = intval($request->input('language_display'));

        Settings::set('language_display', $display);

        if ($display === config('admin.language_url.directory')) {
            Settings::set('default_language_hidden', intval($request->input('default_language_hidden')) === 1);
        }

        flash('Nastavení jazyka bylo uloženo.', 'success');
        return $this->refresh();
    }
}
