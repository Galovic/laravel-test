<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Image;
use File;

class ServicesController extends AdminController
{
    /**
     * Create a new Services controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:services-show')->only('index');
        $this->middleware('permission:services-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:services-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:services-delete')->only('delete');
    }


    /**
     * List of Services
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Služby", "přehled služeb");

        $services = Service::whereLanguage($this->getLanguage())->get();

        return view('admin.services.index', compact('services'));
    }


    /**
     * Create new Services
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription("Služby", "vytvořit službu");

        return view('admin.services.create');
    }


    /**
     * Request: Render form for editing service
     *
     * @param Service $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Service $service)
    {
        $this->setTitleDescription("Služby", "upravit službu");

        return view('admin.services.edit', compact('service'));
    }


    /**
     * Request: store new service
     *
     * @param ServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ServiceRequest $request)
    {
        $service = new Service($request->getValues());

        $service->language_id = $this->getLanguage()->id;
        $service->user_id = auth()->id();
        $service->save();

        // Save image and create thumb
        $imageName = 'image.' . $request->image->getClientOriginalExtension();
        $thumbName = 'thumb.' . $request->image->getClientOriginalExtension();

        $imageDir = $service->photogallery_path;

        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        $request->file('image')->move($imageDir, $imageName);

        $crop = config('admin.image_crop.service.small');
        Image::make("{$imageDir}/{$imageName}")
            ->resize($crop['size'][0], $crop['size'][1])
            ->save("{$imageDir}/{$thumbName}");

        $service->image = $imageName;
        $service->thumbnail = $thumbName;

        flash('Služba byla úspěšně vytvořena!', 'success');
        return redirect()->route('admin.services.index');
    }


    /**
     * Request: Update existing service
     *
     * @param ServiceRequest $request
     * @param Service $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ServiceRequest $request, Service $service)
    {
        // Save image and create thumb
        if($request->image !== null){

            $imageName = 'image.' . $request->image->getClientOriginalExtension();
            $thumbName = 'thumb.' . $request->image->getClientOriginalExtension();

            $imageDir = $service->photogallery_path;

            if (!file_exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            $request->file('image')->move($imageDir, $imageName);

            $crop = config('admin.image_crop.service.small');
            Image::make("{$imageDir}/{$imageName}")
                ->resize($crop['size'][0], $crop['size'][1])
                ->save("{$imageDir}/{$thumbName}");

            $service->image = $imageName;
            $service->thumbnail = $thumbName;
        }

        // Save values
        $service->fill($request->getValues());
        $service->save();

        flash('Služba byla úspěšně upravena!', 'success');
        return redirect()->route('admin.services.index');
    }


    /**
     * Request: Delete service
     *
     * @param Service $service
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Service $service)
    {
        $service->delete();

        flash('Služba byla úspěšně smazána!', 'success');
        return $this->refresh();
    }


}