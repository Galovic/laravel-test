@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::open(['route' => 'admin.pages.store', 'id' => 'form_edit_pages', 'files' => true] ) !!}

                @include('admin.pages._form')

                <div class="form-group mt15">
                    {!! Form::button( 'Vytvořit', [
                        'class' => 'btn bg-teal-400',
                        'type' => 'submit',
                        'id' => 'pages-form-submit'
                    ]) !!}
                    <a href="{!! route('admin.pages.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection