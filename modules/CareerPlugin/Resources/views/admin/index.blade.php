@extends('admin.layouts.master')

@section('content')
    <!-- Default ordering -->
    <div class="panel panel-flat">
        <table class="table datatable-sorting">
            <thead>
            <tr>
                <th>Název pozice</th>
                {{--<th width="180">Publikovat od</th>--}}
                <th width="170">Autor</th>
                {{--<th width="130">Status</th>--}}
                <th width="80" class="text-center">Akce</th>
            </tr>
            </thead>
            <tbody>
            @foreach($careers as $career)

            <tr>
                <td>{{ $career->title }}</td>
                <td>{{ $career->user->name }}</td>
                <td class="text-center">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{ route('admin.module.career.edit', [ $career->id ]) }}">
                                        <i class="fa fa-pencil-square-o"></i> Upravit
                                    </a>
                                </li>
                                <li>
                                    <a class="action-delete" href="{{ route('admin.module.career.delete', $career->id) }}">
                                        <i class="fa fa-trash"></i> Smazat
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.module.career.create') }}" class="btn bg-teal-400 btn-labeled"><b><i class="fa fa-pencil-square-o"></i></b> Vytvořit pracovní pozici</a>
    <!-- /default ordering -->
@endsection
@push('script')
    {!! Html::script( url('js/datatables.js') ) !!}
@endpush

@section('breadcrumb-elements')
    <li><a href="{{ route('admin.module.career.create') }}"><i class="fa fa-pencil-square-o position-left"></i> Vytvořit pracovní pozici</a></li>
@endsection

@section('jquery_ready')
    $('.datatable-sorting').DataTable({
        order: [1, "desc"]
    });
@endsection

