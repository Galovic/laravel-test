{{ Form::model($configuration, [ 'id' => 'model-link-configuration-form' ]) }}

<div class="form-group">
    {{ Form::label($id = 'mcp-text-input', 'Počet zobrazených příležitostí') }}
    {{ Form::number('show_number', null, [
        'class' => 'form-control',
        'id' => $id,
        'min' => 0
    ]) }}
</div>

<div class="form-group">
    {{ Form::label($id = 'mcp-view-input', 'View') }}
    {{ Form::select('view', $views, null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
</div>

{{ Form::close() }}