<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE"/>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#75C7C3">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#75C7C3">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#75C7C3">

    <title>{{ trans('filemanager.title-page') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('media/images/filemanager/folder.png') }}">

    {!! Html::style( url('/') . elixir('css/admin.css') ) !!}

    {!! Html::style( url('/') . elixir('css/filemanager.css') ) !!}

</head>
<body>
<div class="container-fluid" id="wrapper">

    <div class="row">
        <div class="col-xs-12">
            <nav class="navbar navbar-default">
                <div class="navbar-header">

                    <button type="button"
                            class="navbar-toggle collapsed"
                            data-toggle="collapse"
                            data-target="#nav-buttons"
                    >
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand"
                       @click.prevent="goToParentDir"
                       v-show="!isInRootDir"
                    >
                        <i class="fa fa-arrow-left"></i>
                        <span class="hidden-xs">{{ trans('filemanager.nav-back') }}</span>
                    </a>

                    <a class="navbar-brand visible-xs" href="#">
                        {{ trans('filemanager.title-panel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a class="clickable" @click.prevent="toggleShowList()">
                        <span v-show="showList">
                          <i class="fa fa-th-large"></i>
                          <span>{{ trans('filemanager.nav-thumbnails') }}</span>
                        </span>
                                <span v-show="!showList">
                            <i class="fa fa-list"></i>
                            <span>{{ trans('filemanager.nav-list') }}</span>
                        </span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true"
                               aria-expanded="false">
                                {{ trans('filemanager.nav-sort') }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" @click.prevent="changeSortType('alphabetic')">
                                        <i class="fa fa-sort-alpha-asc"></i> {{ trans('filemanager.nav-sort-alphabetic') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#" @click.prevent="changeSortType('time')">
                                        <i class="fa fa-sort-amount-asc"></i> {{ trans('filemanager.nav-sort-time') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="visible-xs" id="current_dir"
                 style="padding: 5px 15px;background-color: #f8f8f8;color: #5e5e5e;"></div>

            <div id="alerts"></div>

            {{-- GRID VIEW --}}
            <div class="row" v-if="!showList">

                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 img-row" v-for="file in files">

                    <div class="square clickable"
                         :class="{'file-item': file.is_file, 'file-folder': !file.is_file}"
                         @click.prevent="file.is_file ? openFile(file.url) : openDirectory(file.path)"
                    >
                        <img :src="file.thumb" alt="Thumbnail" v-if="file.thumb">
                        <i class="fa fa-5x @{{ file.icon }}" v-if="!file.thumb"></i>
                    </div>

                    <div class="caption text-center">
                        <div class="btn-group">
                            <a href="#" @click.prevent="file.is_file ? openFile(file.url) : openDirectory(file.path)">
                                @{{ file.name }}
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            {{-- LIST VIEW --}}
            <div class="row" v-if="showList">

                <div class="col-xs-12">
                    <table class="table table-responsive table-condensed table-striped hidden-xs">
                        <thead>
                        <tr>
                            <th style='width:50%'>{{ Lang::get('filemanager.title-item') }}</th>
                            <th>{{ Lang::get('filemanager.title-size') }}</th>
                            <th>{{ Lang::get('filemanager.title-type') }}</th>
                            <th>{{ Lang::get('filemanager.title-modified') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="file in files">
                            <td>
                                <i class="fa @{{ file.icon }}"></i>
                                <a :class="{'file-item': file.is_file, 'file-folder': !file.is_file}"
                                   @click.prevent="file.is_file ? openFile(file.url) : openDirectory(file.path)">
                                    @{{ file.name | truncate '20' }}
                                </a>
                            </td>
                            <td>@{{ file.size }}</td>
                            <td>@{{ file.type }}</td>
                            <td>@{{ file.time }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xs-12 text-center" style="display: none;" v-show="!files.length">
            <em>{{ trans('filemanager.message-empty') }}</em>
        </div>

        <a id="upload-button"
           data-tooltip="{{ trans('filemanager.title-upload') }}"
           @click.prevent="showUploadModal"
        >
            <i class="fa fa-upload"></i>
        </a>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aia-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">{{ trans('filemanager.title-upload') }}</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.filemanager.upload', compact('model', 'id')) }}" role='form'
                      id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data'>
                    <div class="form-group" id="attachment">
                        <label for='upload' class='control-label'>{{ trans('filemanager.message-choose') }}</label>
                        <div class="controls">
                            <div class="input-group" style="width: 100%">
                                <input type="file" id="upload" name="upload[]" multiple="multiple">
                            </div>
                        </div>
                    </div>
                    <input type='hidden' name='working_dir' id='working_dir'>
                    <input type='hidden' name='type' id='type' value='{{ request("type") }}'>
                    <input type='hidden' name='_token' value='{{csrf_token()}}'>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('filemanager.btn-close') }}</button>
                <button type="button" class="btn btn-primary" id="upload-btn">{{ trans('filemanager.btn-upload') }}</button>
            </div>
        </div>
    </div>
</div>

{!! Html::script( url('/') . elixir('js/admin.js') ) !!}

<script>
    var filemanagerConfig = {
        urls: {
            errors: '{{ route("admin.filemanager.getErrors") }}',
            items: '{{ route("admin.filemanager.items", compact('model', 'id')) }}'
        },
        lang: {!! json_encode(trans('lfm')) !!}
    };
</script>

{!! Html::script( url('/') . elixir('js/filemanager.js') ) !!}

</body>
</html>
