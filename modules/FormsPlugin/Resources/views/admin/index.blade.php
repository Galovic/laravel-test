@extends('admin.layouts.master')

@section('content')
    <!-- Default ordering -->
    <div class="panel panel-flat">
        <table class="table datatable-sorting">
            <thead>
            <tr>
                <th>Název formuláře</th>
                <th width="180">Položek</th>
                <th width="170">Autor</th>
                <th width="80" class="text-center">Akce</th>
            </tr>
            </thead>
            <tbody>
            @foreach($forms as $form)

            <tr>
                <td>{{ $form->name }}</td>
                <td>{{ $form->languageFields($currentLanguage)->count() }}</td>
                <td>{{ $form->user ? $form->user->name : '--' }}</td>
                <td class="text-center">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{ route('admin.module.forms_plugin.responses', [ $form->id ]) }}">
                                        <i class="fa fa-eye"></i> Odpovědi
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.module.forms_plugin.edit', [ $form->id ]) }}">
                                        <i class="fa fa-pencil-square-o"></i> Upravit
                                    </a>
                                </li>
                                <li>
                                    <a class="action-delete" href="{{ route('admin.module.forms_plugin.delete', $form->id) }}">
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

    <a href="{{ route('admin.module.forms_plugin.create') }}" class="btn bg-teal-400 btn-labeled"><b><i class="fa fa-pencil-square-o"></i></b> Vytvořit formulář</a>
    <!-- /default ordering -->
@endsection
@push('script')
    {!! Html::script( url('js/datatables.js') ) !!}
@endpush

@section('breadcrumb-elements')
    <li><a href="{{ route('admin.module.forms_plugin.create') }}"><i class="fa fa-pencil-square-o position-left"></i> Vytvořit formulář</a></li>
@endsection

@section('jquery_ready')
    $('.datatable-sorting').DataTable({
        order: [1, "desc"]
    });
@endsection

