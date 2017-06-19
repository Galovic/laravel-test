@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">
                {!! Form::model($newsletter) !!}

                <div class="form-group required {{ $errors->has($name = 'email') ? 'has-error' : '' }}">
                    {!! Form::label($name, 'Email') !!}
                    {!! Form::email($name, null, [
                        'class' => 'form-control',
                        'placeholder' => 'Email'
                    ]) !!}
                    @include('admin.vendor.form.field_error')
                </div>

                <div class="form-group mt15">
                    {!! Form::button( 'Upravit', ['class' => 'btn bg-teal-400', 'type' => 'submit'] ) !!}
                    <a href="{!! route('admin.module.newsletter_plugin') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection