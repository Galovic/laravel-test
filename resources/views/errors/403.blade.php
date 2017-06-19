@extends('layouts.error')

@section('content')
<body class="notfound">

    <section>
        <div class="notfoundpanel">
            <h1>403!</h1>
            <h3>Nemáte dostatečná <span>oprávnění!</span></h3>
            <h4>Prosím kontaktujte, administrátora.</h4>
            <a href="{!! url('') !!}" class="btn-back">Vrátit se na úvodní stranu</a>
        </div>
    </section>

</body>
@stop
