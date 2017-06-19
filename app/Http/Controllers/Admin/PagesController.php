<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ViewHelper;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Module\Entity;
use App\Models\Module\InstalledModule;
use App\Models\Page\Content;
use App\Models\Page\LayoutType;
use App\Models\Page\Page;
use App\Models\Page\Type;
use App\Models\Page\Version;

class PagesController extends AdminController
{

    /**
     * PagesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:pages-show')->only('index');
        $this->middleware('permission:pages-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:pages-edit')->only([ 'edit', 'update', 'switchVersion' ]);
        $this->middleware('permission:pages-delete')->only('delete');
    }


    /**
     * Request: Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->setTitleDescription("Stránky", "seznam stránek");

        $pages = Page::whereLanguage($this->getLanguage())
            ->orderBy('id', 'asc')->get()->toHierarchy();

        return view('admin.pages.index', compact('pages'));
    }


    /**
     * Request: Show the form for creating a new page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription("Stránky", "vytvořit stránku");

        $pages = Page::whereLanguage($this->getLanguage())
            ->orderBy('id', 'asc')->get()->toHierarchy();

        $pageTypes = Type::all();
        $views = ViewHelper::getDemarcatedViews('page');

        $modules = InstalledModule::enabled()->get()->pluck('module');

        return view('admin.pages.create', compact('pages', 'pageTypes', 'modules', 'views'));
    }


    /**
     * Request: Store new page
     *
     * @param PageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PageRequest $request)
    {
        // Create page
        $page = new Page($request->getValues());
        $page->language_id = $this->getLanguage()->id;
        $page->save();

        $this->saveContentAndModules($page, $request);

        if ($page->parent_id) {
            $page->makeChildOf(Page::find($page->parent_id));
        }

        if($request->hasFile('image')) {

            // Save image and create thumb
            $imageName = 'image.' . $request->image->getClientOriginalExtension();

            $imageDir = $page->images_path;

            if (!file_exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            $request->file('image')->move($imageDir, $imageName);

            $page->image = $imageName;
            $page->createThumbnail();
            $page->save();
        }

        flash('Stránka byla úspěšně vytvořena!', 'success');

        return redirect()->route('admin.pages.index');
    }


    /**
     * Request: Show the form for editing the specified page.
     *
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Page $page)
    {
        // redirect when page language does not match.
        $this->redirectWhenLanguageNotMatch($page, 'admin.pages.index');

        $this->setTitleDescription('Stránky', 'upravit stránku');

        $pageTypes = Type::all();

        $pages = Page::whereLanguage($page->language_id)
            ->where('id', '<>', $page->id)->get()->toHierarchy();

        $layoutTypes = LayoutType::select('id', 'name')->get();

        foreach ($layoutTypes as $type) {
            $type->value = Hashids::encode($type->id);
            $type->text = $type->name;
            unset($type->id, $type->name);
        }

        $views = ViewHelper::getDemarcatedViews('page');
        $modules = InstalledModule::enabled()->get()->pluck('module');
        $pageContentId = $page->getActiveContent()->id;

        $allowed_module = [];

        return view('admin.pages.edit', compact('page', 'pageTypes', 'pages', 'layoutTypes','allowed_module', 'modules', 'views', 'pageContentId'));
    }


    /**
     * Request: Update the specified resource in storage.
     *
     * @param  PageRequest $request
     * @param  Page $page
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $request, Page $page)
    {
        // Set active content before content is examined for changes.
        Content::findOrFail($request->input('page_content_id'))->setActive();

        // Fill model with input.
        $page->fill($request->getValues());

        // Examine content for changes and save it with modules.
        $newVersionCreated = $this->saveContentAndModules($page, $request);

        if ($request->parent_id) {
            $page->makeChildOf(Page::findOrFail($request->parent_id));
        } else {
            $page->makeRoot();
        }

        if($request->hasFile('image')) {

            // Save image and create thumb
            $imageName = 'image.' . $request->image->getClientOriginalExtension();

            $imageDir = $page->images_path;

            if (!file_exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            $request->file('image')->move($imageDir, $imageName);

            $page->image = $imageName;
            $page->createThumbnail();
            $page->save();
        }
        elseif ($request->input('remove_image') === 'true' && $page->image) {
            if(file_exists($page->image_path)){
                \File::delete($page->image_path);
            }
            if(file_exists($page->thumbnail_path)){
                \File::delete($page->thumbnail_path);
            }
            $page->image = null;
            $page->save();
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Stránka uložena.',
                'new_content' => $newVersionCreated ? $page->content : null,
                'versions' => $newVersionCreated ? $page->getVersionsJSON(false) : null
            ]);
        }

        flash('Stránka byla úspěšně upravena!', 'success');
        return redirect()->route('admin.pages.index');
    }


    /**
     * Request: Duplicate specified page
     * @param Page $page
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function duplicate(Page $page)
    {
        $page->replicateFull();
        flash('Stránka byla úspěšně zduplikována!', 'success');
        return $this->refresh();
    }


    /**
     * Create new version of content if needed.
     * Save all entities, configurations and content.
     * @param Page $page
     * @param PageRequest $request
     * @return bool
     */
    private function saveContentAndModules(Page $page, PageRequest $request) {
        $entities = json_decode($request->input('modules', '[]'));
        $inputContent = $request->input('content');
        $activeContent = $page->wasRecentlyCreated ?
            null :
            $page->getActiveContent();

        $createdEntities = [];
        $updatedEntities = [];
        $originalEntities = [];

        // #1 MAP CHANGES
        foreach ($entities as $id => $configuration) {

            // CREATING NEW MODULES
            // New page always has configuration and _helper property with name of module.
            if ($configuration && isset($configuration->_helper)) {

                $moduleName = $configuration->_helper->moduleName;

                // If module with specified name does not exist, continue.
                if (!\Module::find($moduleName)) {
                    continue;
                }

                $createdEntities[$id] = $configuration;

            } else {
                /** @var Entity $entity */
                $entity = Entity::find($id);

                // When entity does not exist, continue.
                if (!$entity) {
                    continue;
                }

                // page_content_id is new attribute, so it can be null. If so, set correct value.
                if (is_null($entity->page_content_id) && $activeContent) {
                    $entity->page_content_id = $activeContent->id;
                    $entity->save();
                }

                // Is creating new page (so entity is stolen) OR entity is stolen (from different page).
                if (!$activeContent || $entity->page_content_id !== $activeContent->id) {
                    if ($configuration) {
                        $updatedEntities[$id] = $entity;
                    } else {
                        $originalEntities[$id] = $entity;
                    }
                }
                // Is updating page and entity belongs to this page content.
                else {
                    if ($configuration) {
                        $entityConfiguration = $entity->getConfiguration();
                        // Check if entity configuration was changed.
                        if ($entityConfiguration->inputFill((array)$configuration)->isDirty()) {
                            $updatedEntities[$id] = $entity;
                        }
                    } else {
                        $originalEntities[$id] = $entity;
                    }
                }
            }
        }

        // Check if new version needs to be created.
        $createNewVersion = $createdEntities || $updatedEntities || !$activeContent;
        if (!$createNewVersion) {
            // Or if content changed.
            $createNewVersion = $activeContent->fill([
                'content' => $inputContent
            ])->isDirty();
        }

        if (!$createNewVersion) {
            return false;
        }

        // Update variables - new version and content.
        $newContent = $page->createNewContent($inputContent);

        // Save entities and configurations.
        foreach ($entities as $id => $configuration) {
            $originalEntity = $newEntity = null;

            // Create new entities.
            if (isset($createdEntities[$id])) {
                /** @var Entity $newEntity */
                $entity = new Entity(['module' => $configuration->_helper->moduleName]);
                $entity->createConfiguration((array)$configuration);
                $newContent->entities()->save($entity);

                $newContent->replaceTempId($id, $entity->id);
            }
            // Recreate updated entities.
            else if (isset($updatedEntities[$id])) {
                /** @var Entity $originalEntity */
                $originalEntity = $updatedEntities[$id];
                $newEntity = $originalEntity->duplicateForNextVersion($newContent->id, (array)$configuration);
            }
            // Duplicate not-changed entities.
            else if (isset($originalEntities[$id])) {
                /** @var Entity $originalEntity */
                $originalEntity = $originalEntities[$id];
                $newEntity = $originalEntity->duplicateForNextVersion($newContent->id);
            }

            if ($originalEntity && $newEntity) {
                $newContent->replaceEntityId($originalEntity->id, $newEntity->id);
            }
        }

        $newContent->save();
        return true;
    }


    /**
     * Request: delete page
     *
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Page $page)
    {
        $page->delete();
        flash('Stránka byla úspěšně smazána!', 'success');
        return $this->refresh();
    }


    /**
     * Switch active content.
     *
     * @param Page $page
     * @param null $versionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchVersion (Page $page, $versionId = null) {
        if (!$versionId) {
            abort(404);
        }

        $content = $page->contents()->where('id', $versionId)->first();

        if (!$content) {
            abort(404);
        }

        return response()->json([
            'content' => $content->content
        ]);
    }
}
