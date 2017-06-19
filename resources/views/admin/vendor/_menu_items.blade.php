@foreach($items as $item)
    <li @if (isset($item->attributes['class']) || $item->hasChildren()) class="
            @if(isset($item->attributes['class'])){{ $item->attributes['class'] }}
            @endif
            @if($item->hasChildren()){{ 'treeview' }}
            @endif"
        @endif>
        <a href="{!! $item->url('') !!}" >
        @if (isset($item->attributes['icon'])) <i class="{{ $item->attributes['icon'] }}"></i>
        @endif <span>{!! $item->title !!}</span></a>
        @if($item->hasChildren())
            <ul>
                @include('admin.vendor._menu_items', array('items' => $item->children()))
            </ul>
        @endif
    </li>
@endforeach