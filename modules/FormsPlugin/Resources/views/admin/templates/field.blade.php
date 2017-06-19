<template id="form-field-template">
    <li class="dd-item">

        <div class="dd-handle dd-handle">
            <i class="fa fa-arrows icon-move"></i>
        </div>

        <div class="dd-content">

            <input type="hidden" class="item-order-input" v-model="field.order">

            <a class="collapsed" data-toggle="collapse" data-target="#item-menu-@{{ id }}">
                @{{ field.name }} <em class="text-thin text-muted" v-if="optionsComponent">@{{ typeNames[field.type] }}</em>
            </a>

            <div id="item-menu-@{{ id }}" class="collapse">
                <div class="dd-item-setting-content">

                    <div class="row">

                        <div class="col-md-6">
                            <label>Název</label>
                            <div class="form-group">
                                <input type="text" class="form-control" v-model="field.name">
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label>Typ</label>
                            <div class="form-group">
                                <select class="form-control" v-model="type">
                                    <option value="text">Textové pole</option>
                                    <option value="email">Emailová adresa</option>
                                    <option value="number">Číslice</option>
                                    <option value="textarea">Textová oblast</option>
                                    <option value="file">Soubor</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Class</label>
                            <div class="form-group">
                                <input type="text" class="form-control" v-model="field.options.class">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Id</label>
                            <div class="form-group">
                                <input type="text" class="form-control" v-model="field.options.id">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="required" class="styled" v-model="field.options.required">
                                    Povinné pole
                                </label>
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <component :is="optionsComponent" :options="field.options"></component>

                    </div>


                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="btn pull-right bg-danger" @click='removeField'>
                                <i class="fa fa-trash"></i> Smazat
                            </button>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </li>
</template>