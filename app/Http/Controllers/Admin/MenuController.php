<?php
namespace App\Http\Controllers\Admin;

use App\Models\Article\Category;
use App\Models\Menu\Menu;
use App\Models\Page\Page;
use App\Models\Web\Theme;
use Illuminate\Http\Request;

class MenuController extends AdminController
{
    /**
     * MenuController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:menu-show')->only('index');
        $this->middleware('permission:menu-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:menu-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:menu-delete')->only('delete');
    }


    /**
     * Request: show menus
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        $this->setTitleDescription('Menu', 'správa menu');
        $menuList =  Menu::toJsonStructure($this->getLanguage());
        $defaultTheme = Theme::getDefault();
        return view('admin.menu.index', compact('menuList', 'defaultTheme'));
    }


    /**
     * Request: Get page tree
     *
     * @return mixed
     */
    public function pagesTree()
    {
        return Page::getTree(Page::whereLanguage($this->getLanguage()));
    }


    /**
     * Request: Get categories tree
     *
     * @return mixed
     */
    public function categoriesTree()
    {
        return Category::getTree(Category::whereLanguage($this->getLanguage()));
    }


    /**
     * Request: create new menu
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
            ]);

        $menu = new Menu($request->only([ 'name' ]));
        $menu->language_id = $this->getLanguage()->id;
        $menu->save();

        return response()->json(['id' => $menu->id ]);
    }


    /**
     * Request: Save menus
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $menuListInput = $request->input('menu');
        if(!$menuListInput) {
            return response()->json(['error' => 'Chybejici data']);
        }

        $menuList = json_decode($menuListInput);

        foreach($menuList as $menuObject){
            $menu = Menu::findOrFail($menuObject->id);
            $preserveIds = $this->updateMenu($menu, $menuObject->items);

            $menu->items()->whereLanguage($this->getLanguage())
                ->whereNotIn('id', $preserveIds)->delete();
        }

        // Set theme menu locations
        if($menuLocationsInput = $request->input('menuLocations')){
            $menuLocations = json_decode($menuLocationsInput);
            $defaultTheme = Theme::getDefault();

            foreach($menuLocations as $location => $menuId){
                $defaultTheme->setMenuLocation($location, $menuId);
            }
        }
    }


    /**
     * Updates menu items and options
     *
     * @param $menu
     * @param $items
     * @param null $parentId
     * @return array
     */
    private function updateMenu($menu, $items, $parentId = null){
        $order = 1;
        $itemIdsPreserve = [];

        foreach($items as $itemObject){

            $itemData = [
                'name' => $itemObject->name,
                'language_id' => $this->getLanguage()->id,
                'url' => isset($itemObject->url) ? $itemObject->url : null,
                'order' => isset($itemObject->order) ? $itemObject->order : 1,
                'parent_id' => $parentId,
                'class' => isset($itemObject->class) ? $itemObject->class : null,
                'open_new_window' => isset($itemObject->openNewWindow) ? !!$itemObject->openNewWindow : false,
                'page_id' => isset($itemObject->pageId) ? $itemObject->pageId : null,
                'category_id' => $itemObject->categoryId ?? null
            ];
            dump($itemData);

            // Editing existing item
            if($itemObject->id){
                $item = $menu->items->find($itemObject->id);
                if(!$item) continue;
                $item->update($itemData);
            }
            // Creating new item
            else{
                $item = $menu->items()->create($itemData);
            }

            $itemIdsPreserve[] = $item->id;
            if($itemObject->children){
                $preserveFromChildren = $this->updateMenu($menu, $itemObject->children, $item->id);
                $itemIdsPreserve = array_merge($itemIdsPreserve, $preserveFromChildren);
            }

            $order++;
        }

        return $itemIdsPreserve;
    }


    /**
     * Delete menu
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function delete(Request $request)
    {
        $menuId = $request->input('id');
        if(!$menuId) return;

        Menu::findOrFail($menuId)->delete();
        return response()->json([ 'ok' => true ]);
    }
}