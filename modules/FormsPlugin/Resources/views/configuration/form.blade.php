{{ Form::model($configuration, [ 'id' => 'module-forms-configuration-form' ]) }}

<div class="form-group">
    {{ Form::label($id = 'mf-form-input', 'Formulář') }}
    {{ Form::select('form_id', $forms, null, [
        'class' => 'form-control',
        'id' => $id,
    ]) }}
</div>

<div class="form-group">
    {{ Form::label($id = 'mf-view-input', 'View') }}
    {{ Form::select('view', $views, null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
</div>

{{ Form::close() }}