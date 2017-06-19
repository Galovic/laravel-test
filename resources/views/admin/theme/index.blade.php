@extends('admin.layouts.master')

@section('content')
<div class='row'>
    <div class="panel panel-default">
        <div class="panel-body">
            <form class="form-horizontal" id="theme-config-form">
                <div class="form-group">
                    <label class="col-xs-4 control-label text-right">Šablona:</label>
                    <div class="col-xs-7" style="padding-top: 8px">
                        <strong>{{ $defaultTheme->name }}</strong>
                         - <a href="#" data-toggle="modal" data-target="#themesModal">Změnit</a>
                    </div>
                </div>

                @if(View::exists('theme::config.form'))
                    @include('theme::config.form')
                @endif

            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="themesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Změna šablony</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Šablona</th>
                            <th class="text-right">Aktivace</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($themes as $theme)
                    <tr>
                        <td>{{ $theme->name }}</td>
                        <td class="text-right">
                            @if($theme->id == $defaultTheme->id)
                                <span class="text-muted">Aktivní</span>
                            @else
                                <a href="{{ route('admin.theme.switch', $theme->id) }}" class="automatic-post">Aktivovat</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function ($) {
        $('#theme-config-form').find(':input').on('change', function(e){
            var $input = $(this);
            $.ajax({
                url: "{{ route('admin.theme.config') }}",
                method: 'POST',
                data: {
                    key: $input.attr('name'),
                    value: $input.val()
                }
            }).done(function(response){
                if(response.error){
                    alert('Vyskytla se chyba. Obnovte prosím stránku.');
                }
            }).fail(function(){
                alert('Vyskytla se chyba. Obnovte prosím stránku.');
            });
        });
    })(jQuery)
</script>
@endpush