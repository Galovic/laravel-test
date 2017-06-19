<?php

namespace Modules\View\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Module\Entity;
use App\Models\Web\Theme;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\View\Http\Requests\ModuleRequest;
use Modules\View\Models\Configuration;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $configuration = Configuration::getDefault();

        $views = ViewHelper::getDemarcatedViews('view_module');
        return view('module-view::configuration.form', compact('configuration', 'views'));
    }


    /**
     * Validate module and return preview.
     *
     * @param ModuleRequest $request
     * @return mixed
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration($request->only('view'));

        $viewName = ViewHelper::getViewName('view_module', $configuration->view);
        $viewVariables = ViewHelper::getViewVariables($configuration->view);
        $variables = $request->input('variable');

        return response()->json([
            'content' => view(
                'module-view::module_preview',
                compact('viewName', 'variables', 'viewVariables')
            )->render()
        ]);
    }


    /**
     * Show the form for editing specified resource.
     * @return Response
     */
    public function edit(Configuration $configuration)
    {
        $views = ViewHelper::getDemarcatedViews('view_module');
        $variables = $configuration->variables;

        return view('module-view::configuration.form', compact('configuration', 'views', 'variables'));
    }


    /**
     * Get variables of specified view.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVariables() {
        $view = request('view');
        if (!$view || !ViewHelper::isViewDemarcated('view_module', $view)) {
            abort(404);
        }

        $variables = ViewHelper::getViewVariables($view);

        return \response()->json([
            'variables' => $variables
        ]);
    }
}
