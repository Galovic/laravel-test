<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab">
                {!! trans('general.tabs.details') !!}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane has-padding active" id="tab_details">

            <div class="form-group">
                {!! Form::label('display_name', trans('admin/roles/general.columns.display_name') ) !!}
                {!! Form::text('display_name', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', trans('admin/roles/general.columns.description') ) !!}
                {!! Form::text('description', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::checkbox('enabled', 1, null, ['id' => 'input-enabled']) !!}
                {!! Form::label('input-enabled', trans('general.status.enabled') ) !!}
            </div>

            <h3>Oprávnění</h3>

            @foreach($groups as $groupId => $group)
            <table class="table">
                <thead>
                <tr>
                    <th></th>
                    @foreach($group['permissions'] as $permission => $weight)
                    <th>
                        {{ Form::checkbox(null, 1, null, [
                            'data-all' => $permission == 'all' ? 1 : null
                        ]) }}
                        {{ trans("admin/permissions.permissions.{$permission}") }}
                    </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($group['areas'] as $area)
                    <tr>
                        <th>{{ trans("admin/permissions.areas.{$area}") }}</th>
                        @foreach($group['permissions'] as $permission => $weight)
                            <td>{{ Form::checkbox("{$area}[{$permission}]", 1, null, [
                                'data-all' => $permission == 'all' ? 1 : null,
                                'checked' => isset($permissionsNames) && isset($permissionsNames["$area-$permission"]) ?: null
                            ]) }}</td>
                        @endforeach
                    </tr>
                @endforeach

                @if(isset($group['modules']))
                    @foreach($group['modules'] as $module)
                        <tr>
                            <th>{{ $module->trans('admin.permissions') }} <span title="Modul">&#9410;</span></th>
                            @foreach($group['permissions'] as $permission => $weight)
                                <td>{{ Form::checkbox("module_{$module->config('nickname')}[{$permission}]", 1, null, [
                                    'data-all' => $permission == 'all' ? 1 : null,
                                    'checked' => isset($permissionsNames) && isset($permissionsNames["module_{$module->config('nickname')}-$permission"]) ?: null
                                ]) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            @endforeach

        </div>
    </div>
</div>


@push('script')
{{ Html::script(url('js/switchery.js')) }}
<script>
    (function($){
        // Checkboxes in table header
        $('thead input[type="checkbox"]').on('click', function(){
            var $this = $(this);
            var isChecked = $this.is(':checked');
            var $table = $this.closest('table');

            // if is for all, check whole table
            if(this.hasAttribute('data-all')){
                $table.find('input[type="checkbox"]')
                        .prop('checked', isChecked)
                        .uniform('update');
            }else {
                var index = $this.closest('th').prevAll().length - 1;

                // check / uncheck whole column
                $table.find('tbody tr')
                        .find('td:eq(' + index + ')')
                        .find('input[type="checkbox"]')
                        .prop('checked', isChecked)
                        .uniform('update');

                // if is unchecked, uncheck "all" checkbox on each row
                if (!isChecked) {
                    $table.find('tr').each(function () {
                        $(this).find('input[data-all=1]')
                                .prop('checked', false)
                                .uniform('update');
                    });
                }
            }


        }).uniform();

        // Checkboxes in table body
        $('tbody input[type="checkbox"]').on('click', function(){
            var $this = $(this);
            var isChecked = $this.is(':checked');

            // If is unchecked
            if(!isChecked){
                var index = $this.closest('td').prevAll().length;
                var $headerRow = $this.closest('table')
                        .find('thead tr');

                // uncheck "all" checkbox in table header
                $headerRow.find('input[data-all=1]')
                        .prop('checked', false)
                        .uniform('update');

                // uncheck checkbox for current column in table header
                $headerRow.find('th:eq(' + index + ')')
                        .find('input[type="checkbox"]')
                        .prop('checked', false)
                        .uniform('update');

                // uncheck "all" checkbox on current row
                $this.closest('tr').find('input[data-all=1]')
                        .prop('checked', false)
                        .uniform('update');
            }

            // If is for all on the row
            if(this.hasAttribute('data-all')){
                // check / uncheck all checkboxes on current row
                $this.closest('tr').find('input[type="checkbox"]')
                    .prop('checked', isChecked)
                    .uniform('update');

                // if is unchecked
                if(!isChecked){

                    // for each column uncheck checkbox in table header
                    $this.closest('td').siblings().each(function(){
                        var siblingIndex = $(this).prevAll().length;
                        $this.closest('table')
                                .find('thead tr')
                                .find('th:eq(' + siblingIndex + ')')
                                .find('input[type="checkbox"]')
                                .prop('checked', false)
                                .uniform('update');
                    })
                }
            }
        }).uniform();

        $('tbody input[type="checkbox"][data-all]:checked').each(function () {
            // check / uncheck all checkboxes on current row
            $(this).closest('tr').find('input[type="checkbox"]')
                .prop('checked', true)
                .uniform('update');
        });

        new Switchery(document.getElementById('input-enabled'));

    })(jQuery);
</script>
@endpush