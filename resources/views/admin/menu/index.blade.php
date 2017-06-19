@extends('admin.layouts.master')
@section('content')
    <div id="menu-page">
    <div class="row">
        <div class="col-md-3">

            <div class="tabbable tab-content-bordered">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="active">
                        <a href="#tab_tree" data-toggle="tab">Stránky</a>
                    </li>
                    <li>
                        <a href="#tab_url" data-toggle="tab">Vlastní stránky</a>
                    </li>
                    <li>
                        <a href="#tab_categories" data-toggle="tab">Kategorie</a>
                    </li>
                </ul>
                <div class="tab-content">

                    <div class="tab-pane has-padding active" id="tab_tree">
                        <div id="page-tree"></div>
                        <br>
                        <button class="btn bg-teal-400" @click="addSelectedPages">
                            Přidat vybrané stránky
                        </button>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane has-padding " id="tab_url">

                        <div class="form-group">
                            {{ Form::label('custom_page_name', 'Název', [
                                'class' => 'form-label'
                            ]) }}
                            {{ Form::text('custom_page_name', null, [
                                'class' => 'form-control',
                                'v-model' => 'customPage.name'
                            ]) }}
                        </div>

                        <div class="form-group">
                            {{ Form::label('custom_page_url', 'URL', [
                                'class' => 'form-label'
                            ]) }}
                            {{ Form::text('custom_page_url', null, [
                                'class' => 'form-control',
                                'placeholder' => 'http://',
                                'v-model' => 'customPage.url'
                            ]) }}
                        </div>

                        <button class="btn bg-teal-400" @click="addCustomPage">
                            Přidat vlastní položku
                        </button>
                    </div>

                    <div class="tab-pane has-padding" id="tab_categories">
                        <div id="category-tree"></div>
                        <br>
                        <button class="btn bg-teal-400" @click="addSelectedCategories">
                            Přidat vybrané kategorie
                        </button>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="panel panel-flat">
                <div class="panel-body">

                    <div class="form-group">
                        <label for="menu-select">Menu: </label>
                        <select id="menu-select" class="bootstrap-select change_menu">
                            <option v-for="menuComponent in menuList" value="@{{ $index }}">
                                @{{ menuComponent.name }}
                            </option>
                        </select>
                        {!! Form::button( 'Zobrazit', [
                            'class' => "btn bg-teal-400",
                        ]) !!}
                        <button class="btn bg-primary btn-labeled" @click="newMenuModal">
                            <b class="fa fa-trello"></b> Vytvořit nové menu
                        </button>
                    </div>

                </div>
            </div>
            <div class="panel panel-flat">
                <div class="panel-body" v-if="activeMenu">
                    <h3>@{{ activeMenu.name }}</h3>

                    <ol class="dd-list" id="menu-items-list">
                        <menu-item v-for="menuItem in activeMenu.items" :item="menuItem"></menu-item>
                    </ol>

                    <em v-show="!activeMenu.items.length">
                        Žádné položky
                    </em>

                    {{-- MENU SETTINGS --}}
                    <div class="row" style="margin-top:40px">
                        <div class="col-xs-12">
                            <h4>Nastavení menu</h4>

                            <dl>
                                <dt>Umístění v šabloně</dt>
                                @foreach($defaultTheme->menu_locations as $menu => $text)
                                <dd>
                                    <input type="checkbox"
                                           class="uniform"
                                           id="locations-{{$menu}}-menu"
                                           :checked="hasActiveMenuLocation('{{$menu}}')"
                                           @change="changedMenuLocation($event, '{{$menu}}')">
                                    <label for="locations-{{$menu}}-menu"> {{$text}}</label>
                                </dd>
                                @endforeach
                            </dl>
                        </div>
                    </div>

                    {{-- SAVE OR DELETE --}}
                    <div class="row" style="margin-top:40px">
                        <div class="col-xs-6">
                            <a href="{{ route('admin.menu.delete') }}" @click.prevent="deleteMenu($event.target.href)">Odstranit menu</a>
                        </div>

                        <div class="col-xs-6">
                            <button class="btn bg-teal-400 btn-labeled pull-right" @click="saveChanges">
                                <b class="fa fa-floppy-o"></b> Uložit změny
                            </button>
                        </div>
                    </div>
                </div>
                <div class="panel-body text-center" v-if="!activeMenu">
                    <em>Nejprve vytvořte nové menu</em>
                </div>
            </div>

            @include('admin.menu._item_template')

        </div>
    </div>

    <!-- Modal -->
    <div id="add-menu-modal" class="modal fade in">
        <div class="modal-dialog">
            <div class="modal-content">

                {{--Modal header--}}
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Vytvořit nové menu</h4>
                </div>

                {{-- Modal body --}}
                <div class="modal-body">

                    <div class="form-group">
                        {{ Form::label('name', 'Název', [
                            'class' => 'control-label'
                        ]) }}
                        {{ Form::text('name', null, [
                            'class' => 'form-control maxlength',
                            'maxlength' => '250',
                            'v-model' => 'newMenu.name'
                        ]) }}
                    </div>

                </div>

                {{-- Modal footer --}}
                <div class="modal-footer">
                    <button class="btn btn-default pull-left" type="button" data-dismiss="modal">
                        Zrušit
                    </button>
                    <button class="btn btn-primary" type="button" @click="createNewMenu">
                        Vytvořit
                    </button>
                </div>

            </div>
        </div>
    </div>
    </div>

    <!-- /default ordering -->
@endsection

@push('script')
{{ Html::script( url('js/jquery-ui.js') ) }}
{{ Html::script( url('js/select2.js') ) }}
{{ Html::script( url('js/uniform.js') ) }}
{{ Html::script( url('js/fancytree.js') ) }}

<script>

    var initData = {
        url: {
            newMenu: "{{ route('admin.menu.store') }}",
            saveMenu: "{{ route('admin.menu.update') }}",
            pageTree: "{{ url(route('admin.menu.pages')) }}",
            categoryTree: "{{ url(route('admin.menu.categories')) }}"
        },
        menuList: {!! json_encode($menuList) !!},
        menuLocations: {!! json_encode($defaultTheme->menu_locations_settings) !!}
    };

</script>

{{ Html::script( url('js/menu.page.js') ) }}

@endpush