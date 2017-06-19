<template id="menu-item-template">
    <li class="dd-item">

        <div class="dd-handle dd-handle">
            <i class="fa fa-arrows icon-move"> @{{ item.name }} </i>
        </div>

        <div class="dd-content">

            <input type="hidden" class="item-order-input" v-model="item.order">

            <a class="collapsed" data-toggle="collapse" data-target="#item-menu-@{{ id }}">
                <span class="label label-success label-roundless" v-show="item.pageId">Stránka</span>
                <span class="label label-primary" v-show="item.categoryId">Kategorie</span>
                <span class="label label-default" v-show="isCustomUrl">Vlastní</span>

                @{{ item.name }}
            </a>

            <div id="item-menu-@{{ id }}" class="collapse">
                <div class="dd-item-setting-content">

                    <div class="row">

                        <div class="col-md-6">
                            <label>Název</label>
                            <div class="form-group">
                                <input type="text" class="form-control" v-model="item.name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Class</label>
                            <div class="form-group">
                                <input type="text" class="form-control" v-model="item.class">
                            </div>
                        </div>

                        <div class='col-md-6' v-if="isCustomUrl">
                            <label>Url</label>
                            <div class='form-group'>
                                <input type='text' class='form-control' v-model="item.url">
                            </div>
                        </div>
                    </div>


                    <div class="row">


                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="styled" v-model="item.openNewWindow">
                                    Otevírat do nového okna prohlížeče
                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="btn pull-right bg-danger" @click='removeItem'>
                                <i class="fa fa-trash"></i>
                                Smazat
                            </button>
                        </div>

                    </div>


                </div>
            </div>
        </div>

        <ol :class="{ 'empty': !item.children.length }">
            <menu-item v-for="menuItem in item.children" :item="menuItem"></menu-item>
        </ol>

    </li>
</template>