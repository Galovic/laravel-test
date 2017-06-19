@extends('theme::layouts.main')

@section('content')

    <!-- BREADCRUMB -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="#">{{ $title }}</a></li>
                </ol>
            </div>
        </div>
    </div>
    <!-- /BREADCRUMB -->

    <!-- CONTAIN -->
    <div class="container">
        <div class="row">

            <h2 class="text-center">Články</h2>

            <div class="list-group">
                @foreach($articles as $article)
                    <a href="{{ $article->full_url }}" class="list-group-item">
                        <img src="{{ $article->thumbnail_url }}" width="100">
                        <div style="display: inline-block;">
                            <strong>{{ $article->title }}</strong>
                            <p>
                                {{ $article->perex }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            @if(!$articles->count())
                <h4> <em>{{ $context->trans('articles.no_articles') }}</em> </h4>
            @endif

        </div>
    </div>

@endsection