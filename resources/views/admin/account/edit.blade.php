@extends('admin.layouts.master')

@section('content')
    <div class="row" id="page-account-edit">
        <div class="col-md-6">

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">Základní údaje</h5>
                </div>

                <div class="panel-body">

                    @include('admin.account._account_form')

                </div>
            </div>

        </div>

        <div class="col-md-6">

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">Změna hesla</h5>
                </div>

                <div class="panel-body">

                    @include('admin.account._password_change_form')

                </div>
            </div>

        </div>

    </div>
@endsection

@push('script')
{!! Html::script( url('js/bootstrap-maxlength.js') ) !!}

<script>
    var accountFormData = {
        defaultThumbnailSrc: "{!! Gravatar::get(auth()->user()->email) !!}",
        imageSrc: "{!! auth()->user()->image_url !!}",
        imageSelected: {!! auth()->user()->hasCustomImage() ? 'true' : 'false' !!},
    };
</script>

{!! Html::script( elixir('js/account.page.js') ) !!}

@endpush