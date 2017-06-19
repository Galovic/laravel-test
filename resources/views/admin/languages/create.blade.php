@extends('admin.layouts.master')

@section('content')

    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::model($language, ['route' => 'admin.languages.store', 'id' => 'form_edit_languages', 'files' => false] ) !!}

                @include('admin.languages._form')

                <div class="form-group mt15">
                    {!! Form::button( 'Vytvořit', ['class' => 'btn bg-teal-400', 'type' => 'submit', 'id' => 'btn-submit-edit'] ) !!}
                    <a href="{!! route('admin.languages.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div>
        </div>

    </div>

@endsection


@section('head_js')
    {!! Html::script( asset("/assets/admin/js/plugins/tables/datatables/datatables.min.js") ) !!}
    {!! Html::script( asset("/assets/admin/js/plugins/forms/selects/select2.min.js") ) !!}
    {!! Html::script( asset("/assets/admin/js/pages/datatables_sorting.js") ) !!}
@endsection





