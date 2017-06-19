@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">
                {!! Form::model( $form, [
                    'route' => ['admin.module.forms_plugin.update', $form->id],
                    'method' => 'PATCH',
                    'id' => 'forms-plugin-form',
                    '@submit.prevent' => 'submitForm'
                ]) !!}

                @include('module-formsplugin::admin._form')

                <div class="form-group mt15">
                    {!! Form::button('Upravit', ['class' => 'btn bg-teal-400', 'id' => 'btn-submit-edit', 'type' => 'submit'] ) !!}
                    <a href="{!! route('admin.module.forms_plugin') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection