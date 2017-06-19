@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-flat">
            <table class="table ">
                <thead>
                <tr>
                    <th width="130">Povoleno</th>
                    <th width="130">Obrázek</th>
                    <th width="180">Jazyk</th>
                    <th width="180">Locale</th>
                    <th>Výchozí</th>
                    <th>Akce</th>
                </tr>
                </thead>
                <tbody>

                @foreach($languages as $language)
                    <tr>
                        <td>
                            <a href="{{ route('admin.languages.toggle', $language->id) }}" class="automatic-post" id="{{$language->id}}">{!! $language->enabled ? '<span class="label label-success">Povoleno</span>' : '<span class="label label-danger">Zakázáno</span>' !!}</a>
                        </td>
                        <td>
                            <img class="img-responsive" src="/media/images/flags/{{ $language->country_code }}.png" alt="{{$language->name}}">
                        </td>
                        <td>{{ $language->name }}</td>
                        <td>{{ $language->language_code }}</td>
                        <td>
                            @if($language->default)
                                <span class="label label-info">Výchozí</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bars"></i>
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="{{ route('admin.languages.edit', [ $language->id ]) }}" title="Upravit">
                                                <i class="fa fa-pencil-square-o"></i> Upravit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="action-delete" href="{{ route('admin.languages.delete', $language->id) }}">
                                                <i class="fa fa-trash"></i> Smazat
                                            </a>
                                        </li>
                                        @if($language->enabled)
                                            <li>
                                                <a href="{{ route('admin.languages.default', $language->id) }}" class="automatic-post">
                                                    <i class="fa fa-bullseye"></i> Nastavit vychozí
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            </ul>
                        </td>

                    </tr>
                @endforeach


                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-6" >
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h2 class="panel-title">Nastavení</h2>
                <p>Pokud je více jazyků používat:</p>

                {!! Form::open(['route'=>'admin.languages.settings', 'id' => 'language-settings-form', 'class' => 'automatic-post']) !!}
                    <div class="radio">
                        <label>
                            {!! Form::radio('language_display', $v = config('admin.language_url.directory'), $languageDisplay === $v, ['type'=>'radio', 'class'=>'styled']) !!}
                            Jazyk nastavený podle adresáře. <kbd>{{ $_SERVER['SERVER_NAME'] }}/cs</kbd> / <kbd>{{ $_SERVER['SERVER_NAME'] }}/en</kbd>
                        </label>
                    </div>

                    <div class="checkbox" {!! $languageDisplay === $v ? '' : 'style="display: none"' !!} id="default-language-input">
                        <label>
                            {!! Form::checkbox('default_language_hidden', 1, $defaultLanguageHidden,['type'=>'checkbox','class'=>'styled']) !!}
                            <input type="checkbox" class="styled">
                            Nezobrazovat výchozí jazyk v adrese.
                        </label>
                    </div>

                    <div class="radio">
                        <label>
                            {!! Form::radio('language_display', $v = config('admin.language_url.subdomain'), $languageDisplay === $v, ['type'=>'radio', 'class'=>'styled']) !!}
                            Jazyk nastavený podle subdomény. <kbd>cs.mujweb.cz</kbd> / <kbd>en.mujweb.cz</kbd>
                        </label>
                    </div>

                    <div class="radio">
                        <label>
                            {!! Form::radio('language_display', $v = config('admin.language_url.domain'), $languageDisplay === $v, ['type'=>'radio', 'class'=>'styled']) !!}
                            Jazyk nastavený podle jiné domény <kbd>mujweb.cz</kbd> / <kbd>myweb.com</kbd>
                        </label>
                    </div>

                    {!! Form::button('Uložit',['class'=>'btn bg-teal-400','type'=>'submit', 'id'=>'btn-submit-edit']) !!}

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.languages.create') }}" class="btn bg-teal-400 btn-labeled">
    <b><i class="fa fa-pencil-square-o"></i></b> Přidat jazyk
</a>
@endsection

@push('script')
    <script>
        (function ($) {
            $(".styled").uniform({
                radioClass: 'choice'
            }).on('change', function () {
                if (this.type !== 'radio') {
                    return;
                }

                $('#default-language-input').toggle(this.value === '{{ config('admin.language_url.directory') }}' && this.checked);
            });
        }(jQuery))
    </script>
@endpush