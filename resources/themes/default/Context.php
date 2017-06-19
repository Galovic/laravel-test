<?php

class Context extends \App\Models\ContextBase
{
    /**
     * Primary menu view
     *
     * @param \Illuminate\View\View $view
     */
    public function viewMenusPrimary(\Illuminate\View\View $view) {
        $this->createMenu('menu_primary', 'MainMenu');
    }


    /**
     * Load five published articles for active language
     *
     * @param \Illuminate\View\View $view
     */
    public function viewArticlesList(\Illuminate\View\View $view){
        $view->articles = \App\Models\Article\Article::whereLanguage($this->getLanguage())
            ->published()->orderPublish()->limit(5)->get();
    }


    /**
     * Custom article render method
     *
     * @param \App\Models\Article\Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderArticle(\App\Models\Article\Article $article){
        $this->object = $article;
        $languageCode = $this->getLanguage()->language_code;

        $articlesPageUrl = \App\Models\Web\Url::findModel(
            \App\Models\Page\Page::class,
            $this->theme->get($languageCode . '_articles_page_id')
        )->url;

        return $this->view('articles.detail', compact('article', 'articlesPageUrl'));
    }


    /**
     * Actualities page
     *
     * @param \Illuminate\View\View $view
     */
    public function viewPagesArticles(\Illuminate\View\View $view){
        if($this->object instanceof \App\Models\Article\Category){
            $articles = $this->object->articles();
            $view->title = $this->object->name;
        }else{
            $articles = with(new \App\Models\Article\Article);
            $view->title = $this->trans('articles.title');
        }

        $scopeArticles = $articles->whereLanguage($this->getLanguage())
            ->published()
            ->orderPublish()
            ->limit(30);

        if ($tag = request('tag')) {
            $articlesTable = \App\Models\Article\Article::getTableName();
            $tag = \App\Models\Article\Tag::where('name', $tag)->first();
            $articlesWithTag = [];

            if ($tag) {
                $articlesWithTag = $tag
                    ->articles()
                    ->select($articlesTable . '.id')
                    ->pluck('id');
            }

            $scopeArticles->whereIn($articlesTable . '.id', $articlesWithTag);
        }

        $view->articles = $scopeArticles->get();

    }


    /**
     * Render page
     *
     * @param \App\Models\Page\Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderPage(\App\Models\Page\Page $page){
        $this->object = $page;
        $languageCode = $this->getLanguage()->language_code;
        $view = 'theme::homepage.index';

        if ($page->id === intval($this->theme->get($languageCode . '_articles_page_id'))) {
            $view = 'theme::pages.articles';
        }

        return view($view, compact('page'));
    }


    /**
     * Render theme config in administration
     *
     * @param \Illuminate\View\View $view
     */
    public function viewConfig(\Illuminate\View\View $view){
        $languageCode = $this->getLanguage()->language_code;
        $view->language_code = $languageCode;

        $view->pages = \App\Models\Page\Page::whereLanguage($this->getLanguage())
            ->pluck('name', 'id');
        $view->pages->put('', '-- žádný --');
        $view->articlesPageId = $this->theme->get($languageCode . '_articles_page_id');
    }
}