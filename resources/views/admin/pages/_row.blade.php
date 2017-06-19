<tr>
    <td>
        <div style="padding-left: {{ $item['depth']*10 }}px">
            {{ $item->depth > 0 ? ' - ' : '' }}{{ $item->name }} {{ $item->homepage ? '(Úvodní strana)':'' }}
        </div>
    </td>
    <td>
        {{ (!$item->published_at || $item->published_at == '0000-00-00 00:00:00') ? '-' : $item->published_at->format('j.n.Y, H:i') }}
    </td>
    <td>
        {!! ($item->published && $item->published_at->lt(\Carbon\Carbon::now())) ? '<span class="label label-success">Publikováno</span>' : '<span class="label label-danger">Nepublikováno</span>' !!}
    </td>
    <td class="text-center">
        <ul class="icons-list">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    @permission(('article-pages-edit'))
                    <li>
                        <a href="{{ route('admin.pages.edit', [$item->id]) }}">
                            <i class="fa fa-pencil-square-o"></i> Upravit
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pages.duplicate', [$item->id]) }}" class="automatic-post">
                            <i class="fa fa-files-o"></i> Duplikovat
                        </a>
                    </li>
                    @endpermission
                    @permission(('article-pages-delete'))
                    <li>
                        <a class="action-delete" href="{{ route('admin.pages.delete', $item->id) }}">
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
            @include('admin.pages._row', $item)
        @endforeach
    </tr>
@endif
