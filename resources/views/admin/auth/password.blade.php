@extends('admin.layouts.blank')

@section('content')
    {{ Form::open([ 'route' => 'admin.password.forgot' ]) }}
        <div class="panel panel-body login-form">
            <div class="text-center">
                <div class="icon-object border-warning text-warning">
                    <i class="fa fa-unlock"></i>
                </div>
                <h5 class="content-group">Password recovery <small class="display-block">We'll send you instructions in email</small></h5>
            </div>

            @include('vendor.form._errors')

            <div class="form-group has-feedback">
                {{ Form::email('email', null, [
                    'class' => 'form-control',
                    'required', 'autofocus',
                    'placeholder' => 'E-mail'
                ]) }}
                <div class="form-control-feedback">
                    <i class="fa fa-at text-muted"></i>
                </div>
            </div>

            <button type="submit" class="btn bg-blue btn-block">Reset password <i class="icon-arrow-right14 position-right"></i></button>
            {!! link_to_route('admin.auth.login', 'Sign in', [], ['class' => "text-center row show"]) !!}<br>
        </div>
    {{ Form::close() }}
@endsection