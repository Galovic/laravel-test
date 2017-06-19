{{ Form::model($user, ['id' => 'form-account-edit', 'files' => true]) }}

    <div class="form-group {{ $errors->has($name = 'firstname') ? 'has-error' : '' }}">
        {!! Form::label($name, 'Jméno') !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '250'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'lastname') ? 'has-error' : '' }}">
        {!! Form::label($name, 'Příjmení') !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '250'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'email') ? 'has-error' : '' }}">
        {!! Form::label($name, 'E-mail') !!}
        {!! Form::email($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '255',
            'required'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'username') ? 'has-error' : '' }}">
        {!! Form::label($name, 'Přihlašovací jméno') !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '100'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'position') ? 'has-error' : '' }}">
        {!! Form::label($name, 'Název pozice') !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '250'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'about') ? 'has-error' : '' }}">
        {!! Form::label($name, 'Text o sobě') !!}
        {!! Form::textarea($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '1000'
        ]) !!}
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

    <div class="text-right">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Uložit</button>
    </div>

{{ Form::close() }}