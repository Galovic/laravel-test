<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">Modul články {{$module_id}}</h4>

</div>

<div class="modal-body">
    <div class="tabbable tab-content-bordered">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="active"><a href="#tab_modal_details" data-toggle="tab" aria-expanded="true">Základní
                    informace</a></li>
        </ul>
        <form id="form-{{$module_id}}">
            <div class="tab-content">
                <div class="tab-pane has-padding active" id="tab_modal_details">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="id">Kategorie</label>
                                <select multiple="multiple" class="my-select" name="article-category">
                                    @foreach($param as $p)
                                        @if(isset($module['article-category']))
                                            @if(in_array($p->id, $module['article-category']))
                                                <option value="{{$p->id}}" selected="selected">{{$p->name}}</option>
                                            @else
                                                <option value="{{$p->id}}">{{$p->name}}</option>
                                            @endif
                                        @else
                                            <option value="{{$p->id}}">{{$p->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="id">View</label>
                                <input class="form-control" name="article-view" type="text" value="{{ $module['article-view'] or old('article-view') }}" id="article-view">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="id">Počet položek <em>(prázdné pole = všechny položky)</em></label>
                                <input class="form-control" name="article-count" type="text" value="{{ $module['article-count'] or old('article-count') }}" id="article-count">
                            </div>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="save-{{$module_id}}">Save changes</button>
            </div>
        </form>
    </div>
    {{--<script type="text/javascript" src="assets/js/pages/form_select2.js"></script>--}}
    <script>
        $('#form-{{$module_id}}').submit(function (e) {
            e.preventDefault();
            var $this = $(this);

            function getValForm(name) {
                return $this.find("[name='" + name + "']").val();
            }

            var json = JSON.stringify(
                {
                    "article-category": getValForm('article-category'),
                    "article-view": getValForm('article-view'),
                    "article-count": getValForm('article-count'),
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
        $('.my-select').select2();
    </script>


