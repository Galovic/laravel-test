<option value="{{ $page_all->id }}" {{ isset($page) && $page->parent_id == $page_all->id? "selected":"" }}>

    @if($page_all->depth > 0)
        @for($i = 0; $i < $page_all->depth; $i++)
            &nbsp;
        @endfor
        -
    @endif {{ $page_all->name }}


</option>

@if (count($page_all->children) > 0)

@foreach($page_all->children as $page_all)
@include('admin.pages._select_nested', $page_all)
@endforeach

@endif


