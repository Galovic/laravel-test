@extends('admin.layouts.master')

@section('content')


    <!-- Default ordering -->
    <div class="panel panel-flat">
        <table class="table datatable-sorting">
            <thead>
            <tr>
                <th>Název modulu</th>
                <th width="180">Povolit</th>
                <th width="170">Instalace</th>
            </tr>
            </thead>
            <tbody>
            @foreach($modules as $module)

            <tr>
                <td>{{ $module->name }}</td>
                <td>
                    @if ($module->installation)
                        <a href="{{ route('admin.modules.toggle', $module->installation->id) }}" class="automatic-post">{!! $module->installation->enabled ? '<span class="label label-success">Povolen</span>' : '<span class="label label-danger">Zakázán</span>' !!}</a>
                    @else
                        <span class="label label-default">Neaktivní</span>
                    @endif
                </td>
                <td>
                    @if ($module->installation)
                        <a href="{{ route('admin.modules.uninstall', $module->installation->id) }}" class="action-uninstall" id="{{$module->installation->id}}"><span class="label label-danger">Odinstalovat</span></a>
                    @else
                        <a href="{{ route('admin.modules.install', $module->name) }}" class="automatic-post"><span class="label label-success">Nainstalovat</span></a>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection

@push('script')
<script>
    (function($){
        $('.action-uninstall').click(function(event){
            event.preventDefault();
            var $target = $(this);

            swal(
                {
                    title: "Odinstalace modulu",
                    text: "Opravdu si přejete tento modul odinstalovat? Budou zároveň nenávratně odstraněna všechna jeho data a použití.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF5350",
                    confirmButtonText: "Ano, odinstalovat!",
                    cancelButtonText: window.trans.confirmDelete.cancel,
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if(!isConfirm) return;

                    $target.lock();
                    $.ajax({
                        url: $target.attr('href'),
                        type: 'POST',
                        dataType: 'json'
                    }).done(function (response) {
                        if(response.refresh) {
                            location.reload();
                        } else if(response.error){
                            $.jGrowl(response.error, {
                                header: ' Chyba! ',
                                theme: ' bg-danger  alert-styled-left alert-styled-custom-danger'
                            });
                        }
                    }).always(function () {
                        $target.unlock();
                    });
                }
            );
        });
    })(jQuery);
</script>
@endpush