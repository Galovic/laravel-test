@extends('admin.layouts.master')

@section('content')
    <div class="panel panel-flat">
        <table class="table" id="categories-table">
            <thead>
            <tr>
                <th>Název kategorie</th>
                <th width="120">Status</th>
                <th width="180">Vytvořeno</th>
                <th width="180">Autor</th>
                <th width="80" class="text-center">Akce</th>
            </tr>
            </thead>
            <tbody>


            @foreach($categories as $item)
                @include('admin.categories._row', [$item])
            @endforeach


            </tbody>
        </table>
    </div>

    @permission(('article-categories-create'))
    <a href="{{ route('admin.categories.create') }}" class="btn bg-teal-400 btn-labeled">
        <b class="fa fa-pencil-square-o"></b> Vytvořit kategorii
    </a>
    @endpermission

@endsection

@push('script')
    {!! Html::script( url('js/datatables.js') ) !!}
    <script>
        (function($){
            $('#categories-table').DataTable({
                order: [0, "asc"]
            });
        })(jQuery);
    </script>
@endpush

@section('breadcrumb-elements')
    @permission(('article-categories-create'))
    <li>
        <a href="{{ route('admin.categories.create') }}">
            <i class="fa fa-pencil-square-o position-left"></i> Vytvořit kategorii
        </a>
    </li>
    @endpermission
@endsection