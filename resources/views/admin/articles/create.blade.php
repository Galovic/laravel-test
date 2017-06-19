@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body" id="articles-form">

                {!! Form::open( [
                    'route' => 'admin.articles.store',
                    'id' => 'form_edit_user',
                    'files' => true
                ]) !!}

                @include('admin.articles._form')

                <div class="form-group mt15">
                    {!! Form::button( 'Vytvořit', [
                        'class' => 'btn bg-teal-400',
                        'type' => 'submit'
                    ]) !!}

                    <a href="{{ URL::previous() }}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection