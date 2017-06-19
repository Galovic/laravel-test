@extends('admin.layouts.blank')

@section('content')
    <form method="POST">
        {!! csrf_field() !!}
        <input type="hidden" name="token" value="{{ $token }}">

        @include('vendor.form._errors')


        <div class="panel panel-body login-form">
            <div class="text-center">
                <div class="icon-object border-warning text-warning">
                    &nbsp;<i class="fa fa-lock"></i>&nbsp;
                </div>
                <h5 class="content-group">Password reset <small class="display-block">We'll send you instructions in email</small></h5>
            </div>

            <div class="form-group has-feedback">
                <input type="email" name="email" value="{{ $email }}" class="form-control" placeholder="E-mail" required/>
                <div class="form-control-feedback">
                    <i class="icon-mail5 text-muted"></i>
                </div>
            </div>

            <div class="form-group has-feedback">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required/>
                <div class="form-control-feedback">
                    <i class="icon-lock2 text-muted"></i>
                </div>
            </div>

            <div class="form-group has-feedback">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Password" required/>
                <div class="form-control-feedback">
                    <i class="icon-lock2 text-muted"></i>
                </div>
            </div>

            <button type="submit" class="btn bg-blue btn-block">Reset password <i class="icon-arrow-right14 position-right"></i></button>
            {!! link_to_route('admin.auth.login', 'Sign in', [], ['class' => "text-center row show"]) !!}<br>
        </div>
    </form>
@endsection
