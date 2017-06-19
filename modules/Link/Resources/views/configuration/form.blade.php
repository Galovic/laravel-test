{{ Form::model($configuration, [ 'id' => 'model-link-configuration-form' ]) }}

<div class="form-group">
    {{ Form::label($id = 'ml-text-input', 'Text') }}
    {{ Form::text($name = 'text', null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
    @include('vendor.form.field_error')
</div>

<div class="form-group">
    {{ Form::label($id = 'ml-custom-url-input', 'Vlastní url: ') }}
    {{ Form::checkbox($name = 'custom_url', 1, $customUrl = !!$configuration->url, [
        'id' => $id,
        'v-model' => 'customUrl'
    ]) }}
    @include('vendor.form.field_error')
</div>

<div v-show="!customUrl">
    <div class="form-group">
        {{ Form::label($id = 'ml-model-input', 'Odkaz na') }}
        {{ Form::select($name = 'model_type', $models, null, [
            'class' => 'form-control',
            'id' => $id,
            'v-model' => 'model'
        ]) }}
        @include('vendor.form.field_error')
    </div>

    @foreach($modelLists as $key => $list)
    <div class="form-group" v-show="model == '{{ $key }}'">
        {{ Form::label($id = 'ml-' . $key . '-id-input', $models[$key]) }}
        {{ Form::select( $name = $key . '_id', $list, null, [
            'class' => 'form-control',
            'id' => $id
        ]) }}
        @include('vendor.form.field_error')
    </div>
    @endforeach
</div>

<div class="form-group" v-show="customUrl">
    {{ Form::label($id = 'ml-url-input', 'Vlastní url') }}
    {{ Form::text($name = 'url', null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
    @include('vendor.form.field_error')
</div>

<div class="form-group">
    {{ Form::label($id = 'ml-view-input', 'View') }}
    {{ Form::select($name = 'view', $views, null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
    @include('vendor.form.field_error')
</div>


<div class="form-group">
    {{ Form::label('ml-attributes-input', 'HTML atributy') }}

    <template id="attribute-row">
        <div class="row">
            <div class="col-xs-11">
                <div class="input-group" style="margin-bottom: 10px">
                    {{ Form::text('attribute_key[]', null, [
                        'class' => 'form-control',
                        'id' => $id,
                        'v-bind:value' => 'attribute.name'
                    ]) }}
                    <div class="input-group-addon"> = </div>
                    {{ Form::text('attribute_value[]', null, [
                        'class' => 'form-control',
                        'id' => $id,
                        'v-bind:value' => 'attribute.value'
                    ]) }}
                </div>
            </div>
            <div class="col-xs-1">
                <button @click.prevent="this.$dispatch('remove')" type="button" class="btn btn-default">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </template>

    <link-attribute v-for="attribute in attributes" @remove="attributes.$remove(attribute)" :attribute='attribute'>
    </link-attribute>

    <div class="text-center">
        <a @click.prevent="addAttributeRow" href="#">
            Přidat atribut
        </a>
    </div>
</div>


{{ Form::close() }}

<script>
    var LinkAttribute = Vue.extend({
        props: [ 'attribute' ],
        template: '#attribute-row'
    });

    var ModuleLinkForm = Vue.extend({});

    var form = new ModuleLinkForm({
        el: '#model-link-configuration-form',
        data: {
            customUrl: null,
            model: '',
            attributes: {!! json_encode($configuration->javascript_tags) !!},
            customUrlSwitchery: null
        },
        components: {
            'link-attribute': LinkAttribute
        },
        methods: {
            addAttributeRow: function(){
                this.attributes.push({
                    name: '',
                    value: ''
                });
            },
            fillForm: function (data) {
                this.attributes = [];

                // Attributes
                if (!data.attribute_key || !data.attribute_value) {
                    return false;
                }

                for (var i in data.attribute_key) {
                    if (Object.prototype.hasOwnProperty.call(data.attribute_key, i)
                        && Object.prototype.hasOwnProperty.call(data.attribute_value, i)) {
                        this.attributes.push({
                            name: data.attribute_key[i],
                            value: data.attribute_value[i]
                        });
                    }
                }
            }
        },
        created: function () {
            this.customUrl = document.getElementById('ml-custom-url-input').checked;
        },
        ready: function(){
            // Switchery
            this.customUrlSwitchery = new Switchery(document.getElementById('ml-custom-url-input'));

            // Form fill
            $(this.$el).trigger('admin:form-fill-ready', this.fillForm);
        }
    });
</script>