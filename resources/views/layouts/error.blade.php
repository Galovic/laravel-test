<!DOCTYPE html>
<html lang="cz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{!! url('favicon.ico') !!}">

    <title>Chyba!</title>

    {!! Html::style( elixir('css/errors.css')) !!}

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    {!! Html::script('assets/js/html5shiv.js') !!}
    {!! Html::script('assets/js/respond.min.js') !!}
    <![endif]-->
</head>

@yield('content')

</html>