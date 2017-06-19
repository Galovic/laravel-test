{{ Form::model($configuration, [ 'id' => 'model-view-configuration-form' ]) }}

    <div class="form-group ">
        {{ Form::label($id = 'mv-view-input', 'View') }}
        {{ Form::select('view', $views, null, [
            'class' => 'form-control',
            'id' => $id
        ]) }}
    </div>

    <div id="mv-view-variables"></div>

{{ Form::close() }}

<script>
    (function ($) {
        var $viewInput = $('#mv-view-input'),
            $viewVariables = $('#mv-view-variables'),
            formFilled = false,
            defaultData = {
                variable: {!! isset($variables) && $variables ? json_encode($variables) : 'null' !!}
            };

        function loadVariables() {
            var view = $viewInput.val();
            $viewVariables.lock();

            $.getJSON("{{ route('module.view.variables') }}", {view: view})
                .done(function (response) {
                    showVariablesInputs(response.variables);

                    if (!formFilled) {
                        fillForm(defaultData);
                        // Form fill
                        $('#model-view-configuration-form').trigger('admin:form-fill-ready', fillForm);

                        formFilled = true;
                    }
                })
                .always(function () {
                    $viewVariables.unlock();
                });
        }

        function showVariablesInputs(variables) {
            $viewVariables.children().remove();

            for (var variable in variables) {
                var inputName = 'variable['+ variable +']',
                    inputId = variable + '_' + variable,
                    props = variables[variable],
                    $input;

                if (!props) {
                    continue;
                }

                if (props.type && props.type !== 'select') {
                    $input = $('<input>', {
                        type: props.type,
                        value: props.value,
                        placeholder: props.placeholder,
                        name: inputName,
                        id: inputId,
                        'class': 'form-control'
                    });
                } else if (props.type && props.type === 'select') {
                    $input = $('<select>', {
                        name: inputName,
                        id: inputId,
                        'class': 'form-control'
                    });

                    for (var option in props.options) {
                        $input.append(
                            $('<option />', {value: option, text: props.options[option]})
                        )
                    }
                }

                $viewVariables.append(
                    $('<div />', {'class': 'form-group'}).append(
                        $('<label />', {'for': inputId}).text(props.label || variable)
                    ).append($input)
                )
            }
        }

        function fillForm (data) {
            if (typeof data.variable === 'object') {
                for (var variable in data.variable) {
                    $viewVariables
                        .find('*[name="variable['+variable+']"]')
                        .val(data.variable[variable])
                }
            }
        }

        $viewInput.on('change', loadVariables);
        loadVariables();
    }(jQuery));
</script>