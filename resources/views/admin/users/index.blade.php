@extends('admin.layouts.master')

@section('breadcrumb-elements')
    <li>
        <a href="{!! route('admin.users.create') !!}" title="{{ trans('admin/users/general.button.create') }}">
            <i class="fa fa-pencil-square-o position-left"></i> {{ trans('admin/users/general.button.create') }}
        </a>
    </li>
@endsection

@section('content')

    <div class="panel panel-flat">
        <table class="table datatable-sorting" id="users-table">
            <thead>
            <tr>
                <th width="50">{{ trans('admin/users/general.columns.enabled') }}</th>
                <th>{{ trans('admin/users/general.columns.username') }}</th>
                <th>{{ trans('admin/users/general.columns.name') }}</th>
                <th>{{ trans('admin/users/general.columns.email') }}</th>
                <th width="50">{{ trans('admin/users/general.columns.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)

                <tr>
                    <td class="text-center">
                        @if ( $user->enabled )
                            <a href="{!! route('admin.users.toggle', $user->id) !!}" title="{{ trans('general.button.disable') }}" class="automatic-post">
                                <i class="fa-2x fa fa-check-circle-o enabled" style="color: green"></i>
                            </a>
                        @else
                            <a href="{!! route('admin.users.toggle', $user->id) !!}" title="{{ trans('general.button.enable') }}" class="automatic-post">
                                <i class="fa-2x fa fa-ban disabled" style="color: red"></i>
                            </a>
                        @endif
                    </td>
                    <td>{!! $user->username !!}</td>
                    <td>{!! $user->name !!}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        @if ( !$user->protected )
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bars"></i>
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="{{ route('admin.users.edit', [ $user->id ]) }}" title="{{ trans('general.button.edit') }}">
                                                <i class="fa fa-pencil-square-o"></i> Upravit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="action-delete" href="{{ route('admin.users.delete', $user->id) }}">
                                                <i class="fa fa-trash"></i> Smazat
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        @else
                            <i class="fa fa-lock text-muted"></i>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {!! $users->render() !!}

    </div>

    <a href="{{ route('admin.users.create') }}" class="btn bg-teal-400 btn-labeled">
        <b class="fa fa-pencil-square-o"></b> Vytvořit uživatele
    </a>
@endsection