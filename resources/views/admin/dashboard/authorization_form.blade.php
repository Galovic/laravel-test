@extends('admin.layouts.master')

@section('content')
    <div class='row'>

        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h2 class="panel-title">Google Analytics - Autorizace</h2>

                    {{ Form::open(['route' => 'admin.dashboard.authorization', 'id' => 'ga-auth-form']) }}

                    <p>
                        Nejprve je potřeba pomocí tohoto odkazu <a href="javascript:window.open('{!! $authUrl !!}', 'ga_auth_win', 'width=800, height=600')">získat kód</a>.
                    </p>

                    <div class="form-group required {{ $errors->has($name = 'code') ? 'has-error' : '' }}">
                        {!! Form::label($name, 'Kód:') !!}
                        {!! Form::text($name, null, [
                            'class' => 'form-control',
                            'maxlength' => 255
                        ]) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="form-group mt15">
                        {!! Form::button( 'Autorizovat', ['class' => 'btn bg-teal-400 pull-right', 'type' => 'submit'] ) !!}
                        <div class="clearfix"></div>
                    </div>
                    {{ Form::close() }}

                </div>
            </div>
        </div>


    </div>
@endsection