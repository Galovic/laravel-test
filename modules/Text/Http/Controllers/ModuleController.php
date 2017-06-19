<?php

namespace Modules\Text\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Module\Entity;
use Illuminate\Http\Response;
use Modules\Text\Http\Requests\ModuleRequest;
use Modules\Text\Models\Configuration;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $configuration = new Configuration();
        return view('module-text::configuration.form', compact('configuration'));
    }


    /**
     * Validate module and return preview.
     *
     * @param ModuleRequest $request
     * @return mixed
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration([
            'content' => trim($request->input('content'))
        ]);

        return response()->json([
            'content' => view('module-text::module_preview', compact('configuration') )->render()
        ]);
    }


    /**
     * Show the form for editing specified resource.
     * @return Response
     */
    public function edit(Configuration $configuration)
    {
        return view('module-text::configuration.form', compact('configuration'));
    }
}
