<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab" aria-expanded="true">Základní informace</a>
        </li>
        <li>
            <a href="#tab_seo" data-toggle="tab" aria-expanded="false">SEO</a>
        </li>
    </ul>
    <div class="tab-content">

        <div class="tab-pane has-padding active" id="tab_details">
            <div class="form-group required {{ $errors->has($name = 'title') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Název pozice') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '250']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'url') ? 'has-error' : '' }}">
                {!! Form::label($name, 'URL') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '250']) !!}
                @include('admin.vendor.form.field_error')
            </div>


            <div class="form-group {{ $errors->has($name = 'salary') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Mzda') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '250',
                    'placeholder' => 'např. 20 000 Kč'
                    ]) !!}
                @include('admin.vendor.form.field_error')
            </div>


            <div class="form-group {{ $errors->has($name = 'bound') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Typ úvazku') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '250',
                    'placeholder' => 'např. HPP nebo brigáda'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'perex') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Co budete dělat?') !!}
                @include('admin.vendor.form.field_error')
                {!! Form::textarea($name, null, ['class' => 'form-control editor-full', 'id' => 'editor-full']) !!}
            </div>

            <div class="form-group {{ $errors->has($name = 'requirements') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Co očekáváme?') !!}
                @include('admin.vendor.form.field_error')
                {!! Form::textarea($name, null, ['class' => 'form-control editor-full', 'id' => 'editor-full']) !!}
            </div>

            <div class="form-group {{ $errors->has($name = 'offerings') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Co nabízíme?') !!}
                @include('admin.vendor.form.field_error')
                {!! Form::textarea($name, null, ['class' => 'form-control editor-full', 'id' => 'editor-full']) !!}
            </div>

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

            <div class="form-group {{ $errors->has($name = 'sort') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Seřazení') !!}
                {!! Form::number($name, null, ['class' => 'form-control maxlength', 'maxlength' => '10', 'style' => 'max-width: 100px;']) !!}
                @include('admin.vendor.form.field_error')
            </div>

        </div>

        <div class="tab-pane has-padding" id="tab_seo">
            <h6 class="panel-title mb-5">SEO vlastnosti</h6>
            <p class="mb-15">
                Prosím vyplňte následující pole, které umožňují vyhledávačum lépe nalézt stránku.<br />
                <kbd>SEO title</kbd> je nadpis stránky v prohlížeči. Pokud pole nevyplníte, automaticky se do nadpisu stránky vloží název článku.<br />
                <kbd>SEO description</kbd> je text, který je zobrazen u popisku stránky ve výsledku vyhledávání.<br />
                <kbd>SEO keywords</kbd> jsou kličová slova/sousloví, která identifikují článek. Oddělujte čárkou nebo tlačítekm ENTER.
            </p>

            <div class="form-group {{ $errors->has($name = 'seo_title') ? 'has-error' : '' }}">
                {!! Form::label($name, 'SEO title') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '60']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'seo_description') ? 'has-error' : '' }}">
                {!! Form::label($name, 'SEO description') !!}
                {!! Form::textarea($name, null, ['class' => 'form-control maxlength noresize small', 'maxlength' => '160', 'id' => 'seo_description']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'seo_keywords') ? 'has-error' : '' }}">
                {!! Form::label($name, 'SEO keywords') !!}
                {!! Form::text($name, null, ['class' => 'form-control tags-input']) !!}
                @include('admin.vendor.form.field_error')
            </div>
        </div>

    </div>
</div>

@push('script')
{{ Html::script( url('js/bootstrap-tagsinput.js') ) }}
{{ Html::script( url('js/bootstrap-maxlength.js') ) }}
{!! Html::script( url("plugins/ckeditor/ckeditor.js") ) !!}
{!! Html::script( url('plugins/fancybox/jquery.fancybox.js') ) !!}

<script>
    new Vue({
        el: '#career-form',
        data: {
            defaultThumbnail: "{{ asset('media/admin/images/thumbnail100x100.png') }}",
            imageSrc: {!! isset($career) && $career->image ? "\"" . $career->thumbnail_url . "\"" : "null" !!},
            imageSelected: {!! isset($career) && $career->image ? "true" : "false" !!},
            filebrowserImageBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'career',
                'id' => $career->id ?: 0,
                'type' => 'Images'
            ]) }}',
            filebrowserImageUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Images',
                'model' => 'career',
                'id' => $career->id ?: 0
            ]) }}',
            filebrowserBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'career',
                'id' => $career->id ?: 0,
                'type' => 'Files'
            ]) }}',
            filebrowserUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Files',
                'model' => 'career',
                'id' => $career->id ?: 0
            ]) }}'
        },
        computed: {
            removeImageValue: function() {
                return this.imageSelected ? 'false' : 'true';
            }
        },
        methods: {
            openFileInput: function () {
                $('#image-input').click();
            },
            removeImage: function () {
                this.imageSrc = this.defaultThumbnail;
                this.imageSelected = false;
                $('#image-input').val('');
            },
            previewThumbnail: function (event) {
                var input = event.target;

                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    var vm = this;

                    reader.onload = function(e) {
                        vm.imageSrc = e.target.result;
                        vm.imageSelected = true;
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
        },
        created: function () {
            if (!this.imageSrc) {
                this.imageSrc = this.defaultThumbnail;
            }
        },
        ready: function () {
            var self = this;

            // Tagsinput
            $('.tags-input').tagsinput();

            // Maxlength
            $('.maxlength').maxlength({
                alwaysShow: true
            });

            // CKeditor
            $('.editor-full').each(function(){
                CKEDITOR.replace(this, {
                    filebrowserImageBrowseUrl: self.filebrowserImageBrowseUrl,
                    filebrowserImageUploadUrl: self.filebrowserImageUploadUrl,
                    filebrowserBrowseUrl: self.filebrowserBrowseUrl,
                    filebrowserUploadUrl: self.filebrowserUploadUrl,
                    removeDialogTabs: 'link:upload;image:upload',
                    height: '400px'
                });
            });
        }
    });
</script>
@endpush