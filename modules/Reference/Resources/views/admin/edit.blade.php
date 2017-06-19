@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">
                {!! Form::model( $career, ['route' => ['admin.module.career.update', $career->id], 'id' => 'career-form', 'method' => 'PATCH', 'files' => true ]) !!}

                @include('module-careerplugin::admin._form')

                <div class="form-group mt15">
                    {!! Form::button('Upravit', ['class' => 'btn bg-teal-400', 'id' => 'btn-submit-edit', 'type' => 'submit'] ) !!}
                    <a href="{!! route('admin.module.career.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection