<!DOCTYPE html>
<html lang="{{ $data->language->language_code }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $data->description }}">
    <meta name="keywords" content="{{ $data->keywords }}">
    <meta name="author" content="SIMPLO s.r.o.">
    {{--<link rel="icon" href="../../favicon.ico">--}}

    <title>{{ $data->title }}</title>
    {{ Html::style( $context->elixir('css/style.css') ) }}

    {{-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries --}}
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>

<header>
    <!-- TOP-BAR -->
    <div id="top-bar">
        <div class="container">
            <div class="row">
                <div class="hidden-xs col-sm-8">
                    <ul class="list-unstyled list-inline topbar-contact-info">
                        <li><span class="text-icon telephone">+420 111 222 333</span></li>
                        <li><a href="#"><span class="text-icon e-mail">vas@email.com</span></a></li>
                    </ul>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="dropdown pull-right lang-toggle">
                        <a class="dropdown-toggle text-icon cs" id="languageMenu" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="true">
                            <span class="arrow to-right show-down">Česky</span>
                        </a>
                        <ul class="dropdown-menu list-unstyled" aria-labelledby="languageMenu">
                            <li><a href="#"><span class="text-icon en">English</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /TOP-BAR -->

    <!-- HOMEPAGE HEADER -->
    <div id="hp-header">
        <!-- NAVBAR -->
    @include('theme::menus.primary')
    <!-- /NAVBAR -->

        <!-- CAROUSEL -->
        <div id="hp-carousel" class="carousel slide">
            <div class="carousel-inner">
                <div class="item active" style="background-image:url({{ $context->media('images/slide-1.jpg') }});">
                </div>
                <div class="item" style="background-image:url({{ $context->media('images/slide-2.jpg') }});">
                </div>
                <div class="item" style="background-image:url({{ $context->media('images/slide-3.jpg') }});">
                </div>
            </div>
            <!-- Controls -->
            <a class="left carousel-control" href="#hp-carousel" data-slide="prev">
                <span class="icon-prev"></span>
            </a>
            <a class="right carousel-control" href="#hp-carousel" data-slide="next">
                <span class="icon-next"></span>
            </a>
        </div>
        <!-- /CAROUSEL -->
    </div>
    <!-- /HOMEPAGE HEADER -->
</header>

@yield('content')

<!-- FOOTER -->
<footer>
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque pretium lectus id turpis.
                    Nullam justo enim, consectetuer nec, ullamcorper ac, vestibulum in, elit. Duis viverra diam non justo.</p>
                </div>
                <div class="hidden-xs col-sm-4">
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque pretium lectus id turpis.
                    Nullam justo enim, consectetuer nec, ullamcorper ac, vestibulum in, elit. Duis viverra diam non justo.</p>
                </div>
                <div class="hidden-xs col-sm-4">
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque pretium lectus id turpis.
                    Nullam justo enim, consectetuer nec, ullamcorper ac, vestibulum in, elit. Duis viverra diam non justo.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom">
            <div class="row">
                <div class="col-md-12 text-right">
                    <p>&copy; Simplo.cz</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- /FOOTER -->

{{ Html::script( $context->elixir('js/app.js') ) }}

@stack('scripts')

@include('theme::vendor.photoswipe')

</body>
</html>