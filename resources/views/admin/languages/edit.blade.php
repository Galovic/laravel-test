@extends('admin.layouts.master')

@section('content')

    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::model($language, ['route' => ['admin.languages.update', $language->id], 'files' => false] ) !!}

                @include('admin.languages._form')

                <div class="form-group mt15">
                    {!! Form::button( 'Uložit', ['class' => 'btn bg-teal-400', 'type' => 'submit', 'id' => 'btn-submit-edit'] ) !!}
                    <a href="{!! route('admin.languages.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

@endsection





