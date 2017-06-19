@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                @if(Module::isEnabled('NewsletterPlugin'))

                    @if(\Modules\NewsletterPlugin\Models\Newsletter::wasSuccessful())
                        <div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">
                            <button type="button" class="close" data-dismiss="alert">
                                <span>×</span><span class="sr-only">Zavřít</span>
                            </button>
                            <span class="text-semibold">Hotovo!</span> Úspěšně jste se přihlásil/a k odběru newsletteru.
                        </div>
                    @endif

                    {!! Form::open( ['route' => 'module.newsletter_plugin.submit'] ) !!}

                    <div class="form-group required {{ $errors->has($name = 'email') ? 'has-error' : '' }}">
                        {!! Form::label($name, 'Email') !!}
                        {!! Form::email($name, null, [
                            'class' => 'form-control',
                            'placeholder' => 'Email'
                        ]) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="form-group mt15">
                        {!! Form::button( 'Přihlásit', ['class' => 'btn bg-teal-400', 'type' => 'submit'] ) !!}
                        <a href="{!! route('admin.module.newsletter_plugin') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                    </div>

                    {!! Form::close() !!}
                @endif

            </div>
        </div>
    </div>
@endsection