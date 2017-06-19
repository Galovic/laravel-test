<?php

namespace Modules\FormsPlugin\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Response;
use Modules\FormsPlugin\Http\Requests\Admin\FormsPluginRequest;
use Modules\FormsPlugin\Models\Field;
use Modules\FormsPlugin\Models\Form;
use Modules\FormsPlugin\Models\Localisation;

class FormsController extends AdminController
{
    /**
     * FormsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:module_formsplugin-show')->only(['index', 'responses', 'downloadFile']);
        $this->middleware('permission:module_formsplugin-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:module_formsplugin-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:module_formsplugin-delete')->only('delete');
    }


    /**
     * List of forms
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Formuláře", "přehled formulářů");

        $forms = Form::all();

        return view('module-formsplugin::admin.index', compact('forms'));
    }


    /**
     * List of responses
     *
     * @param Form $form
     * @return \Illuminate\View\View
     */
    public function responses(Form $form)
    {
        $this->setTitleDescription("Formulář", "odeslané odpovědi");

        $formFields = $form->languageFields($this->getLanguage())->get();
        $responses = $form->responses()->orderBy('created_at', 'DESC')->paginate(5);

        return view('module-formsplugin::admin.responses', compact('form', 'formFields', 'responses'));
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->setTitleDescription("Formuláře", "vytvořit formulář");
        return view('module-formsplugin::admin.create');
    }


    /**
     * Store a newly created resource in storage.
     * @param  FormsPluginRequest $request
     * @return Response
     */
    public function store(FormsPluginRequest $request)
    {
        $form = new Form($request->getValues());
        $form->user_id = auth()->id();
        $form->save();

        $localisation = new Localisation($request->getLocalised());
        $localisation->language_id = $this->getLanguage()->id;
        $form->localisation()->save($localisation);

        $form->saveFields($request->getFields(), $this->getLanguage());

        flash('Formulář byl úspěšně vytvořen!', 'success');
        return response()->json([ 'redirect' => route('admin.module.forms_plugin') ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Form $form
     * @return Response
     */
    public function edit(Form $form)
    {
        $this->setTitleDescription("Formuláře", "upravit formulář");

        $form->setLanguage($this->getLanguage());
        $formFields = $form->languageFields($this->getLanguage())->get();
        return view('module-formsplugin::admin.edit', compact('form', 'formFields'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  FormsPluginRequest $request
     * @param  Form $form
     * @return Response
     */
    public function update(FormsPluginRequest $request, Form $form)
    {
        // Save values
        $form->update($request->getValues());

        /** @var Localisation $localisation */
        $localisation = $form->localisation()->whereLanguage($this->getLanguage())->first();
        if (!$localisation) {
            $localisation = new Localisation($request->getLocalised());
            $localisation->language_id = $this->getLanguage()->id;
            $form->localisation()->save($localisation);
        } else {
            $localisation->update($request->getLocalised());
        }

        $form->saveFields($request->getFields(), $this->getLanguage());

        flash('Formulář byl úspěšně upraven!', 'success');
        return response()->json([ 'redirect' => route('admin.module.forms_plugin') ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  Form $form
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Form $form)
    {
        $form->delete();

        flash('Formulář byl úspěšně smazán!', 'success');
        return $this->refresh();
    }


    public function downloadFile(\Modules\FormsPlugin\Models\Response $response, $fieldId) {
        if (!isset($response->values[$fieldId])) {
            return abort(404);
        }

        $file = $response->values[$fieldId];
        /** @var Field $field */
        $field = $response->form->fields()->find($fieldId);
        $field->setFileName($file['file']);

        if (\File::exists($field->file_path)) {
            return response()->download($field->file_path, $file['name']);
        }

        return abort(404);
    }
}
