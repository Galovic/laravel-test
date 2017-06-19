@if (Session::has('flash_notification.message'))
    $.jGrowl('{{ Session::get('flash_notification.message') }}', {
        header: '@if(!empty(Session::get('flash_notification.title'))) {{ Session::get('flash_notification.title') }} @elseif("danger" == Session::get('flash_notification.level')) Chyba! @elseif("success" == Session::get('flash_notification.level')) Úspěch @elseif("warning" == Session::get('flash_notification.level')) Varování @elseif("info" == Session::get('flash_notification.level')) Informace @else Informace @endif',
        theme: '@if("danger" == Session::get('flash_notification.level')) bg-danger @elseif("success" == Session::get('flash_notification.level')) bg-teal @elseif("warning" == Session::get('flash_notification.level')) bg-warning @elseif("info" == Session::get('flash_notification.level')) bg-info @else bg-slate-400 @endif alert-styled-left alert-styled-custom-{{ Session::get('flash_notification.level') }}'
    });
@endif