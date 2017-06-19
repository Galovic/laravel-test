@extends('admin.layouts.master')

@section('content')
    <div class="panel panel-flat">

        <table class="table ">
            <thead>
            <tr>
                <th>Název stránky</th>
                <th width="250">Publikovat od</th>
                <th width="130">Status</th>
                <th width="80" class="text-center">Akce</th>
            </tr>
            </thead>
            <tbody>

            @foreach($pages as $item)
                @include('admin.pages._row', $item)
            @endforeach

            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.pages.create') }}" class="btn bg-teal-400 btn-labeled"><b><i class="fa fa-pencil-square-o"></i></b>
        Vytvořit stránku</a>

@endsection


@section('head_js')
    {!! Html::script( asset("assets/admin/js/plugins/tables/datatables/datatables.min.js") ) !!}
    {!! Html::script( asset("assets/admin/js/plugins/forms/selects/select2.min.js") ) !!}
    {!! Html::script( asset("assets/admin/js/pages/datatables_sorting.js") ) !!}
@endsection

@section('breadcrumb-elements')
    <li><a href="{{ route('admin.pages.create') }}"><i class="fa fa-pencil-square-o position-left"></i> Vytvořit stránku</a></li>
@endsection


@section('jquery_ready')
    $('.datatable-sorting').DataTable({
    order: [1, "desc"]
    });
@endsection

