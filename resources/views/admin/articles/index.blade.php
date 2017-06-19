@extends('admin.layouts.master')

@section('content')


    <!-- Default ordering -->
    <div class="panel panel-flat">
        <table class="table datatable-sorting" id="articles-table">
            <thead>
            <tr>
                <th>Název článku</th>
                <th width="180">Publikovat od</th>
                <th width="170">Autor</th>
                <th width="130">Status</th>
                <th width="80" class="text-center">Akce</th>
            </tr>
            </thead>
            <tbody>
            @foreach($articles as $article)

            <tr>
                <td>{{ $article->title }}</td>
                <td>{{ $article->publish_at->format('j.n.Y, H:i') }}</td>
                <td>{{ $article->user->name }}</td>
                <td>
                    @if($article->status && $article->publish_at->lt(\Carbon\Carbon::now()))
                        <span class="label label-success">Publikováno</span>
                    @else
                        <span class="label label-danger">Nepublikováno</span>
                    @endif
                </td>
                <td class="text-center">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{ route('admin.articles.edit', [ $article->id ]) }}">
                                        <i class="fa fa-pencil-square-o"></i> Upravit
                                    </a>
                                </li>
                                <li>
                                    <a class="action-delete" href="{{ route('admin.articles.delete', $article->id) }}">
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

    <a href="{{ route('admin.articles.create') }}" class="btn bg-teal-400 btn-labeled">
        <b class="fa fa-pencil-square-o"></b> Vytvořit článek
    </a>

@endsection

@push('script')
{!! Html::script( url('js/datatables.js') ) !!}
<script>
    (function($){
        $('#articles-table').DataTable({
            order: [1, "desc"]
        });
    })(jQuery);
</script>
@endpush

@section('breadcrumb-elements')
    <li>
        <a href="{{ route('admin.articles.create') }}">
            <i class="fa fa-pencil-square-o"></i> Vytvořit článek
        </a>
    </li>
@endsection

