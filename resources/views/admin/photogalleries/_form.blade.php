@include('admin.vendor.form.panel_errors')

<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">Základní informace</a></li>
        <li><a href="#tab_photogallery" data-toggle="tab" aria-expanded="false">Fotogalerie</a></li>
        <li><a href="#tab_seo" data-toggle="tab" aria-expanded="false">SEO</a></li>
        <li><a href="#tab_publish" data-toggle="tab" aria-expanded="false">Plánování</a></li>
    </ul>

    {{-- GENERAL --}}

    <div class="tab-content">

        <div class="tab-pane has-padding active" id="tab_details">

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

            <div class="form-group">
                {!! Form::label('text', 'Text') !!}
                {!! Form::textarea('text', null, ['class' => 'form-control editor-full', 'id' => 'editor-full']) !!}
            </div>

            <div class="form-group required {{ $errors->has($name = 'sort') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Seřazení') !!}
                {!! Form::number($name, null, [
                    'class' => 'form-control',
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

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
                        {!! Form::text($name = 'publish_at_date', $photogallery->publish_at ? $photogallery->publish_at->format('d.m.Y') : null, ['class' => 'form-control pickadate', 'placeholder' => 'Zvolte datum']) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="input-group input-group-time">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        {!! Form::text($name = 'publish_at_time', $photogallery->publish_at ? $photogallery->publish_at->format('H:i') : null, ['class' => 'form-control pickatime']) !!}
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
                        {!! Form::text($name = 'unpublish_at_date', $photogallery->unpublish_at ? $photogallery->unpublish_at->format('d.m.Y') : null, ['class' => 'form-control pickadate', 'placeholder' => 'Zvolte datum']) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="input-group input-group-time">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        {!! Form::text($name = 'unpublish_at_time', $photogallery->unpublish_at ? $photogallery->unpublish_at->format('H:i') : null, ['class' => 'form-control pickatime']) !!}
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

    {!! Html::script( url("js/editable.js")) !!}
    {!! Html::script( url("plugins/plupload/plupload.full.min.js")) !!}
    {!! Html::script( url("plugins/plupload/plupload.queue.min.js")) !!}
    {!! Html::script( url("plugins/plupload/i18n/cs_new.js")) !!}

    <script>
        var photogalleryFormData = {

            uploadPhotoUrl: '{{ route('admin.photogalleries.upload_photo', $photogallery->id) }}',

            photoListUrl: "{{ route('admin.photogalleries.photo.list', $photogallery->id) }}",

            updatePhotoUrl: '{{ route('admin.photogalleries.photo.update', $photogallery->id) }}',
            filebrowserImageBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'photogallery',
                'id' => $photogallery->id ?: 0,
                'type' => 'Images'
            ]) }}',
            filebrowserImageUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Images',
                'model' => 'photogallery',
                'id' => $photogallery->id ?: 0
            ]) }}',
            filebrowserBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'photogallery',
                'id' => $photogallery->id ?: 0,
                'type' => 'Files'
            ]) }}',
            filebrowserUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Files',
                'model' => 'photogallery',
                'id' => $photogallery->id ?: 0
            ]) }}'

        };
    </script>

    {!! Html::script( elixir('js/photogallery.form.js') ) !!}

@endpush