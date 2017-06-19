<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">Views modul {{$module_id}}</h4>
</div>

<div class="modal-body">
    <form id="form-{{$module_id}}">
    <div class="tabbable tab-content-bordered">
        <label>Název view</label>
        <input type="text" name="modul-view" value="{!! $module['modul-view'] or "" !!}">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="save-{{$module_id}}">Save changes</button>
    </div>
    </form>
</div>

<script>

    $('#form-{{$module_id}}').submit(function (e) {

        e.preventDefault();

        var $this = $(this);


        function getValForm(name) {

            return $this.find("[name='" + name + "']").val();
        }

        var json = JSON.stringify(
                {
                    "modul-view": getValForm('modul-view'),

                }
        );

        $.ajax({
            url: '/admin/ajax-grideditor/module/text/save/{{$module_id}}',
            dataType: 'text',
            type: 'post',
            contentType: 'application/json',
            data: json,
            success: function (data, textStatus, jQxhr) {
                console.log('success');
                $('#modules-edit-modal').modal('hide');

                // $("#live-preview-{{$module_id}}").text(editor_data);


            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });


    });


</script>


