@extends('admin.layouts.master')

@section('content')
    <div class='row'>

        @if (!$authorizeLink)
            @include('admin.dashboard._graphs')
        @else
            <div class="col-xs-12 text-center">
                <a class="btn btn-primary" href="{!! $authorizeLink !!}">
                    Autorizovat Google Analytics
                </a>
            </div>
        @endif

    </div>
@endsection