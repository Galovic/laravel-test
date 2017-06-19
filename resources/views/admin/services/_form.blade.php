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
                {!! Form::label($name, 'Nadpis') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '250']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'url') ? 'has-error' : '' }}">
                {!! Form::label($name, 'URL') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '250']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'perex') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Perex') !!}
                {!! Form::textarea($name, null, ['class' => 'form-control noresize medium']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'text') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Text') !!}
                {!! Form::textarea($name, null, ['class' => 'form-control editor-full', 'id' => 'editor-full']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'sort') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Seřazení') !!}
                {!! Form::number($name, null, ['class' => 'form-control maxlength', 'maxlength' => '10', 'style' => 'max-width: 100px;']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'image') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Obrázek') !!}
                {!! Form::file($name, ['class' => 'file-styled']) !!}

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
    (function($){
        // Tagsinput
        $('.tags-input').tagsinput();

        // Maxlength
        $('.maxlength').maxlength({
            alwaysShow: true
        });

        // CKeditor
        var roxyFileman = '/media/fileman/index.html';
        $(function(){
            CKEDITOR.replace( 'editor-full',{filebrowserBrowseUrl:roxyFileman,
                filebrowserImageBrowseUrl:roxyFileman+'?type=image',
                removeDialogTabs: 'link:upload;image:upload',
                height: '400px'
            });
        });

        // Uniform
        $(".file-styled").uniform({
            fileButtonHtml: '<i class="fa fa-image"></i>',
            wrapperClass: 'bg-teal-400'
        });

        // Lightbox
        $('[data-popup="lightbox"]').fancybox({
            padding: 0
        });

        // Automatic complete url and SEO attributes
        $( "#title" ).blur(function()
        {
            var src= document.getElementById("title");
            // delete diacritics
            sdiak="áäčďéěíĺľňóôőöŕšťúůűüýřžÁÄČĎÉĚÍĹĽŇÓÔŐÖŔŠŤÚŮŰÜÝŘŽ";
            bdiak="aacdeeillnoooorstuuuuyrzAACDEEILLNOOOORSTUUUUYRZ";

            tx=""; txt=src.value;

            for( p=0 ; p < txt.length ; p++){
                if (sdiak.indexOf(txt.charAt(p))!=-1) tx+=bdiak.charAt(sdiak.indexOf(txt.charAt(p)));
                else tx+=txt.charAt(p);
            }
            //change gap
            tx = tx.split(' ').join('-');
            // change big word
            tx = tx.toLowerCase();

            // add text to SEO bookmarks
            var dest= document.getElementById("url");
            if(!dest.value) dest.value=tx;

            dest= document.getElementById("seo_title");
            if(!dest.value) dest.value=src.value;

            dest= document.getElementById("seo_description");
            if(!dest.value) dest.value=src.value;

        });

    })(jQuery);
</script>
@endpush