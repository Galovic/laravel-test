<?php

namespace Modules\CareerPlugin\Http\Controllers\Admin;

use App\Helpers\Functions;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CareerPlugin\Http\Requests\Admin\CareerRequest;
use Modules\CareerPlugin\Models\Career;
use Image;

class CareerController extends AdminController
{

    /**
     * CareerController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:module_careerplugin-show')->only('index');
        $this->middleware('permission:module_careerplugin-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:module_careerplugin-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:module_careerplugin-delete')->only('delete');
    }


    /**
     * List of career opportunities
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Kariéra", "přehled pracovních pozic");

        $careers = Career::whereLanguage($this->getLanguage())->get();

        return view('module-careerplugin::admin.index', compact('careers'));
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->setTitleDescription("Kariéra", "vytvořit pracovní pozici");
        $career = new Career();
        return view('module-careerplugin::admin.create', compact('career'));
    }


    /**
     * Store a newly created resource in storage.
     * @param  CareerRequest $request
     * @return Response
     */
    public function store(CareerRequest $request)
    {
        $career = new Career($request->getValues());

        $career->language_id = $this->getLanguage()->id;
        $career->user_id = auth()->id();
        $career->save();

        // Uploaded files
        $uploadPath = Career::getTempPath();
        $imageDir = $career->images_path;

        if(\File::exists($uploadPath)){
            Functions::recurseDirectoryCopy("$uploadPath/", "$imageDir/");
            \File::deleteDirectory($uploadPath);
        }

        $career->fixUrlsInTexts();

        if($request->hasFile('image')) {

            // Save image and create thumb
            $imageName = 'image.' . $request->image->getClientOriginalExtension();

            if (!\File::exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            $request->file('image')->move($imageDir, $imageName);

            $career->image = $imageName;
            $career->createThumbnail();
        }

        $career->save();

        flash('Pracovní pozice byla úspěšně vytvořena!', 'success');
        return redirect()->route('admin.module.career.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Career $career
     * @return Response
     */
    public function edit(Career $career)
    {
        // redirect when career language does not match.
        $this->redirectWhenLanguageNotMatch($career, 'admin.module.career.index');

        $this->setTitleDescription("Kariéra", "upravit pracovní pozici");
        return view('module-careerplugin::admin.edit', compact('career'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  CareerRequest $request
     * @param  Career $career
     * @return Response
     */
    public function update(CareerRequest $request, Career $career)
    {
        // Save values
        $input = $request->getValues();

        if($request->hasFile('image')) {

            // Save image and create thumb
            $imageName = 'image.' . $request->image->getClientOriginalExtension();

            $imageDir = $career->images_path;

            if (!file_exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            $request->file('image')->move($imageDir, $imageName);

            $career->image = $imageName;
            $career->createThumbnail();
            $career->save();
        }
        elseif ($request->input('remove_image') == 'true' && $career->image) {
            if(file_exists($career->image_path)){
                \File::delete($career->image_path);
            }
            if(file_exists($career->thumbnail_path)){
                \File::delete($career->thumbnail_path);
            }
            $career->image = null;
        }

        $career->update($input);

        flash('Pracovní pozice byla úspěšně upravena!', 'success');
        return redirect()->route('admin.module.career.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  Career $career
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Career $career)
    {
        $career->delete();

        flash('Pracovní pozice byla úspěšně smazána!', 'success');
        return $this->refresh();
    }
}
