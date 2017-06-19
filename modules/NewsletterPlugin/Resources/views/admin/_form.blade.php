<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_fields" data-toggle="tab" aria-expanded="true">Základní informace a položky</a>
        </li>
        <li>
            <a href="#tab_localisation" data-toggle="tab" aria-expanded="true">Lokalizace - {{ $currentLanguage->name }}</a>
        </li>
        <li>
            <a href="#tab_props" data-toggle="tab" aria-expanded="true">Odesílání dat</a>
        </li>
    </ul>
    <div class="tab-content">

        {{-- TAB FIELDS --}}
        <div class="tab-pane has-padding active" id="tab_fields">
            <div class="form-group required {{ $errors->has($name = 'name') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Název') !!}
                {!! Form::text($name, null, ['class' => 'form-control maxlength', 'maxlength' => '250']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            {{-- Fields --}}

            <h4>Položky</h4>

            <div class="row">

                <div class="col-sm-3">
                    <div class="panel panel-flat">
                        <div class="panel-body">

                            <button type="button" class="btn btn-block btn-default" @click="addField('text')"><i class="fa fa-plus"></i> Textové pole</button>
                            <button type="button" class="btn btn-block btn-default" @click="addField('email')"><i class="fa fa-plus"></i> Emailová adresa</button>
                            <button type="button" class="btn btn-block btn-default" @click="addField('number')"><i class="fa fa-plus"></i> Číslice</button>
                            <button type="button" class="btn btn-block btn-default" @click="addField('textarea')"><i class="fa fa-plus"></i> Textová oblast</button>
                            <button type="button" class="btn btn-block btn-default" @click="addField('file')"><i class="fa fa-plus"></i> Soubor</button>

                        </div>
                    </div>
                </div>

                <div class="col-sm-9">
                    <div class="well">
                        <ol class="dd-list" id="menu-items-list">
                            <form-field v-for="field in fieldList" :field="field"></form-field>
                        </ol>

                        <em v-show="!fieldList.length">
                            Žádné položky
                        </em>
                    </div>
                    @include('module-formsplugin::admin.templates.field')
                    @include('module-formsplugin::admin.templates.text')
                    @include('module-formsplugin::admin.templates.email')
                    @include('module-formsplugin::admin.templates.number')
                    @include('module-formsplugin::admin.templates.textarea')
                    @include('module-formsplugin::admin.templates.file')
                </div>

            </div>


        </div>

        {{-- TAB LOCALISATION --}}
        <div class="tab-pane has-padding" id="tab_localisation">

            <div class="form-group required {{ $errors->has($name = 'submit_text') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Text tlačítka pro odeslání formuláře') !!}
                {!! Form::text($name, $form->localised->submit_text ?? null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '100',
                    'placeholder' => 'Např.: Odeslat'
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'success_message') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Zpráva, která bude zobrazena uživateli po odeslání formuláře') !!}
                {!! Form::textarea($name, $form->localised->success_message ?? null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '255',
                    'rows' => 2
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

        </div>

        {{-- TAB SENDING PROPERTIES --}}

        <div class="tab-pane has-padding" id="tab_props">
            <div class="form-group {{ $errors->has($name = 'send_to_email') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Zasílat na email') !!}
                {!! Form::email($name, null, ['class' => 'form-control']) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'email_view') ? 'has-error' : '' }}">
                {!! Form::label($name, 'Formát emailu') !!}
                {!! Form::select($name, [ '' => 'Výchozí' ], null, ['class' => 'form-control']) !!}
                @include('admin.vendor.form.field_error')
            </div>
        </div>
    </div>
</div>


@push('script')
{{ Html::script( url('js/jquery-ui.js') ) }}
{{ Html::script( url('js/uniform.js') ) }}
{{ Html::script( url('js/bootstrap-maxlength.js') ) }}
{{ Html::script( url('js/bootstrap-tagsinput.js') ) }}

<script>
    var initData = {
        fieldList: {!! $formFields ?? "[]" !!},
        typeNames: {
            text: 'Textové pole',
            email: 'Emailová adresa',
            number: 'Číslice',
            textarea: 'Textová oblast',
            file: 'Soubor'
        }
    };

    var fieldComponent = function(type, allowedOptions){
        return {
            props: [ 'options' ],
            template: '#field-' + type + '-template',
            data: function(){
                return {
                    name: initData.typeNames[type], max: 1
                }
            },
            created: function(){
                if(!allowedOptions){
                    allowedOptions = [];
                }
                allowedOptions = allowedOptions.concat([ 'id', 'class', 'required' ]);

                for(var option in this.options){
                    if($.inArray(option, allowedOptions) === -1){
                        delete this.options[option];
                    }
                }
            },
            attached: function(){
                var $element = $(this.$el);
                if(type == 'file'){
                    var $extensions = $element.find('.extensions');
                    $extensions.tagsinput({
                        cancelConfirmKeysOnEmpty: false,
                        trimValue: true,
                        confirmKeys: [13, 44, 32]
                    });
                    $extensions.on('beforeItemAdd', function(event) {

                        if(event.options && event.options.edited){
                            return;
                        }

                        var item = event.item;
                        if(item.substring(0,1) == '.'){
                            item = item.substring(1, item.length);
                            event.cancel = true;
                        }

                        var fileExtPattern = /^[0-9a-z]+$/;
                        if(!fileExtPattern.test(item)){
                            event.cancel = true;
                            return;
                        }

                        if(event.cancel)
                        {
                            $extensions.tagsinput('add', item, {edited: true});
                        }
                    });
                }
            }
        }
    };

    Vue.component('field-text', fieldComponent('text', [ 'value', 'placeholder', 'maxlength' ]));
    Vue.component('field-email', fieldComponent('email', [ 'value', 'placeholder' ]));
    Vue.component('field-number', fieldComponent('number', [ 'value', 'min', 'max' ]));
    Vue.component('field-textarea', fieldComponent('textarea', [ 'value', 'placeholder', 'maxlength', 'rows' ]));
    Vue.component('field-file', fieldComponent('file', [ 'accept' ]));

    Vue.component('form-field', {
        props: [ 'field' ],
        data: function(){
            return {
                id: 0,
                optionsComponent: null,
                type: null,
                typeNames: initData.typeNames,
            }
        },
        template: '#form-field-template',
        methods: {
            removeField: function(){
                this.$parent.fieldList.$remove(this.field);
            },
            makeId: function()
            {
                var text = "";
                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                for( var i=0; i < 5; i++ )
                    text += possible.charAt(Math.floor(Math.random() * possible.length));

                return text;
            }
        },
        watch: {
            type: function(){
                this.field.type = this.type;
                this.optionsComponent = 'field-' + this.type;
            }
        },
        created: function(){
            this.id = this.makeId();
        },
        attached: function(){
            var $element = $(this.$el);

            $element.find('input[type="checkbox"]').uniform();

            this.type = this.field.type;
            $element.closest('ol').sortable({
                handle: ".icon-move",
                stop: function( event, ui ) {
                    var order = 1;
                    $(this).children().each(function(){
                        $(this).find('.item-order-input')
                                .val(order++).trigger('change');
                    });

                }
            });
        }
    });

    new Vue({
        el: '#forms-plugin-form',
        data: {
            newField: {
                name: '',
                type: ''
            },
            fieldList: initData.fieldList
        },
        methods: {
            addField: function(type){
                this.fieldList.push({
                    id: 0,
                    name: 'Položka ' + initData.typeNames[type],
                    type: type,
                    options: {
                        required: true
                    }
                });
            },

            submitForm: function(){
                var $form = $(this.$el);

                var data = $form.serializeArray();
                data.push({
                    name: 'fields',
                    value: JSON.stringify(this.fieldList)
                });

                $form.find('.form-group.has-error').removeClass('has-error');
                $form.lock({
                    spinner: SpinnerType.OVER
                });

                $.ajax({
                    url: this.$el.action,
                    type: 'POST',
                    data: data
                }).done(function(response){
                    if(response.redirect){
                        window.location = response.redirect;
                    }
                    else{
                        alert(response);
                    }
                }).fail(function(request) {
                    if (request.responseJSON) {
                        for (var field in request.responseJSON) {
                            $form.find('input[name="' + field + '"]')
                                .next('.help-block')
                                .text(request.responseJSON[field])
                                .show()
                                .closest('.form-group')
                                .addClass('has-error');
                        }
                    }
                }).always(function () {
                    $form.unlock();
                });
            }
        },

        ready: function(){

            // Append data from Laravel
            this.url = initData.url;

            var $element = $(this.$el);

            // Maxlength
            $element.find('.maxlength').maxlength();
        }
    });
</script>
@endpush