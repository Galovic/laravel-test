@extends('admin.layouts.master')

@section('content')
    <div class="panel panel-flat">
        <table class="table datatable-sorting">
            <thead>
            <tr>
                <th>Email</th>
                <th width="180">Vytvořeno</th>
                <th width="80" class="text-center">Akce</th>
            </tr>
            </thead>
            <tbody>
            @foreach($newsletters as $newsletter)

            <tr>
                <td>{{ $newsletter->email }}</td>
                <td>{{ $newsletter->created_at->format('j.n.Y H:i:s') }}</td>
                <td class="text-center">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{ route('admin.module.newsletter_plugin.edit', $newsletter->id) }}">
                                        <i class="fa fa-pencil-square-o"></i> Upravit
                                    </a>
                                </li>
                                <li>
                                    <a class="action-delete" href="{{ route('admin.module.newsletter_plugin.delete', $newsletter->id) }}">
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

    <a href="{{ route('admin.module.newsletter_plugin.export') }}" class="btn bg-teal-400 btn-labeled"><b><i class="fa fa-download"></i></b> Export</a>
@endsection
@push('script')
    {!! Html::script( url('js/datatables.js') ) !!}
@endpush

@section('breadcrumb-elements')
    <li><a href="{{ route('admin.module.newsletter_plugin.export') }}"><i class="fa fa-download position-left"></i> Export</a></li>
@endsection

@section('jquery_ready')
    $('.datatable-sorting').DataTable({
        order: [1, "desc"]
    });
@endsection

