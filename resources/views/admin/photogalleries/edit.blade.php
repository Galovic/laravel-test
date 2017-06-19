@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body" id="photogalleries-form">

                {!! Form::model( $photogallery, ['route' => ['admin.photogalleries.update', $photogallery->id], 'method' => 'PATCH', 'id' => 'form_edit_permission', 'files' => true]) !!}

                @include('admin.photogalleries._form')

                <div class="form-group mt15">
                    {!! Form::button('Upravit', ['class' => 'btn bg-teal-400', 'id' => 'btn-submit-edit', 'type' => 'submit'] ) !!}
                    <a href="{{ URL::previous() }}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection