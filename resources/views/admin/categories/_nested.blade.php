<option value="{{ $item->id }}" {{ isset($category) && $category->parent_id == $item->id? "selected":"" }}>

    @if($item->depth > 0)
        @for($i = 0; $i < $item->depth; $i++)
            &nbsp;
        @endfor
        -
    @endif {{ $item->name }}


</option>

@if (count($item->children) > 0)


    @foreach($item->children as $item)
        @include('admin.categories._nested', $item)
    @endforeach

@endif


