<?php

namespace Modules\FormsPlugin\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Module\Entity;
use Illuminate\Http\Request;
use Modules\FormsPlugin\Http\Requests\ModuleRequest;
use Modules\FormsPlugin\Models\Configuration;
use Modules\FormsPlugin\Models\Form;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return $this->createForm();
    }


    /**
     * Show the form for editing specified resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Configuration $configuration)
    {
        return $this->createForm($configuration);
    }


    /**
     * Validate module and return preview.
     *
     * @param ModuleRequest $request
     * @return mixed
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration();
        $configuration->inputFill($request->all());

        return response()->json([
            'content' => view('module-formsplugin::module_preview', compact('configuration') )->render()
        ]);
    }


    /**
     * Return form view
     *
     * @param $configuration
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function createForm($configuration = null){
        $views = [ '' => 'Výchozí' ] + ViewHelper::getAllViews();
        $forms = Form::pluck('name', 'id');
        $configuration = $configuration ?: new Configuration();

        return view('module-formsplugin::configuration.form', compact('configuration', 'views', 'forms'));
    }
}
