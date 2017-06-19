<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">Textový modul {{$module_id}}</h4>

</div>


<div class="modal-body">
    <div class="tabbable tab-content-bordered">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="active"><a href="#tab_modal_details" data-toggle="tab" aria-expanded="true">Základní
                    informace</a></li>
            <li class=""><a href="#tab_modal_settings" data-toggle="tab" aria-expanded="true">Základní nastavení</a>
            </li>
        </ul>
        <form id="form-{{$module_id}}">
            <div class="tab-content">

                <div class="tab-pane has-padding active" id="tab_modal_details">


                 <textarea name="editor-full" id="editor-full" rows="4" cols="4">
                    {!! $module['modul-text'] or "" !!}
                </textarea>




                </div><!-- /.tab-pane -->





                <div class="tab-pane has-padding" id="tab_modal_settings">
                   @include('admin.pages.grideditor.modules.partials.basic-settings', $module)




                </div><!-- /.tab-pane -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="save-{{$module_id}}">Save changes</button>
            </div>
        </form>


    </div>


    <script>

        // Full featured editor
        var roxyFileman = '/media/fileman/index.html';
        var editor = CKEDITOR.replace( 'editor-full',{filebrowserBrowseUrl:roxyFileman,
            filebrowserImageBrowseUrl:roxyFileman+'?type=image',
            removeDialogTabs: 'link:upload;image:upload',
            height: '400px',
            extraPlugins: 'youbo'
        });

        $('#form-{{$module_id}}').submit(function (e) {

            e.preventDefault();

            var $this = $(this);

            var editor_data = editor.getData();

            function getValForm(name) {

                return $this.find("[name='" + name + "']").val();
            }

            var json = JSON.stringify(
                    {
                        "class": getValForm('class'),
                        "id": getValForm('id'),
                        "margin": getValForm('margin'),
                        "padding": getValForm('padding'),
                        "border": getValForm('border'),
                        "modul-text": editor_data
                    }
            );

            console.log(getValForm('class'));

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


</div>