<div class="navbar navbar-inverse">

    <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('admin') }}">
            <img src="{{ asset("media/admin/images/logo_light.png") }}" alt="">
        </a>

        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li>
                <a data-toggle="collapse" data-target="#navbar-mobile">
                    <i class="icon-tree5"></i>
                </a>
            </li>
            <li>
                <a class="sidebar-mobile-main-toggle">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav">
            <li>
                <a class="sidebar-control sidebar-main-toggle hidden-xs">
                    <i class="fa fa-align-justify"></i>
                </a>
            </li>
            <li>
                <a class="btn btn-link" href="{{ url('') }}" target="_blank">
                    <i class="fa fa-home"></i>
                    Přejít na web
                </a>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown language-switch">
                <a class="dropdown-toggle" data-toggle="dropdown">

                    <div class="flag">
                        <img class="img-responsive" src="{{ asset('media/images/flags/' . $currentLanguage->country_code . '.png') }}" alt="{{ $currentLanguage->name }}">
                    </div>
                    <div class="name"> {{ $currentLanguage->name }}</div>
                    <span class="fa fa-angle-down"></span>
                </a>

                <ul class="dropdown-menu">
                    @foreach ($languages as $language)
                        <li>
                            <a href="{{ route('admin.switch', $language->id) }}">
                                <div class="flag">
                                    <img class="img-responsive" src="{{  asset('media/images/flags/' . $language->country_code . '.png') }}" alt="{{ $language->name }}">
                                </div>
                                <div class="name"> {{ $language->name }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <img src="{!! auth()->user()->image_url !!}" alt="">
                    <span>{{ Auth::user()->username }}</span>
                    <i class="fa fa-angle-down"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li class="user-header">
                        <p class="name">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="registered-from">
                            Registrace od {{ Auth::user()->created_at->format('d. m. Y') }}
                        </p>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.account.edit') }}">
                            <i class="fa fa-cog"></i> Nastavení účtu
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.auth.logout') }}" class="automatic-post">
                            <i class="fa fa-power-off"></i> Odhlásit se
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>