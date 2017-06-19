@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::model( $page, ['route' => ['admin.pages.update', $page->id], 'id' => 'form_edit_pages', 'files' => true]) !!}

                @include('admin.pages._form')

                <div class="form-group mt15">
                    {!! Form::button('Uložit a dokončit', [
                        'class' => 'btn bg-teal-400',
                        'id' => 'pages-form-submit',
                        'type' => 'submit'
                    ]) !!}
                    {!! Form::button('Uložit', [
                        'class' => 'btn bg-teal-400',
                        'id' => 'pages-form-save',
                        'type' => 'submit'
                    ]) !!}
                    <a href="{!! route('admin.pages.index') !!}" title="Zrušit" class='btn btn-default'>Zrušit</a>
                </div>

                {!! Form::close() !!}

            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection