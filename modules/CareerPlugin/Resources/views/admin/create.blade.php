@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::open( ['route' => 'admin.module.career.store', 'id' => 'career-form', 'files' => true] ) !!}

                @include('module-careerplugin::admin._form')

                <div class="form-group mt15">
                    {!! Form::button( 'Vytvořit', ['class' => 'btn bg-teal-400', 'type' => 'submit', 'id' => 'btn-submit-edit'] ) !!}
                    <a href="{!! route('admin.module.career.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection