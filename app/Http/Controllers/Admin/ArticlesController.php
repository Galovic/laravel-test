<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Functions;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Article\Photo;
use App\Models\Article\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Image;

class ArticlesController extends AdminController
{

    /**
     * ArticlesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:articles-show')
            ->only([ 'index', 'categoriesTree' ]);

        $this->middleware('permission:articles-create')
            ->only([ 'create', 'store' ]);

        $this->middleware('permission:articles-edit')
            ->only([ 'edit', 'update' ]);

        $this->middleware('permission:articles-edit|articles-create')
            ->only([ 'updatePhoto', 'photoList', 'deletePhoto' ]);

        $this->middleware('permission:articles-delete')
            ->only('delete');
    }


    /**
     * Request: List of articles
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Články", "přehled článků");
        $articles = Article::whereLanguage($this->getLanguage())
            ->articlesOnly()->orderPublish()
            ->passJoins()
            ->groupBy("id")->get();

        return view('admin.articles.index', compact('articles'));
    }


    /**
     * Request: Create new article
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription("Články", "vytvořit článek");
        $article = new Article();

        if (config('project.functionality.article_tags')) {
            $tags = Tag::pluck('name');
        }

        return view('admin.articles.create', compact('article', 'tags'));
    }


    /**
     * Request: Edit article
     *
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Article $article)
    {
        // redirect when article language does not match.
        $this->redirectWhenLanguageNotMatch($article, 'admin.articles.index');

        $this->setTitleDescription("Články", "upravit článek");

        $articleCategories = $article->categories()->pluck('id')->implode(',');

        if (config('project.functionality.article_tags')) {
            $tags = Tag::pluck('name');
        }

        return view('admin.articles.edit', compact('article', 'articleCategories', 'tags'));
    }


    /**
     * Create categories tree
     *
     * @param Article|NULL $article
     * @return mixed
     */
    public function categoriesTree(Article $article = NULL)
    {
        return Category::getTree(
            Category::articlesOnly()->whereLanguage($this->getLanguage()),
            $article
        );
    }


    /**
     * Request: Store new article
     *
     * @param ArticleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ArticleRequest $request)
    {
        // Create article
        $article = new Article($request->getValues());

        $photosScope = $article->getPhotosScope();

        $article->language_id = $this->getLanguage()->id;
        $article->user_id = auth()->id();
        $article->save();

        // Fix urls
        $article->fixUrlsInText();

        // Save image and create thumb
        $imageName = 'image.' . $request->image->getClientOriginalExtension();
        $thumbName = 'thumb.' . $request->image->getClientOriginalExtension();

        $imageDir = $article->photogallery_path;

        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        $request->file('image')->move($imageDir, $imageName);

        $crop = config('admin.image_crop.service.small');
        Image::make("{$imageDir}/{$imageName}")
            ->resize($crop['size'][0], $crop['size'][1])
            ->save("{$imageDir}/{$thumbName}");

        $article->image = $imageName;
        $article->thumbnail = $thumbName;
        $article->save();

        // Uploaded gallery images
        $uploadPath = $this->getArticlePhotosTempPath() . '/done';
        if(file_exists($uploadPath)){
            Functions::recurseDirectoryCopy("$uploadPath/", "$imageDir/");
            \File::deleteDirectory($this->getArticlePhotosTempPath());
        }

        $photosScope->update([
            'temporary_id' => null,
            'article_id' => $article->id
        ]);

        // Save categories
        $article->syncCategories($request->getCategories());

        // Save tags
        if (config('project.functionality.article_tags')) {
            $article->syncTags($request->getTags());
        }

        flash('Článek byl úspěšně vytvořen!', 'success');
        return redirect()->route('admin.articles.index');
    }


    /**
     * Request: Update article
     *
     * @param ArticleRequest $request
     * @param Article $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $article->syncTags($request->getTags());
        // Save image and create thumb
        if($request->hasFile('image')) {

            $imageExtension = $request->image->getClientOriginalExtension();

            $imageName = 'image.' . $imageExtension;
            $thumbName = 'thumb.' . $imageExtension;

            $imageDir = $article->photogallery_path;

            if (!file_exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            $request->file('image')->move($imageDir, $imageName);

            $crop = config('admin.image_crop.service.small');
            Image::make("{$imageDir}/{$imageName}")
                ->resize($crop['size'][0], $crop['size'][1])
                ->save("{$imageDir}/{$thumbName}");

            $article->image = $imageName;
            $article->thumbnail = $thumbName;
        }
        elseif ($request->input('remove_image') === 'true' && $article->image) {
            if(file_exists($article->image_path)){
                \File::delete($article->image_path);
            }
            if(file_exists($article->thumbnail_path)){
                \File::delete($article->thumbnail_path);
            }
            $article->image = null;
            $article->save();
        }

        // Save values
        $article->fill($request->getValues());
        $article->save();

        // Update categories
        $article->syncCategories($request->getCategories());

        // Update tags
        if (config('project.functionality.article_tags')) {
            $article->syncTags($request->getTags());
        }

        flash('Článek byl úspěšně upraven!', 'success');
        return redirect()->route('admin.articles.index');
    }


    /**
     * Request: Update article photo attributes author or title
     *
     * @param Article $article
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updatePhoto(Article $article, Request $request){
        $photo = $article->getPhotosScope()->whereId($request->input('pk'))->first();

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
     * Request: delete article
     *
     * @param Article $article
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Article $article)
    {
        $article->delete();

        flash('Článek byl úspěšně smazán!', 'success');
        return $this->refresh();
    }


    /**
     * GEt temporary path to article photos
     *
     * @return string
     */
    private function getArticlePhotosTempPath(){
        return public_path( config('admin.path_upload') )
        . '/articles/temp/' . request()->input('_token');
    }


    /**
     * Upload photo to photogallery
     *
     * @param Request $request
     * @param Article|null $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(Request $request, Article $article = null)
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

        $uploadPath = Article::getPhotosTempPath();

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

            $newFileDir = $article->photogallery_path;

            if (!file_exists($newFileDir)) {
                mkdir($newFileDir, 0755, true);
            }

            $photo = $article->createPhoto($newFileName);
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
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function photoList(Article $article)
    {
        $photos = $article->getPhotosScope()->get();
        return view('admin.articles._photogallery', compact('article', 'photos'));
    }


    /**
     * Delete article photo
     *
     * @param Photo $photo
     * @return mixed
     */
    public function deletePhoto(Photo $photo){
        $photo->delete();

        return response()->json([ 'result' => 'ok' ]);
    }
}