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
<?php

                    ?>

                    <select class="select-modal" name="form_type" id="form-type">
                        <option value="">-</option>
                        @foreach( Config::get('module.forms') as $form)
                            <option value="{{ $form['form_type'] }}" {{isset($module['form-type']) && $form['form_type'] == $module['form-type'] ? 'selected':'' }}>{{ $form['form_name'] }}</option>
                        @endforeach
                    </select>

                    <p>Povinné pole</p>

                    <div id="form-fields">
                        @if(isset($module['form-type']))
                            @foreach( Config::get("module.forms.".$module['form-type'].".fields") as $field)
                                <div class="checkbox checkbox-switchery">
                                    <label>
                                        <input class="switchery-modal" name="fields[]" type="checkbox"
                                               {{ in_array($field, $module['fields'])?"checked":"" }} value="{{ $field }}">{{ trans('forms.' . $field) }}
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>


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

        // Initialize multiple switches
        if (Array.prototype.forEach) {
            var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-modal'));
            elems.forEach(function (html) {
                var switchery = new Switchery(html);
            });
        }
        else {
            var elems = document.querySelectorAll('.switchery-modal');
            for (var i = 0; i < elems.length; i++) {
                var switchery = new Switchery(elems[i]);
            }
        }


        var forms = {!! json_encode(Config::get('module.forms')) !!};

        console.log(forms);

        var fields = $('#form-fields');


        $('#form-type').change(function () {
            var $this = $(this);

            fields.html('');
            $.each(forms[$this.val()]['fields'], function (index, val) {
                fields.append('<div class="checkbox checkbox-switchery"><label><input class="switchery-modal" name="fields[]" type="checkbox" value="'+index+'">'+val+'</label></div>');
            });

            // Initialize multiple switches
            if (Array.prototype.forEach) {
                var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-modal'));
                elems.forEach(function (html) {
                    var switchery = new Switchery(html);
                });
            }
            else {
                var elems = document.querySelectorAll('.switchery-modal');
                for (var i = 0; i < elems.length; i++) {
                    var switchery = new Switchery(elems[i]);
                }
            }



            console.log();
        });


        // Default initialization
        $('.select-modal').select2({
            minimumResultsForSearch: Infinity
        });



        $('#form-{{$module_id}}').submit(function (e) {

            e.preventDefault();

            var $this = $(this);

            function getValForm(name) {

                return $this.find("[name='" + name + "']").val();
            }

            function getMultipleValForm(name) {

                var tmp = [];
                $('input[name^='+name+']').filter(':checked').each(function(){
                    tmp.push($(this).val());
                });
                return tmp;
            }


            var json = JSON.stringify(
                    {
                        "class": getValForm('class'),
                        "id": getValForm('id'),
                        "margin": getValForm('margin'),
                        "padding": getValForm('padding'),
                        "border": getValForm('border'),
                        "form-type": getValForm('form_type'),
                        "fields": getMultipleValForm('fields')
                    }
            );


            $.ajax({
                url: '/admin/ajax-grideditor/module/form/save/{{$module_id}}',
                dataType: 'text',
                type: 'post',
                contentType: 'application/json',
                data: json,
                success: function (data, textStatus, jQxhr) {
                    console.log('success');
                    $('#modules-edit-modal').modal('hide');
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });


        });


    </script>


</div>