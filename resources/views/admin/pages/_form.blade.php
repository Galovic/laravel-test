@include('admin.vendor.form.panel_errors')

<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab">Základní informace</a>
        </li>
        <li>
            <a href="#tab_seo" data-toggle="tab">SEO</a>
        </li>
        <li>
            <a href="#tab_publish" data-toggle="tab">Plánování</a>
        </li>
        <li>
            <a href="#tab_og" data-toggle="tab">OG Tagy</a>
        </li>
        <li>
            <a href="#tab_grid" data-toggle="tab">Grid</a>
        </li>
    </ul>
    <div class="tab-content">


        <div class="tab-pane has-padding active" id="tab_details">
            <div class="form-group required {{ $errors->has($name = 'name') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Název') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '255']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'url') ? 'has-error' : '' }}">
                {!! Form::label($name, 'URL') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '250']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            {{-- Page type --}}
            <div class="form-group {{ $errors->has($name = 'type') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Typ stránky') !!}
                <select name="{{ $name }}" class="select" id="input-type">
                    @foreach($pageTypes as $pageType)

                        <option value="{{ $pageType->id }}" data-need-view="{{ $pageType->need_view }}" {!!  isset($page) && $page->type == $pageType->id ? 'selected' : '' !!} >{{ $pageType->name }}</option>
                    @endforeach
                </select>

                @include('admin.vendor.form.field_error')
            </div>

            {{-- Page view --}}
            <div class="form-group required {{ $errors->has($name = 'view') ? 'has-error' : '' }}" id="view-input-wrapper">
                {!! Form::label($name, 'View') !!}
                {!! Form::select($name, $views, null, ['class' => 'select']) !!}

                @include('admin.vendor.form.field_error')
            </div>

            {{-- Parent --}}
            <div class="form-group {{ $errors->has($name = 'parent_id') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Nadřazená stránka') !!}

                <select class="select" name="{{ $name }}">
                    <option value="">-</option>

                    @foreach($pages as $page_all)
                        @include('admin.pages._select_nested', $page_all)
                    @endforeach

                </select>

                @include('admin.vendor.form.field_error')
            </div>

            {{-- Image --}}
            <div class="clearfix image-input-control">
                <div class="thumbnail display-inline-block pull-left">
                    <img :src="imageSrc" alt="Náhled obrázku" v-show="imageSrc">
                </div>
                <div class="col-xs-6">
                    <button class="btn btn-primary" type="button" @click="openFileInput">
                        <span v-if="!imageSelected">Vybrat obrázek</span>
                        <span v-if="imageSelected">Změnit obrázek</span>
                    </button>
                    <button class="btn btn-default" type="button" v-show="imageSelected" @click="removeImage">Odebrat obrázek</button>
                    @include('admin.vendor.form.field_error', ['name' => 'image'])
                    {{ Form::file('image', [
                        'id' => 'image-input',
                        'class' => 'hidden',
                        'accept' => "image/*",
                        '@change' => 'previewThumbnail'
                    ]) }}
                    {{ Form::hidden('remove_image', null, [
                        ':value' => 'removeImageValue'
                    ]) }}
                </div>
            </div>

            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('is_homepage', 1, isset($page)?$page->is_homepage:false, ['class' => 'switchery']) !!}
                    Úvodní strana
                </label>
            </div>


            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('listed', '1', isset($page)?$page->listed:true, ['class' => 'switchery']) !!}
                    Viditelné v menu
                </label>
            </div>


            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('published', '1', isset($page)?$page->listed:true, ['class' => 'switchery']) !!}

                    Zveřejněné
                </label>
            </div>
        </div>


        <div class="tab-pane has-padding" id="tab_seo">
            <h6 class="panel-title mb-5">SEO vlastnosti</h6>
            <p class="mb-15">
                Prosím vyplňte následující pole, které umožňují vyhledávačum lépe nalézt stránku.
                <br/>
                <kbd>SEO title</kbd> je nadpis stránky v prohlížeči. Pokud pole nevyplníte, automaticky se do nadpisu stránky vloží název článku.
                <br/>
                <kbd>SEO description</kbd> je text, který je zobrazen u popisku stránky ve výsledku vyhledávání.<br/>
                <kbd>SEO keywords</kbd> jsou kličová slova/sousloví, která identifikují článek. Oddělujte čárkou nebo
                tlačítekm ENTER.
            </p>

            <div class="form-group">
                {!! Form::label('seo_title', 'SEO title') !!}
                {!! Form::text('seo_title', null, ['class' => 'form-control maxlength', 'maxlength' => '60', 'placeholder' => (isset($page)?(!$page->seo_title?$page->name:$page->seo_title):"")]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('seo_description', 'SEO description') !!}
                {!! Form::textarea('seo_description', null, ['class' => 'form-control maxlength noresize small', 'maxlength' => '160', 'id' => 'seo_description']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('seo_keywords', 'SEO keywords') !!}
                {!! Form::text('seo_keywords', null, ['class' => 'form-control tags-input']) !!}
            </div>

            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('seo_index', '1', isset($page)?$page->seo_index:true, ['class' => 'switchery']) !!}

                    Indexovat
                </label>
            </div>

            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('seo_follow', '1', isset($page)?$page->seo_follow:true, ['class' => 'switchery']) !!}

                    Follow
                </label>
            </div>

            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('seo_sitemap', '1', isset($page)?$page->seo_sitemap:true, ['class' => 'switchery']) !!}

                    Zobrazovat na sitemap
                </label>
            </div>
        </div>


        <div class="tab-pane has-padding" id="tab_publish">
            <h6 class="panel-title mb-5">Plánování</h6>
            <p class="mb-15">
                Publikaci článků je možné naplánovat.<br/>
                Jakmile je zadané pole <kbd>Publikovat</kbd>, tak článek se zobrazí na webu od tohoto data.<br/>
                Pokud je zadané pole <kbd>Odpublikovat</kbd>, tak článek se skryje z webu od tohoto data.<br/>
                V případě, že není nějaké z těchto polí vyplněné, článek je ihned publikován, příp. je zobrazen na webu
                na neomezenou dobu.
            </p>

            <div class="form-group">
                {!! Form::label('publish_at', 'Publikovat') !!}
                <div>
                    <div class="input-group input-group-date">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('publish_at_date', isset($page)?$page->published_at->format('d.m.Y'):null, ['class' => 'form-control pickadate-format', 'placeholder' => 'Zvolte datum']) !!}
                    </div>

                    <div class="input-group input-group-time">
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                        {!! Form::text('publish_at_time', isset($page)?$page->published_at->format('H:i'):null, ['class' => 'form-control', 'id' => 'anytime-time']) !!}
                    </div>

                    <br class="clearfix">
                </div>
                <br class="clearfix">
            </div>


            <div class="checkbox checkbox-switchery">
                <label data-toggle="collapse" data-target="#set_unpublished_at">
                    {!! Form::checkbox('set_unpublished_at', '0', isset($page)&&$page->unpublished_at?true:false, ['class' => 'switchery']) !!}

                    Zvolit odpublikování stránky
                </label>
            </div>

            <div id="set_unpublished_at" class="collapse">
                <div class="form-group">
                    {!! Form::label('unpublish_at', 'Odpublikovat') !!}
                    <div>
                        <div class="input-group input-group-date">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('unpublish_at_date', isset($page)&&$page->unpublished_at?$page->unpublished_at->format('d.m.Y'):null, ['class' => 'form-control pickadate-format', 'placeholder' => 'Zvolte datum']) !!}
                        </div>

                        <div class="input-group input-group-time">
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                            {!! Form::text('unpublish_at_time', isset($page)&&$page->unpublished_at?$page->unpublished_at->format('H:i'):null, ['class' => 'form-control', 'id' => 'anytime-time-unpublish']) !!}
                        </div>

                        <br class="clearfix mb10">
                    </div>
                </div>
            </div>
        </div>


        <div class="tab-pane has-padding" id="tab_og">
            {{-- TODO: translate description--}}
            <h6 class="panel-title mb-5">OG Tags</h6>
            <p class="mb-15">
                The Open Graph protocol enables any web page to become a rich object in a social graph. For instance,
                this is used on Facebook to allow any web page to have the same functionality as any other object on
                Facebook.<br/>
                <kbd>og:title</kbd> - The title of your object as it should appear within the graph, e.g., "The
                Rock".<br/>
                <kbd>og:type</kbd> - The type of your object, e.g., "video.movie". Depending on the type you specify,
                other properties may also be required.<br/>
                <kbd>og:image</kbd> - An image URL which should represent your object within the graph.<br/>
                <kbd>og:url</kbd> - The canonical URL of your object that will be used as its permanent ID in the graph,
                e.g., "http://www.imdb.com/title/tt0117500/".<br/>
                <kbd>og:description</kbd> - A one to two sentence description of your object.<br/>
            </p>

            <div class="form-group">
                {!! Form::label('og_title', 'OG:title') !!}
                {!! Form::text('og_title', null, ['class' => 'form-control maxlength', 'maxlength' => '60', 'placeholder' => (isset($page)?(!$page->og_title?$page->name:$page->og_title):"")]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('og_type', 'OG:type') !!}
                {!! Form::text('og_type', null, ['class' => 'form-control ']) !!}
            </div>


            <div class="form-group">
                {!! Form::label('og_url', 'OG:url') !!}
                {!! Form::text('og_url', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('og_description', 'OG:description') !!}
                {!! Form::text('og_description', null, ['class' => 'form-control']) !!}
            </div>


            {{--<div class="form-group {{ $errors->has($name = 'og_image') ? 'has-error' : '' }}">--}}
                {{--{!! Form::label($name, 'OG:image') !!}--}}
                {{--{!! Form::file($name, ['class' => 'file-styled']) !!}--}}

                {{--@if(isset($page) && $page->og_image)--}}

                    {{--<img src="/media/upload/thumbnail/{{ $page->og_image }}"--}}
                         {{--alt="{{ $page->og_title == ""?$page->name:$page->og_title }}">--}}

                {{--@endif--}}

                {{--@include('admin.vendor.form.field_error')--}}
            {{--</div>--}}


        </div>


        <div class="tab-pane has-padding" id="tab_grid">

            <div id="grid" style="display: none;">
                {!! $page->content ?? old('content')!!}
            </div>
            <input type="hidden" name="content" id="page_content">
            {{ Form::hidden('modules', old('modules'), ['id' => 'page_modules']) }}
            <input type="hidden" name="helper_page_id" value="{{ time() }}">
            {!! Form::hidden('grid_style','',['id'=>'grid_style']) !!}
            {{-- Page content id is available and supported only for updating page --}}
            @if (isset($pageContentId))
                {!! Form::hidden('page_content_id', $pageContentId, ['id' => 'page-content-id']) !!}
            @endif

        </div>
    </div>
</div>


<!-- Remote source -->
<div id="modules_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Výběr modulů</h5>
            </div>

            <div class="modal-body clearfix">
                @foreach($modules as $module)
                    @if (!$module->config('has-page-module', true))
                        @continue
                    @endif
                    <div class="col-xs-3">
                        <a href="{{ $module->route('create') }}"
                           class="views-default add-grid-module"
                           data-module-type="{{ $module->getName() }}"
                        >
                            <div class="thumb">
                                @include($module->getViewName('icon'))
                            </div>

                            <div class="caption text-center">
                                <h6 class="no-margin">
                                    {{ $module->getName() }}
                                </h6>
                            </div>
                        </a>
                    </div>

                @endforeach
            </div>

            <div class="modal-footer">
                <button type="button" class="btn bg-danger" data-dismiss="modal">Zavřít</button>
            </div>
        </div>
    </div>
</div>
<!-- /remote source -->


<!-- Remote source -->
<div id="modules-edit-modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-remote"></div>
        </div>
    </div>
</div>
<!-- /remote source -->


<div id="modal-template" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Konfigurace modulu</h5>
            </div>

            <div class="modal-body"></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Zrušit</button>
                <button type="button" class="btn btn-primary modal-submit">Uložit</button>
            </div>
        </div>
    </div>
</div>

@push('style')
{{ Html::style( elixir('css/grideditor.css') ) }}
@endpush

@push('script')

<script>
    var grideditorOpts = {
        urls: {
            modules: {
                edit: "{{ route('admin.pages.modules.edit') }}",
                load: "{{ route('admin.pages.modules.load') }}",
                submit: "{{ route('admin.pages.modules.validateAndPreview') }}"
            },
            @if (isset($page))
            switchVersion: "{{ route('admin.pages.switch_version', $page->id) }}",
            @endif
            imageSrc: {!! isset($page) && $page->image ? "\"" . $page->thumbnail_url . "\"" : "null" !!},
            defaultThumbnail: "{{ asset('media/admin/images/image_placeholder.png') }}"
        },

        pageId: {{ isset($page) ? $page->id : time() }},

        versions: {!! isset($page) ? $page->getVersionsJSON() : "[]" !!}
    }
</script>

{{ Html::script( elixir('js/jquery-ui.js') ) }}
{{ Html::script( elixir('js/grideditor.js') ) }}
{{ Html::script( url('js/uniform.js') ) }}
{{ Html::script( url('js/switchery.js') ) }}
{{ Html::script( url('js/pickadate.js') ) }}
{{ Html::script( url('js/select2.js') ) }}
{{ Html::script( url('js/bootstrap-tagsinput.js') ) }}
{{ Html::script( url('js/bootstrap-maxlength.js') ) }}
{{ Html::script( url('js/beautify.js') ) }}
{{ Html::script( url('plugins/ace/ace.js') ) }}
{{ Html::script( elixir('js/pages.form.js') ) }}
@endpush