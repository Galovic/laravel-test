@if(isset($MainMenu))
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="nav navbar-nav navbar-right">
                @foreach($MainMenu->roots() as $item)
                    <li class="{{ $item->hasChildren() ? 'dropdown' : '' }} {{ $item->isActive ? 'active' : '' }}">
                        @if($item->hasChildren())
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>{!! $item->title !!}</span></a>

                            <ul class="dropdown-menu">
                                @foreach($item->children() as $subitem)
                                    <li>
                                        <a href="{!! $subitem->url() !!}" class="{{ $item->isActive ? 'active' : '' }}">{!! $subitem->title !!} </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a href="{!! $item->url() !!}" class="{{ $item->isActive ? 'active' : '' }}"><span>{!! $item->title !!}</span></a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>
@endif