<!-- Custom Tabs -->
<div class="tabbable tab-content-bordered" id="categories-form">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab" aria-expanded="true">Základní informace</a>
        </li>
        <li class="">
            <a href="#tab_seo" data-toggle="tab" aria-expanded="false">SEO</a>
        </li>
    </ul>


    <div class="tab-content">
        <div class="tab-pane has-padding active" id="tab_details">
            <div class="form-group {{ $errors->has($name = 'name') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Název') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '255',
                    '@change' => 'titleChanged($event.target.value)'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>
            <div class="form-group {{ $errors->has($name = 'url') ? 'has-error' : '' }}">
                {!! Form::label($name, 'URL') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '255',
                    'v-model' => 'url'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>
            <div class="form-group {{ $errors->has($name = 'parent_id') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Nadřazená kategorie') !!}
                <select name="{{ $name }}" class="form-control">
                    <option value="">Žádná</option>

                    @foreach($categories as $item)
                        @include('admin.categories._nested', $item)
                    @endforeach

                </select>
                @include('admin.vendor.form.field_error')
            </div>
            <br>
            <div class="checkbox checkbox-switchery {{ $errors->has($name = 'show') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Zobrazení na webových stánkách') !!}
                {!! Form::checkbox($name, 1, null, ['class' => 'switchery']) !!}
                @include('admin.vendor.form.field_error')
            </div>
        </div>



        <div class="tab-pane has-padding {{ $errors->has($name = 'seo_title') ? 'has-error' : '' }}" id="tab_seo">
            <div class="form-group">
                {!! Form::label($name, 'SEO title') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '60',
                    'v-model' => 'seo.title'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'seo_description') ? 'has-error' : '' }}">
                {!! Form::label($name, 'SEO description') !!}
                {!! Form::textarea($name, null, [
                    'class' => 'form-control maxlength noresize small',
                    'maxlength' => '160', 'id' => 'seo_description',
                    'v-model' => 'seo.description',
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'seo_keywords') ? 'has-error' : '' }}">
                {!! Form::label($name, 'SEO keywords') !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control tags-input',
                    '@keydown' => 'preventKeydownEnter($event)'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>
        </div>

    </div>
</div>

@push('script')
{!! Html::script( url('js/bootstrap-tagsinput.js') ) !!}
{!! Html::script( url('js/bootstrap-maxlength.js') ) !!}
{!! Html::script( url('js/switchery.js') ) !!}

{!! Html::script( url('/') . elixir('js/categories.form.js') ) !!}

@endpush