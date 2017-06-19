<?php

namespace Modules\CareerPlugin\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Module\Entity;
use Modules\CareerPlugin\Http\Requests\ModuleRequest;
use Modules\CareerPlugin\Models\Configuration;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return $this->createForm(Configuration::getDefault());
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
            'content' => view('module-careerplugin::module_preview', compact('configuration') )->render()
        ]);
    }


    /**
     * Return form view
     *
     * @param $configuration
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function createForm($configuration){
        $views = [ '' => 'Výchozí' ] + ViewHelper::getAllViews();

        return view('module-careerplugin::configuration.form', compact('configuration', 'models', 'modelLists', 'views'));
    }
}
