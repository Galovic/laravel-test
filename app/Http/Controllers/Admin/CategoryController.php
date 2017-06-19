<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Article\Category;

class CategoryController extends AdminController
{

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:article-categories-show')->only('index');
        $this->middleware('permission:article-categories-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:article-categories-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:article-categories-delete')->only('delete');
    }

    /**
     * List of categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Kategorie", "přehled kategorií");

        // All categroies
        $categories = Category::articlesOnly()
            ->whereLanguage($this->getLanguage())
            ->orderBy('id', 'desc')
            ->get()->toHierarchy();

        return view('admin.categories.index', compact('category', 'categories'));
    }


    /**
     * Create new category
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription("Kategorie", "vytvořit kategorii");

        // All categories to choose list
        $categories = Category::whereLanguage($this->getLanguage())
            ->get()->toHierarchy();

        return view('admin.categories.create', compact('categories'));
    }


    /**
     * Edit existing category
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        // redirect when category language does not match.
        $this->redirectWhenLanguageNotMatch($category, 'admin.categories.index');

        $this->setTitleDescription("Kategorie", "upravit kategorii");

        // All categories to choose list
        $categories = Category::whereLanguage($category->language_id)
            ->where('id', '<>', $category->id)
            ->get()->toHierarchy();

        return view('admin.categories.edit',compact('category', 'categories'));
    }


    /**
     * Store new category
     *
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request)
    {
        $category = new Category($request->all());
        $category->flag = 'articles';
        $category->language_id = $this->getLanguage()->id;
        $category->user_id = auth()->id();
        $category->save();

        if ($parentId = $request->input('parent_id')) {
            $category->makeChildOf(Category::findOrFail($parentId));
        }else{
            $category->makeRoot();
        }

        flash('Kategorie byla úspěšně vytvořena!', 'success');

        return redirect()->route('admin.categories.index');
    }


    /**
     * Update category
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->all());

        if ($parentId = $request->input('parent_id')) {
            $category->makeChildOf(Category::findOrFail($parentId));
        }else{
            $category->makeRoot();
        }

        flash('Kategorie byla úspěšně upravena!', 'success');

        return redirect()->route('admin.categories.index');
    }


    /**
     * Delete category
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Category $category)
    {
        $category->delete();

        flash('Kategorie byla úspěšně smazána!', 'success');

        return $this->refresh();
    }


}