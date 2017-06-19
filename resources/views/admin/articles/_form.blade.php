@include('admin.vendor.form.panel_errors')

<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">Základní informace</a></li>
        <li><a href="#tab_categories" data-toggle="tab" aria-expanded="false">Kategorie</a></li>
        <li><a href="#tab_photogallery" data-toggle="tab" aria-expanded="false">Fotogalerie</a></li>
        <li><a href="#tab_seo" data-toggle="tab" aria-expanded="false">SEO</a></li>
        <li><a href="#tab_publish" data-toggle="tab" aria-expanded="false">Plánování</a></li>
    </ul>

    {{-- GENERAL --}}

    <div class="tab-content">

        <div class="tab-pane has-padding active" id="tab_details">

            <div class="form-group">
                @foreach (config('admin.article_type') as $id => $type)
                <label class="article_type">{!! Form::radio('type', $id, ($id == 1 && Session::get('listing_form_data.type') === null) ? true : null) !!} {{ $type['title'] }}</label>
                @endforeach
                @include('admin.vendor.form.field_error', [ 'name' => 'type' ])
            </div>

            <div class="form-group required {{ $errors->has($name = 'title') ? 'has-error' : '' }}">
                {!! Form::label('title', 'Nadpis') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '250',
                    '@change' => 'titleChanged($event.target.value)'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'url') ? 'has-error' : '' }}">
                {!! Form::label('url', 'URL') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '250',
                    'v-model' => 'url'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            {{-- ARTICLE TAGGING --}}
            @if (config('project.functionality.article_tags'))
                <div class="form-group {{ $errors->has($name = 'tags') ? 'has-error' : '' }}">
                    {!! Form::label($name, 'Tagy') !!}
                    {!! Form::text($name, $article->tags()->pluck('name')->implode(','), [
                        'class' => 'form-control js-article-tags'
                    ]) !!}
                    @include('admin.vendor.form.field_error')
                </div>
            @endif

            <div class="form-group required {{ $errors->has($name = 'perex') ? 'has-error' : '' }}">
                {!! Form::label('perex', 'Perex') !!}
                {!! Form::textarea($name, null, ['class' => 'form-control noresize medium']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group">
                {!! Form::label('text', 'Text') !!}
                {!! Form::textarea('text', null, ['class' => 'form-control editor-full', 'id' => 'editor-full']) !!}
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

        </div>

        {{-- CATEGORIES --}}

        <div class="tab-pane has-padding" id="tab_categories">

            <h6 class="panel-title mb-5">Výběr kategorií</h6>
            <p class="mb-15">Prosím vyberte alespoň jednu kategorii ve které bude článek uložen. Využívat můžete i <kbd>klávesnici</kbd>.</p>

            <div class="tree-checkbox well border-left-lg"></div>

            {!! Form::hidden('categories_imploded', isset($articleCategories) ? $articleCategories : NULL, ['id' => 'categories_imploded']) !!}

        </div>

        {{-- PHOTOGALLERY --}}

        <div class="tab-pane has-padding" id="tab_photogallery">

            <h6 class="panel-title mb-5">Fotogalerie</h6>
            <p class="mb-15">Uploadujte své fotografie. Povolené formáty jsou <kbd>JPEG</kbd>, <kbd>PNG</kbd> a <kbd>GIF</kbd>.</p>

            <div class="file-uploader"><p>Ve vašem prohlížeči není nainstalovaný flash.</p></div>

            <div v-if="isPhotogalleryLoading">
                <div>
                    <i class="fa fa-spinner fa-spin fa-fw spinner"></i> Načítám
                </div>
            </div>

            <div id="result"></div>

            <a href="#" @click.prevent="displayImages">Obnovit</a>

        </div>

        {{-- SEO --}}

        <div class="tab-pane has-padding" id="tab_seo">

            <h6 class="panel-title mb-5">SEO vlastnosti</h6>
            <p class="mb-15">
                Prosím vyplňte následující pole, které umožňují vyhledávačum lépe nalézt stránku.<br />
                <kbd>SEO title</kbd> je nadpis stránky v prohlížeči. Pokud pole nevyplníte, automaticky se do nadpisu stránky vloží název článku.<br />
                <kbd>SEO description</kbd> je text, který je zobrazen u popisku stránky ve výsledku vyhledávání.<br />
                <kbd>SEO keywords</kbd> jsou kličová slova/sousloví, která identifikují článek. Oddělujte čárkou nebo tlačítekm ENTER.
            </p>

            <div class="form-group {{ $errors->has($name = 'seo_title') ? 'has-error' : '' }}">
                {!! Form::label('seo_title', 'SEO title') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '60',
                    'v-model' => 'seo.title'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'seo_description') ? 'has-error' : '' }}">
                {!! Form::label('seo_description', 'SEO description') !!}
                {!! Form::textarea($name, null, [
                    'class' => 'form-control maxlength noresize small',
                    'maxlength' => '160',
                    'id' => 'seo_description',
                    'v-model' => 'seo.description',
                    '@keydown' => 'onSeoDescriptionKeyDown'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'seo_keywords') ? 'has-error' : '' }}">
                {!! Form::label('seo_keywords', 'SEO keywords') !!}
                {!! Form::text($name, null, ['class' => 'form-control tags-input']) !!}
                @include('admin.vendor.form.field_error')
            </div>
        </div>

        {{-- PLANNING --}}

        <div class="tab-pane has-padding" id="tab_publish">
            <h6 class="panel-title mb-5">Plánování</h6>
            <p class="mb-15">
                Publikaci článků je možné naplánovat.<br />
                Jakmile je zadané pole <kbd>Publikovat</kbd>, tak článek se zobrazí na webu od tohoto data.<br />
                Pokud je zadané pole <kbd>Odpublikovat</kbd>, tak článek se skryje z webu od tohoto data.<br />
                V případě, že není nějaké z těchto polí vyplněné, článek je ihned publikován, příp. je zobrazen na webu na neomezenou dobu.
            </p>

            <div class="form-group {{ $errors->has('publish_at_date') || $errors->has('publish_at_time') ? 'has-error' : '' }}">
                {!! Form::label('publish_at', 'Publikovat') !!}
                <div>
                    <div class="input-group input-group-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {!! Form::text($name = 'publish_at_date', $article->publish_at ? $article->publish_at->format('d.m.Y') : null, ['class' => 'form-control pickadate', 'placeholder' => 'Zvolte datum']) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="input-group input-group-time">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        {!! Form::text($name = 'publish_at_time', $article->publish_at ? $article->publish_at->format('H:i') : null, ['class' => 'form-control pickatime']) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <br class="clearfix">
                </div>
                <br class="clearfix">
            </div>

            <div class="form-group {{ $errors->has('unpublish_at_date') || $errors->has('unpublish_at_time') ? 'has-error' : '' }}">
                {!! Form::label('unpublish_at', 'Odpublikovat') !!}
                <div>
                    <div class="input-group input-group-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {!! Form::text($name = 'unpublish_at_date', $article->unpublish_at ? $article->unpublish_at->format('d.m.Y') : null, ['class' => 'form-control pickadate', 'placeholder' => 'Zvolte datum']) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="input-group input-group-time">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        {!! Form::text($name = 'unpublish_at_time', $article->unpublish_at ? $article->unpublish_at->format('H:i') : null, ['class' => 'form-control pickatime']) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <br class="clearfix mb10">
                </div>
            </div>
        </div>

    </div>
</div>

@push('script')
    {!! Html::script( url('js/bootstrap-tagsinput.js') ) !!}
    {!! Html::script( url('js/bootstrap-maxlength.js') ) !!}
    {!! Html::script( url('js/pickadate.js') ) !!}
    {!! Html::script( url("plugins/ckeditor/ckeditor.js") ) !!}
    {!! Html::script( url('plugins/fancybox/jquery.fancybox.js') ) !!}
    {!! Html::script( url('js/fancytree.js') ) !!}

    @if (config('project.functionality.article_tags'))
        {!! Html::script( url('js/typeahead.js') ) !!}
    @endif

    {!! Html::script( url("js/editable.js")) !!}
    {!! Html::script( url("plugins/plupload/plupload.full.min.js")) !!}
    {!! Html::script( url("plugins/plupload/plupload.queue.min.js")) !!}
    {!! Html::script( url("plugins/plupload/i18n/cs_new.js")) !!}

    <script>
        var articlesFormData = {
            categoriesTreeUrl: "{{ route('admin.articles.categories_tree', $article->id ?? null) }}",
            uploadPhotoUrl: '{{ route('admin.articles.upload_photo', $article->id ?? null) }}',

            photoListUrl: "{{ route('admin.articles.photo.list', $article->id ?? null) }}",

            updatePhotoUrl: '{{ route('admin.articles.photo.update', $article->id ?? null) }}',

            imageSrc: {!! isset($article) && $article->image ? "\"" . $article->thumbnail_url . "\"" : "null" !!},
            defaultThumbnail: "{{ asset('media/admin/images/image_placeholder.png') }}",

            deletePhotoUrl: "/admin/ajax/photogallery-photo-delete/{{$article->id}}/",

            filebrowserImageBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'article',
                'id' => $article->id,
                'type' => 'Images'
            ]) }}',
            filebrowserImageUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Images',
                'model' => 'article',
                'id' => $article->id
            ]) }}',
            filebrowserBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'article',
                'id' => $article->id,
                'type' => 'Files'
            ]) }}',
            filebrowserUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Files',
                'model' => 'article',
                'id' => $article->id
            ]) }}',

            @if (config('project.functionality.article_tags'))
            tags: {!! $tags->toJson() !!}
            @endif
        };
    </script>

    {!! Html::script( url('js/articles.form.js') ) !!}

@endpush