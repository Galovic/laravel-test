@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::model( $role, ['route' => [ 'admin.roles.update', $role->id ], 'method' => 'PATCH'] ) !!}

                @include('admin.roles._form')

                <div class="form-group mt15">
                    {!! Form::button( trans('general.button.update'), [
                        'class' => 'btn bg-teal-400',
                        'type' => 'submit',
                        'id' => 'btn-submit-edit'
                    ]) !!}
                    <a href="{!! route('admin.roles') !!}" title="{{ trans('general.button.cancel') }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection
