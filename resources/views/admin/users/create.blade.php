@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::open( ['route' => 'admin.users.store'] ) !!}

                @include('admin.users._form')

                <div class="form-group mt15">
                    {!! Form::button( trans('general.button.create'), [
                        'class' => 'btn bg-teal-400',
                        'type' => 'submit'
                    ] ) !!}
                    <a href="{!! route('admin.users') !!}" title="{{ trans('general.button.cancel') }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection

