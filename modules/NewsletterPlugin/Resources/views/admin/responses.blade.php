@extends('admin.layouts.master')

@section('content')
    @foreach ($responses as $response)
    <div class="panel panel-flat">
        <div class="panel-body">
            <em class="text-muted">Přijato: {{ $response->created_at->format('j.n.Y H:i:s') }}</em><br/>
            @foreach ($response->getNamedValues() as $field)
                <strong>{{ $field->name }}:</strong> {{ $field->value }} <br />
            @endforeach

            @foreach ($response->getFiles() as $file)
                <a href="{{ route('admin.module.forms_plugin.download-file', [ $response->id, $file->id ]) }}">{{ $file->name }}</a> <br />
            @endforeach
        </div>
    </div>
    @endforeach

    {{ $responses->links() }}
@endsection

