{{ Form::model($configuration) }}

<div class="form-group ">
    {{ Form::label($id = 'mp-photogallery-id-input', 'View') }}
    {{ Form::select('photogallery_id', $photogalleries, null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
</div>

<div class="form-group ">
    {{ Form::label($id = 'mp-view-input', 'View') }}
    {{ Form::select('view', $views, null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
</div>

{{ Form::close() }}