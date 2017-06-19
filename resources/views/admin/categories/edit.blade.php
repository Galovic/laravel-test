@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::model( $category, ['route' => ['admin.categories.update', $category->id], 'method' => 'PATCH', 'files' => true]) !!}

                @include('admin.categories._form')

                <div class="form-group mt15">
                    {!! Form::button('Upravit', [
                        'class' => 'btn bg-teal-400',
                        'id' => 'btn-submit-edit',
                        'type' => 'submit'
                    ]) !!}
                    <a href="{!! route('admin.categories.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection