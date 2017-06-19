{{ Form::model($configuration, [
    'id' => 'mt-configuration-form'
]) }}

    <div class="form-group ">
        {{ Form::label($id = 'mt-content-input', 'Obsah') }}
        {{ Form::textarea('content', null, [
            'class' => 'form-control',
            'id' => $id
        ]) }}
    </div>

{{ Form::close() }}

{!! Html::script( url("plugins/ckeditor/ckeditor.js") ) !!}
<script>
    (function ($) {
        var editor = CKEDITOR.replace( 'mt-content-input',{
            filebrowserImageBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'module-text',
                'id' => $configuration->id ?: 0,
                'type' => 'Images'
            ]) }}',
            filebrowserImageUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Images',
                'model' => 'module-text',
                'id' => $configuration->id ?: 0
            ]) }}',
            filebrowserBrowseUrl: '{{ route('admin.filemanager.show', [
                'model' => 'module-text',
                'id' => $configuration->id ?: 0,
                'type' => 'Files'
            ]) }}',
            filebrowserUploadUrl: '{{ route('admin.filemanager.upload', [
                'type' => 'Files',
                'model' => 'module-text',
                'id' => $configuration->id ?: 0
            ]) }}',
            removeDialogTabs: 'link:upload;image:upload',
            height: '400px'
        });

        editor.on( 'change', function( evt ) {
            $('#mt-content-input').val(evt.editor.getData());
        });
    })(jQuery);

</script>