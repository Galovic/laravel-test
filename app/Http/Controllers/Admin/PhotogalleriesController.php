<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Functions;
use App\Http\Requests\Admin\PhotogalleryRequest;
use App\Models\Photogallery\Photo;
use App\Models\Photogallery\Photogallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Image;

class PhotogalleriesController extends AdminController
{

    /**
     * PhotogalleriesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:photogalleries-show')
            ->only([ 'index' ]);

        $this->middleware('permission:photogalleries-create')
            ->only([ 'create', 'store' ]);

        $this->middleware('permission:photogalleries-edit')
            ->only([ 'edit', 'update' ]);

        $this->middleware('permission:photogalleries-edit|photogalleries-create')
            ->only([ 'updatePhoto', 'photoList', 'deletePhoto' ]);

        $this->middleware('permission:photogalleries-delete')
            ->only('delete');
    }


    /**
     * Request: List of photogalleries
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Fotogalerie", "přehled fotogalerií");
        $photogalleries = Photogallery::whereLanguage($this->getLanguage())
            ->orderPublish()->get();

        return view('admin.photogalleries.index', compact('photogalleries'));
    }


    /**
     * Request: Create new photogallery
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription("Fotogalerie", "vytvořit fotogalerii");
        $photogallery = new Photogallery();
        return view('admin.photogalleries.create', compact('photogallery'));
    }


    /**
     * Request: Edit photogallery
     *
     * @param Photogallery $photogallery
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Photogallery $photogallery)
    {
        // redirect when photogallery language does not match.
        $this->redirectWhenLanguageNotMatch($photogallery, 'admin.photogalleries');

        $this->setTitleDescription("Fotogalerie", "upravit fotogalerii");

        return view('admin.photogalleries.edit', compact('photogallery'));
    }


    /**
     * Request: Store new photogallery
     *
     * @param PhotogalleryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PhotogalleryRequest $request)
    {
        // Create photogallery
        $photogallery = new Photogallery($request->getValues());

        $photosScope = $photogallery->getPhotosScope();

        $photogallery->language_id = $this->getLanguage()->id;
        $photogallery->user_id = auth()->id();
        $photogallery->save();

        $photogallery->fixUrlsInText();

        $photogallery->save();

        $imageDir = $photogallery->photogallery_path;
        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        // Uploaded gallery images
        $uploadPath = Photogallery::getTempPath() . '/done';
        if(file_exists($uploadPath)){
            Functions::recurseDirectoryCopy("$uploadPath/", "$imageDir/");
        }

        $photosScope->update([
            'temporary_id' => null,
            'photogallery_id' => $photogallery->id
        ]);

        flash('Fotogalerie byla úspěšně vytvořena!', 'success');
        return redirect()->route('admin.photogalleries');
    }


    /**
     * Request: Update photogallery
     *
     * @param PhotogalleryRequest $request
     * @param Photogallery $photogallery
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PhotogalleryRequest $request, Photogallery $photogallery)
    {
        // Save values
        $photogallery->update($request->getValues());

        flash('Fotogalerie byla úspěšně upravena!', 'success');
        return redirect()->route('admin.photogalleries');
    }


    /**
     * Request: Update photogallery photo attributes author or title
     *
     * @param Photogallery $photogallery
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updatePhoto(Photogallery $photogallery, Request $request){
        $photo = $photogallery->getPhotosScope()
            ->whereId($request->input('pk'))->first();

        $field = $request->input('name');
        if(!$photo || !in_array($field, ['title', 'author'])){
            return new Response('Neplatné pole.', 400);
        }

        $photo->update([
            $field => $request->input('value')
        ]);

        return new JsonResponse([ 'result' => 'ok' ]);
    }


    /**
     * Request: delete photogallery
     *
     * @param Photogallery $photogallery
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Photogallery $photogallery)
    {
        $photogallery->delete();

        flash('Fotogalerie byla úspěšně smazána!', 'success');
        return $this->refresh();
    }


    /**
     * Upload photo to photogallery
     *
     * @param Request $request
     * @param Photogallery|null $photogallery
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(Request $request, Photogallery $photogallery = null)
    {
        if (!$request->hasFile('file')){
            return response()->json([
                "OK" => 0,
                "info" => "Failed to upload file."
            ]);
        }

        $chunk = intval($request->input('chunk', 0));
        $chunks = intval($request->input('chunks', 0));

        $fileName = $request->input('name', $request->file->getClientOriginalName());
        $fileNameHash = md5($fileName);

        $uploadPath = Photogallery::getTempPath();

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filePath = $uploadPath . '/' . $fileNameHash;

        $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");

        if(!$out){
            return response()->json([
                "OK" => 0,
                "info" => "Failed to open output stream."
            ]);
        }

        $in = @fopen($request->file->path(), "rb");

        if(!$in){
            return response()->json([
                "OK" => 0,
                "info" => "Failed to open input stream."
            ]);
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($in);
        @fclose($out);

        // Done?
        if (!$chunks || $chunk == $chunks - 1) {

            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = $fileNameHash . '_' . time() . '.' . $extension;
            $photo = null;

            $newFileDir = $photogallery->photogallery_path;

            if (!file_exists($newFileDir)) {
                mkdir($newFileDir, 0755, true);
            }

            $photo = $photogallery->createPhoto($newFileName);
            $photo->type = $extension;

            rename("{$filePath}.part", $newFileDir . '/' . $newFileName);

            foreach(config('admin.image_crop.photogallery') as $cropName => $crop) {
                $cropPath = $newFileDir . '/' . $cropName;
                if (!file_exists($cropPath)) {
                    mkdir($cropPath, 0755, true);
                }

                if (strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) != 'gif') {
                    Image::make($newFileDir . '/' . $newFileName)
                        ->resize(
                            $crop['size'],
                            null,
                            function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            }
                        )->save($cropPath . '/' . $newFileName);
                }
                else
                {
                    copy($newFileDir . '/' . $newFileName, $cropPath . '/' . $newFileName);
                }
            }

            if($photo) {
                $image = Image::make($newFileDir . '/' . $newFileName);

                $photo->size = round((strtolower($image->extension) == 'gif' ? $image->filesize() : strlen((string)$image->encode())) / 1024, 2);

                $photo->save();
            }

            unlink($newFileDir . '/' . $newFileName);
        }

        return response()->json([
            "OK" => 1,
            "info" => "Upload successful."
        ]);
    }


    /**
     * Render photo list
     *
     * @param Photogallery $photogallery
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function photoList(Photogallery $photogallery)
    {
        $photos = $photogallery->getPhotosScope()->get();
        return view('admin.photogalleries._photogallery', compact('photogallery', 'photos'));
    }


    /**
     * Delete photogallery photo
     *
     * @param Photo $photo
     * @return mixed
     */
    public function deletePhoto(Photo $photo){
        $photo->delete();

        return response()->json([ 'result' => 'ok' ]);
    }
}