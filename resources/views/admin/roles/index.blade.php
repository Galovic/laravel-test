@extends('admin.layouts.master')

@section('breadcrumb-elements')
    <li>
        <a href="{!! route('admin.roles.create') !!}"  title="{{ trans('admin/roles/general.action.create') }}">
            <i class="fa fa-pencil-square-o position-left"></i> {{ trans('admin/roles/general.action.create') }}
        </a>
    </li>
@endsection

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title">Uživatelské role</h3>
            </div>

            <div class="box-body">
                <div class="panel panel-flat">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans('admin/routes/general.columns.enabled') }}</th>
                                <th>{{ trans('admin/roles/general.columns.display_name') }}</th>
                                <th>{{ trans('admin/roles/general.columns.description') }}</th>
{{--                                <th>{{ trans('admin/roles/general.columns.permissions') }}</th>--}}
{{--                                <th>{{ trans('admin/roles/general.columns.users') }}</th>--}}
                                <th>{{ trans('admin/roles/general.columns.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td class="text-center">
                                    @if ( $role->enabled )
                                        <a href="{!! route('admin.roles.toggle', $role->id) !!}" title="{{ trans('general.button.disable') }}">
                                            <i class="fa-2x fa fa-check-circle-o enabled" style="color: green"></i>
                                        </a>
                                    @else
                                        <a href="{!! route('admin.roles.toggle', $role->id) !!}" title="{{ trans('general.button.enable') }}">
                                            <i class="fa-2x fa fa-ban disabled" style="color: red"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->description }}</td>
                                {{--<td></td>--}}
                                {{--<td></td>--}}
                                <td class="text-center">
                                    @if ( $role->isEditable() )
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-bars"></i>
                                                    <i class="fa fa-angle-down"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a href="{{ route('admin.roles.edit', [ $role->id ]) }}" title="{{ trans('general.button.edit') }}">
                                                            <i class="fa fa-pencil-square-o"></i> Upravit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="action-delete" href="{{ route('admin.roles.delete', $role->id) }}">
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
                    {!! $roles->render() !!}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
