<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('admin.title') }} {{ $pageTitle or '' }}</title>

    {!! Html::style( url('/') . elixir('css/admin.css') ) !!}

    @stack('style')

</head>

<body>

@include('admin.vendor._main_navbar')

<div class="page-container">

    <div class="page-content">

        <div class="sidebar sidebar-main">
            <div class="sidebar-content">

                <!-- User menu -->
                <div class="sidebar-user">
                    <div class="category-content">
                        <div class="media">
                            <span class="media-left">
                                <img src="{!! auth()->user()->image_url !!}" class="img-circle img-sm" alt="">
                            </span>
                            <div class="media-body">
                                <span class="media-heading text-semibold login-name">{{ Auth::user()->name }}</span>
                            </div>

                            <div class="media-right media-middle">
                                <ul class="icons-list">
                                    <li>
                                        <a href="{{ route('admin.auth.logout') }}" class="automatic-post">
                                            <i class="fa fa-power-off"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /user menu -->

                @include('admin.vendor._menu')

            </div>
        </div>
        <!-- /main sidebar -->


        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Page header -->
            <div class="page-header">
                <div class="page-header-content">
                    <div class="page-title">
                        <h4>
                            <i class="fa fa-arrow-circle-o-left"></i>
                            <span class="text-semibold">{{ $pageTitle or "Page Title" }}</span>
                            - {{ $pageDescription or "Page description" }}
                        </h4>
                    </div>
                </div>

                <div class="breadcrumb-line">
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('admin') }}">
                                <i class="fa fa-home"></i> Admin
                            </a>
                        </li>
                        <li class="active">{{ $pageTitle or "Page Title" }}</li>
                    </ul>

                    <ul class="breadcrumb-elements">
                        @yield('breadcrumb-elements')
                    </ul>
                </div>
            </div>

            <div class="content">

                @yield('content')

                <div class="footer text-muted">
                    {!! config('admin.copyright') !!}
                </div>
            </div>
        </div>
    </div>
</div>

{!! Html::script( url('/') . elixir('js/admin.js') ) !!}
@include('admin.vendor.js-config')

@stack('script')
<script>
    jQuery(document).ready(function () {
        @include('admin.vendor.flash.message')
        @yield('jquery_ready')
    });
</script>

</body>
</html>