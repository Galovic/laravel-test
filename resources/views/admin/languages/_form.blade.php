<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">Základní informace</a></li>
    </ul>
    <div class="tab-content">

        <div class="tab-pane has-padding active" id="tab_details">
            <div class="form-group required {{ $errors->has($name = 'name') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Název') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 255
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'country_code') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Kód státu') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 3
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'language_code') ? 'has-error' : '' }}">
                {!! Form::label($name,'Kód jazyka (ISO 639-1)') !!}
                {!! Form::text($name, isset($language)?$language->locale:null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 3
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'domain') ? 'has-error' : '' }}">
                {!! Form::label($name,'Doména pro jazyk') !!}
                {!! Form::text($name, $language->domain ?? null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 255,
                    'placeholder' => 'např. mujweb.cz'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="checkbox checkbox-switchery">
            <label>
                {!! Form::checkbox('enabled', 1, null, ['id' => 'input-enabled']) !!}

                Povoleno
            </label>
        </div>

        </div>

    </div>
</div>

@push('script')
    {{ Html::script( url('js/switchery.js') ) }}
    {{ Html::script( url('js/bootstrap-maxlength.js') ) }}
    <script>
        (function($){
            new Switchery(document.getElementById('input-enabled'));
            $('.maxlength').maxlength();
        })(jQuery)
    </script>
@endpush