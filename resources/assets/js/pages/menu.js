Vue.component('menu-item', {
    props: [ 'item' ],
    data: function(){
        return {
            id: 0
        }
    },
    template: '#menu-item-template',
    methods: {
        removeItem: function(){
            if (this.$parent.activeMenu) {
                this.$parent.activeMenu.items.$remove(this.item);
            } else {
                this.$parent.item.children.$remove(this.item);
            }
            $(this.$el).remove();
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
    computed: {
        /**
         * Is item with custom url?
         * @return {Boolean}
         */
        isCustomUrl: function () {
            return !this.item.pageId && !this.item.categoryId;
        }
    },
    created: function () {
        this.id = this.makeId();
    },
    attached: function () {
        var $element = $(this.$el),
            self = this;

        $element.data('component', self.item);
        $element.find('input[type="checkbox"]').uniform();

        var $lists = $('#menu-items-list').parent().find('ol');

        $lists.each(function () {
            var $otherLists = $lists.not(this);

            $(this).sortable({
                handle: ".icon-move",
                connectWith: $otherLists,
                start: function () {
                    $('#menu-page').addClass('sorting');
                },
                stop: function( event, ui ) {
                    var order = 1;
                    $('#menu-page').removeClass('sorting');
                    $(this).children().each(function(){
                        $(this).find('.item-order-input')
                            .val(order++).trigger('change');
                    });
                },
                receive: function(event, ui) {
                    var $sourceList = ui.sender,
                        $targetList = $(this),
                        $sourceItem = $sourceList.parent(),
                        $targetItem = $targetList.parent(),
                        item = ui.item.data('component'),
                        insertTo = $mainVm.activeMenu.items;

                    if ($targetItem.hasClass('dd-item')) {
                        insertTo = $targetItem.data('component').children;
                    }

                    var $next = ui.item.next();
                    if ($next.length) {
                        var indexOfItem = $next.data('component');
                        var index = insertTo.indexOf(indexOfItem);
                        insertTo.splice(index, 0, item);
                    } else {
                        insertTo.push(item);
                    }

                    if ($sourceItem.hasClass('dd-item')) {
                        $sourceItem.data('component').children.$remove(item);
                    } else { // main list
                        $mainVm.activeMenu.items.$remove(item);
                    }

                    ui.item.remove();
                }
            });
        });
    }
});

var $mainVm = new Vue({
    el: '#menu-page',
    data: {
        activeMenu: null,
        pageTree: null,
        categoryTree: null,
        customPage: {
            name: '',
            url: ''
        },
        newMenu: {
            name: '',
            modal: null,
            index : 0
        },
        menuSelect: null,
        menuLocations: initData.menuLocations,
        menuLocationsUsage: {},
        menuList: initData.menuList
    },
    methods: {

        /**
         * Modal for new menu
         */
        newMenuModal: function(){
            this.newMenu.modal.modal();
        },

        /**
         * Delete active menu
         */
        deleteMenu: function (url) {
            var vueContext = this;
            swal(
                {
                    title: window.trans.confirmDelete.title,
                    text: window.trans.confirmDelete.text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF5350",
                    confirmButtonText: window.trans.confirmDelete.confirm,
                    cancelButtonText: window.trans.confirmDelete.cancel,
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if(!isConfirm) return;

                    vueContext.deleteMenuConfirm(url);
                }
            );
        },

        deleteMenuConfirm: function(){
            var vueContext = this;
            $.ajax({
                url: event.target.href,
                data: { id: this.activeMenu.id },
                type: 'DELETE',
                dataType: 'json'
            }).done(function(){
                vueContext.menuList.$remove(vueContext.activeMenu);
            });
        },

        /**
         * Create new menu
         */
        createNewMenu: function(){
            if(!this.newMenu.name.length){
                alert('Vyplňte prosím název.');
                return;
            }

            var newMenu = {
                id: 0,
                name: this.newMenu.name,
                items: [ ]
            };

            this.menuList.push(newMenu);

            $.post({
                url: this.url.newMenu,
                data: {
                    name: newMenu.name
                }
            }).done(function(response){
                newMenu.id = response.id;
            });

            this.newMenu.index = this.menuList.length - 1;

            this.newMenu.name = '';
            this.newMenu.modal.modal('hide');
        },

        /**
         * Switch menu
         */
        switchActiveMenu: function (e) {
            this.activeMenu = this.menuList[e.target.value];
        },

        /**
         * Checks occurrence in children by id
         * @param {Array} children
         * @param {String} idProperty
         * @param {Number} id
         * @returns {boolean}
         */
        checkItemOccurrenceInChildren: function(children, idProperty, id){
            for(var pi in children){
                if(children[pi][idProperty] == id){
                    return true;
                }
            }
            return false;
        },

        /**
         * Add selected items in specified tree to menu
         * @param {string} treeName
         */
        addSelectedItems: function (treeName) {
            if(!this.activeMenu) return;
            var tree, idProperty;

            switch (treeName) {
                case 'pages':
                    tree = this.pageTree;
                    idProperty = 'pageId';
                    break;
                case 'categories':
                    tree = this.categoryTree;
                    idProperty = 'categoryId';
                    break;
                default: return;
            }

            var nodes = tree.getSelectedNodes();
            for(var ni in nodes){

                var hasParent = !nodes[ni].getParent().isRoot();

                nodes[ni].toggleSelected();

                var pushTo = this.activeMenu.items;

                // check if is page already in menu
                var exists = false;
                for(var ei in this.activeMenu.items){

                    if(this.activeMenu.items[ei][idProperty] == nodes[ni].key){
                        exists = true;
                        break;
                    }

                    // if node has parent and parent is in menu
                    if(hasParent && this.activeMenu.items[ei][idProperty] == nodes[ni].getParent().key){
                        pushTo = this.activeMenu.items[ei].children;
                        if(this.checkItemOccurrenceInChildren(pushTo, idProperty, nodes[ni].key)){
                            exists = true;
                            break;
                        }
                    }
                }

                if(exists) break;

                var menuItem = {
                    id: 0,
                    name: nodes[ni].title,
                    class: '',
                    openNewWindow: false,
                    order: pushTo.length + 1,
                    children: []
                };

                menuItem[idProperty] = nodes[ni].key;

                pushTo.push(menuItem);
            }
        },

        /**
         * Add selected pages to menu items
         */
        addSelectedPages: function () {
            this.addSelectedItems('pages');
        },

        /**
         * Add selected pages to menu items
         */
        addSelectedCategories: function () {
            this.addSelectedItems('categories');
        },

        /**
         * Add custom page
         */
        addCustomPage: function(){
            if(!this.customPage.name.length){
                alert('Vyplňte prosím název vlastní stránky.');
                return;
            }

            var customUrl = this.customPage.url;
            if (customUrl.length && customUrl.charAt(0) === '/') {
                customUrl = customUrl.substring(1, customUrl.length)
            }

            this.activeMenu.items.push({
                id: 0,
                name: this.customPage.name,
                url: customUrl,
                class: '',
                openNewWindow: true,
                pageId: 0,
                order: this.activeMenu.items.length + 1,
                children: []
            });

            this.customPage.url = this.customPage.name = '';
        },

        /**
         * Save all changes
         */
        saveChanges: function(e){
            var $button = $(e.target);

            $button.lock({
                spinner: SpinnerType.OVER
            });

            $.post({
                url: this.url.saveMenu,
                data: {
                    menu: JSON.stringify(this.menuList),
                    menuLocations: JSON.stringify(this.menuLocations)
                }
            }).done(function(){
                $.jGrowl('Všechny změny úspěšně uloženy.', {
                    header: 'Úspěch',
                    theme: 'bg-teal'
                });
            }).fail(function(){
                $.jGrowl('Nepodařilo se uložit změny. Kontaktujte správce webu.', {
                    header: 'Chyba',
                    theme: 'bg-danger'
                });
            }).always(function () {
                $button.unlock();
            });
        },

        /**
         * is active menu on specified location?
         * @param menu
         * @returns {boolean}
         */
        hasActiveMenuLocation: function(menu){
            return this.activeMenu.id == this.menuLocations[menu];
        },

        /**
         * Change menu location checkbox
         * @param event
         * @param {String} menu
         */
        changedMenuLocation: function(event, menu){
            this.menuLocations[menu] = event.target.checked ? this.activeMenu.id : null;
        }
    },
    watch: {
        /**
         * Update select2 when menuList changes
         */
        menuList: function(){
            this.menuSelect.select2("destroy")
                .val(this.newMenu.index)
                .select2()
                .trigger('change');

            this.newMenu.index = 0;
        },

        activeMenu: function(){
            // Uniform checkboxes
            $('.uniform').uniform();
        }
    },

    ready: function(){

        // Append data from Laravel
        this.url = initData.url;

        // Init active menu
        if(this.menuList.length) {
            this.activeMenu = this.menuList[0];
        }

        // Fancy tree of pages
        this.pageTree = $("#page-tree").fancytree({
            checkbox: true,
            selectMode: 2   ,
            autoScroll: true,
            icon: false,
            source: {
                url: this.url.pageTree
            }
        }).fancytree("getTree");

        // Fancy tree of categories
        this.categoryTree = $("#category-tree").fancytree({
            checkbox: true,
            selectMode: 2   ,
            autoScroll: true,
            icon: false,
            source: {
                url: this.url.categoryTree
            }
        }).fancytree("getTree");

        // Select2 for menu
        this.menuSelect = $('#menu-select').select2().on('change', this.switchActiveMenu);

        // New menu modal
        this.newMenu.modal = $('#add-menu-modal');
    }
});