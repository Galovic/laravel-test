@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::open( ['route' => ['admin.categories.store'], 'id' => 'form_add_category'] ) !!}

                @include('admin.categories._form')

                <div class="form-group mt15">
                    {!! Form::button( 'Vytvořit', ['class' => 'btn bg-teal-400', 'type' => 'submit', 'id' => 'btn-submit-edit'] ) !!}
                    <a href="{!! route('admin.categories.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection
