@extends('admin.layouts.blank')

@section('content')
    <!-- Advanced login -->

    {{ Form::open([ 'id' => 'login-form' ]) }}
        @if (isset($redirect_to))
            <input type="hidden" name="_redirect" value="{{ $redirect_to }}"/>
        @endif

        <div class="panel panel-body login-form">
            <div class="text-center">
                <div class="icon-object border-warning-400 text-warning-400">
                    <i class="fa fa-users"></i>
                </div>
                <h5 class="content-group-lg">
                    Přihlášení k účtu <small class="display-block">Vyplňte své přihlašovací údaje</small>
                </h5>
            </div>

            @include('vendor.form._errors')

            <div class="form-group has-feedback has-feedback-left">
                <input type="text" id="username" name="username" class="form-control" placeholder="Uživatel" value="{{ old('username') }}" required autofocus/>
                <div class="form-control-feedback">
                    <i class="fa fa-user text-muted"></i>
                </div>
            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input type="password" id="password" name="password" class="form-control" placeholder="Heslo" required/>
                <div class="form-control-feedback">
                    <i class="fa fa-lock text-muted"></i>
                </div>
            </div>

            <div class="form-group login-options">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="styled" id="remember" name="remember"/> Zapamatovat
                        </label>
                    </div>

                    <div class="col-sm-6 text-right">
                        {{ Html::link(route('admin.password.forgot'), 'Zapomenuté heslo') }}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn bg-blue btn-block">
                    Přihlásit se <i class="icon-circle-right2 position-right"></i>
                </button>
            </div>

            <span class="help-block text-center no-margin">
                Přihlášením souhlasíte s ukládáním souborů cookie.
            </span>
        </div>
    {{ Form::close() }}
@endsection

@push('script')
<script>
    (function($){
        $('.styled').uniform();
    })(jQuery);
</script>
@endpush