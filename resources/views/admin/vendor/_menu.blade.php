<div class="sidebar-category sidebar-category-visible">
    <div class="category-content no-padding">
        <ul class="navigation navigation-main navigation-accordion">
            <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
{{--            {{dd($MyNavBar)}}--}}
            @include('admin.vendor._menu_items', array('items' => $MyNavBar->roots()))
        </ul>
    </div>
</div>
