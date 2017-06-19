<tr>
    <td><div style="padding-left: {{ $item['depth']*10 }}px">@if($item->depth > 0) - @endif {{ $item->name }}</div></td>


    <td>{!! ($item->show && $item->show == 1)? '<span class="label label-success">Publikováno</span>' : '<span class="label label-danger">Nepublikováno</span>' !!}</td>

    <td>{{ (!$item->created_at || $item->created_at == '0000-00-00 00:00:00') ? '-' : $item->created_at->format('j.n.Y, H:i') }}</td>
    <td>{{ $item->last_name  }}</td>


    <td class="text-center">
        <ul class="icons-list">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    @permission(('article-categories-edit'))
                    <li>
                        <a href="{{ route('admin.categories.edit', [$item->id]) }}">
                            <i class="fa fa-pencil-square-o"></i> Upravit
                        </a>
                    </li>
                    @endpermission
                    @permission(('article-categories-delete'))
                    <li>
                        <a class="action-delete" href="{{ route('admin.categories.delete', $item->id) }}">
                            <i class="fa fa-trash"></i> Smazat
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>
        </ul>
    </td>


</tr>

@if (count($item->children) > 0)
    <tr>
        @foreach($item->children as $item)
            @include('admin.categories._row', [ $item ])
        @endforeach
    </tr>
@endif
