@extends('layouts.error')

@section('content')
<body class="notfound">

    <section>
        <div class="notfoundpanel">
            <h1>404!</h1>
            <h3>Hledaná stránka <span>nebyla nalezena!</span></h3>
            <h4>Prosím zkontrolujte, jestli jste zadali správnou adresu.</h4>
            <a href="{!! url('') !!}" class="btn-back">Vrátit se na úvodní stranu</a>
        </div>
    </section>

</body>
@stop
