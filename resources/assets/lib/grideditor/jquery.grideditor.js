$.fn.removeText = function () {
    this.each(function () {

        // Get elements contents
        var $cont = $(this).contents();

        // Loop through the contents
        $cont.each(function () {
            var $this = $(this);

            // If it's a text node
            if (this.nodeType == 3) {
                $this.remove(); // Remove it
            } else if (this.nodeType == 1) { // If its an element node
                $this.removeText(); //Recurse
            }
        });
    });
}

/* jQuery selector to match exact text inside an element
 *  http://wowmotty.blogspot.com/2010/05/jquery-selectors-adding-contains-exact.html
 *  :containsExact()     - case insensitive
 *  :containsExactCase() - case sensitive
 *  :containsRegex()     - set by user ( use: $(el).find(':containsRegex("/(red|blue|yellow)/gi")') )
 */
$.extend($.expr[":"], {
    containsExact: $.expr.createPseudo ?
        $.expr.createPseudo(function (text) {
            return function (elem) {
                return $.trim(elem.innerHTML.toLowerCase()) === text.toLowerCase();
            };
        }) :
        // support: jQuery <1.8
        function (elem, i, match) {
            return $.trim(elem.innerHTML.toLowerCase()) === match[3].toLowerCase();
        },

    containsExactCase: $.expr.createPseudo ?
        $.expr.createPseudo(function (text) {
            return function (elem) {
                return $.trim(elem.innerHTML) === text;
            };
        }) :
        // support: jQuery <1.8
        function (elem, i, match) {
            return $.trim(elem.innerHTML) === match[3];
        },

    containsRegex: $.expr.createPseudo ?
        $.expr.createPseudo(function (text) {
            var reg = /^\/((?:\\\/|[^\/])+)\/([mig]{0,3})$/.exec(text);
            return function (elem) {
                return reg ? RegExp(reg[1], reg[2]).test($.trim(elem.innerHTML)) : false;
            };
        }) :
        // support: jQuery <1.8
        function (elem, i, match) {
            var reg = /^\/((?:\\\/|[^\/])+)\/([mig]{0,3})$/.exec(match[3]);
            return reg ? RegExp(reg[1], reg[2]).test($.trim(elem.innerHTML)) : false;
        }

});


/**
 * Frontwise grid editor plugin.
 */
(function ($) {

    $.fn.gridEditor = function (options) {

        var self = this;
        var grideditor = self.data('grideditor');
        var html_editor = self.data('html_editor');

        /** Methods **/

        if (arguments[0] === 'getHtml') {
            if (html_editor) {
                return html_editor.getValue();
            } else if (grideditor) {

                if (self.hasClass('ge-editing')) {
                    self.hide();
                    grideditor.deinit();
                }

                var html = self.html();
                grideditor.init();
                return html;
            } else {
                return self.html();
            }
        } else if (arguments[0] === 'getModules') {
            return grideditor.getModules();
        } else if (arguments[0] === 'destroy') {
            if (grideditor) {
                grideditor.destroy();
            }
            return;
        }

        /** Initialize plugin */

        self.each(function (baseIndex, baseElem) {
            baseElem = $(baseElem);

            // Wrap content if it is non-bootstrap
            if (baseElem.children().length && !baseElem.find('div.row').length) {
                var children = baseElem.children();
                var newRow = $('<div class="row"><div class="col-md-12"/></div>').appendTo(baseElem);
                newRow.find('.col-md-12').append(children);
            }

            var settings = $.extend({
                'new_row_layouts': [ // Column layouts for add row buttons
                    [12],
                    [6, 6],
                    [4, 4, 4],
                    [3, 3, 3, 3],
                    [2, 2, 2, 2, 2, 2],
                    [2, 8, 2],
                    [4, 8],
                    [8, 4]
                ],
                'row_classes': [{label: 'Example class', cssClass: 'example-class'}],
                'col_classes': [{label: 'Example class', cssClass: 'example-class'}],
                'layout_types': [{value: '', text: "-"}],
                'col_tools': [], /* Example:
                 [ {
                 title: 'Set background image',
                 iconClass: 'glyphicon-picture',
                 on: { click: function() {} }
                 } ]
                 */
                'row_tools': [],
                'custom_filter': '',
                'content_types': ['tinymce'],
                'valid_col_sizes': [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                'source_textarea': '',
                'message_loading': 'Loading',
                'page_id': null,
                'warning_modal': {
                    "row_delete": ["Jste si jistý?", "Tento krok již nebude možné vrátit!", "Ano, jsem si jistý", "Zrušit"],
                    "col_delete": ["Jste si jistý?", "Tento krok již nebude možné vrátit!", "Ano, jsem si jistý", "Zrušit"],
                    "module_delete": ["Jste si jistý?", "Tento krok již nebude možné vrátit!", "Ano, jsem si jistý", "Zrušit"],
                },
                isFirstInit: false
            }, options);

            // Elems
            var canvas,
                mainControls,
                addRowGroup,
                htmlTextArea;

            var modules = settings.modules || {};

            var colClasses = ['col-md-', 'col-lg-', 'col-sm-', 'col-xs-'];
            var curColClassIndex = 0; // Index of the column class we are manipulating currently
            var MAX_COL_SIZE = 12;

            var col_guid;

            var message_grid_loading = $('<div class="label label-info">' + settings.message_loading + '...</div>');
            setup();
            init();

            function destroy () {
                self.removeData('grideditor');
                self.removeData('html_editor');
                self.removeData('modules');

                baseElem.attr('class', null).html('').off('click');
                var $window = $(window);
                $window.off('scroll');
                canvas.removeClass('ge-canvas');
                mainControls.remove();
                htmlTextArea.remove();
                $('#html_editor').remove();
            }

            function setup() {
                /* Setup canvas */
                canvas = baseElem.addClass('ge-canvas');

                if (settings.source_textarea) {
                    var sourceEl = $(settings.source_textarea);

                    sourceEl.addClass('ge-html-output');
                    htmlTextArea = sourceEl;

                    if (sourceEl.val()) {
                        self.html(sourceEl.val());
                    }
                }

                if (typeof htmlTextArea === 'undefined' || !htmlTextArea.length) {
                    htmlTextArea = $('<textarea class="ge-html-output" id="ge-html-output" />').insertBefore(canvas);
                }

                /* Create main controls*/
                mainControls = $('<div class="ge-mainControls" />').insertBefore(htmlTextArea);
                var wrapper = $('<div class="ge-wrapper ge-top" />').appendTo(mainControls);

                // Add row
                addRowGroup = $('<div class="ge-addRowGroup btn-group" />').appendTo(wrapper);
                $.each(settings.new_row_layouts, function (j, layout) {
                    var btn = $('<a class="btn btn-xs btn-primary" />')
                            .attr('title', 'Add row ' + layout.join('-'))
                            .on('click', function () {
                                var container = createContainer().appendTo(canvas);
                                var row = createRow().appendTo(container);
                                layout.forEach(function (i) {
                                    createColumn(i).appendTo(row);
                                });
                                initRow();
                            })
                            .appendTo(addRowGroup)
                        ;

                    btn.append('<span class="fa fa-plus-circle"/>');

                    var layoutName = layout.join(' - ');
                    var icon = '<div class="row ge-row-icon">';
                    layout.forEach(function (i) {
                        icon += '<div class="column col-xs-' + i + '"/>';
                    });
                    icon += '</div>';
                    btn.append(icon);
                });


                // Buttons on right
                var layoutDropdown = $('<div class="dropdown pull-right ge-layout-mode">' +
                    '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span>Desktop</span></button>' +
                    '<ul class="dropdown-menu" role="menu">' +
                    '<li><a data-width="auto" title="Desktop"><span>Desktop</span></a></li></li>' +
                    '<li><a title="Large "><span>Large screen</span></a>' +
                    '<li><a title="Tablet"><span>Tablet</span></li>' +
                    '<li><a title="Phone"><span>Phone</span></a></li>' +
                    '</ul>' +
                    '</div>')
                    .on('click', 'a', function () {
                        var a = $(this);
                        switchLayout(a.closest('li').index());
                        var btn = layoutDropdown.find('button');
                        btn.find('span').remove();
                        btn.append(a.find('span').clone());
                    })
                    .appendTo(wrapper);

                var btnGroup = $('<div class="btn-group pull-right"/>')
                        .appendTo(wrapper);

                var htmlButton = $('<button title="Edit Source Code" type="button" class="btn btn-xs btn-primary gm-edit-mode" id="edit-source-code"><span class="fa fa-chevron-left"></span><span class="fa fa-chevron-right"></span></button>')
                    .on('click', function () {
                        if (htmlButton.hasClass('active')) {


                            canvas.empty().html(html_editor.getValue()).show();
                            $('#html_editor').remove();

                            init();
                            htmlTextArea.hide();
                            self.removeData('html_editor');

                        } else {

                            deinit();
                            htmlTextArea
                                .height(0.8 * $(window).height())
                                .val(canvas.html())
                                .hide()
                            ;

                            $('<div id="html_editor"></div>').insertBefore(canvas).text(html_beautify(canvas.html())).show(); //

                            // HTML editor
                            html_editor = ace.edit("html_editor");
                            html_editor.setTheme("ace/theme/monokai");
                            html_editor.getSession().setMode("ace/mode/html");
                            html_editor.setShowPrintMargin(false);

                            self.data('html_editor', html_editor);
                            canvas.hide();


                        }

                        htmlButton.toggleClass('active btn-danger');
                    })
                    .appendTo(btnGroup);

                if (settings.versions && settings.versions.length) {
                    var $versionList = $('<ul />', {
                        'class': 'dropdown-menu',
                        role: 'menu',
                        css: {
                            height: 'auto',
                            'max-height': '300px',
                            'overflow-x': 'hidden'
                        }
                    });

                    var $button = $('<button />', {
                        type: 'button',
                        'class': 'btn btn-xs btn-primary dropdown-toggle',
                        'data-toggle': 'dropdown'
                    });

                    var $versionDropdown = $('<div />', {
                            'class': 'dropdown pull-right mr-5',
                            id: 'version-switch'
                        })
                        .append(
                            $button.text('Verze')
                        )
                        .append($versionList)
                        .appendTo(wrapper);

                    for (var vi in settings.versions) {
                        var version = settings.versions[vi],
                            versionText = 'Verze ' + version.index,
                            smallText = 'Vytvořeno ' + version.date;

                        if (version.author) {
                            smallText += ' uživatelem ' + version.author;
                        }

                        $versionList.append(
                            $('<li />').append(
                                $('<a />', {
                                    'href': '#',
                                    'data-id': version.id,
                                    'class': version.isActive ? 'active' : null
                                }).append(
                                    version.isActive ?
                                        $('<strong />').text(versionText) :
                                        $('<span />').text(versionText)
                                ).append(
                                    $('<small />', {'class': 'text-muted display-block'})
                                        .text(smallText)
                                ).on('click', function (e) {
                                    e.preventDefault();
                                    var $this = $(this);
                                    if ($this.hasClass('active')) {
                                        return;
                                    }

                                    loadDifferentVersion($this.data('id'));
                                })
                            )
                        );

                        if (version.isActive) {
                            $button.html('<i class="fa fa-paperclip"></i> Verze ' + version.index);
                        }
                    }
                }

                // Make controls fixed on scroll
                var $window = $(window);
                $window.on('scroll', function (e) {
                    if (
                        $window.scrollTop() > mainControls.offset().top &&
                        $window.scrollTop() < canvas.offset().top + canvas.height()
                    ) {
                        if (wrapper.hasClass('ge-top')) {
                            wrapper
                                .css({
                                    left: wrapper.offset().left,
                                    width: wrapper.outerWidth(),
                                })
                                .removeClass('ge-top')
                                .addClass('ge-fixed')
                            ;
                        }
                    } else {
                        if (wrapper.hasClass('ge-fixed')) {
                            wrapper
                                .css({left: '', width: ''})
                                .removeClass('ge-fixed')
                                .addClass('ge-top')
                            ;
                        }
                    }
                });

                /* Init RTE on click */
                canvas.on('click', '.ge-content', function (e) {
                    var rte = getRTE($(this).data('ge-content-type'));
                    if (rte) {
                        rte.init(settings, $(this));
                    }
                });
            }

            function reset() {
                deinit();
                init();
            }

            function init() {

                $("#edit-source-code").prop('disabled', true);

                canvas.hide();
                message_grid_loading.insertAfter(canvas);
                var $modules = canvas.find('.module-wrapper');
                var modulesIds = [];
                $modules.each(function(){
                    var moduleId = Number($(this).data('module-id'));
                    modulesIds.push(moduleId);
                    if (!getModule(moduleId)) {
                        setModule(moduleId, false);
                    }
                });
                /*canvas.find('.has-module').each(function () {

                    var $this = $(this);


                    var node = $this.contents().filter(function () {
                        return this.nodeType == 3; // text node
                    });


                    module_code = node.text();


                    $this.contents().filter(function () {
                        return this.nodeType === 3;
                    }).remove();
                    module_code = $.parseJSON(module_code);


                    $.each(module_code.modules, function (k, v) {
                        modules++;


                        $this.append(
                            $('<div class="module-wrapper"></div>').load('/admin/ajax-grideditor/module/' + v.type + '/preview/' + v.id));
                    });


                });*/


                if (modulesIds.length > 0) {
                    $("#edit-source-code").prop('disabled', false);
                    message_grid_loading.remove();

                    if(this.isFirstInit) {
                        $.ajax({
                            url: grideditorOpts.urls.modules.load,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                ids: JSON.stringify(modulesIds)
                            }
                        }).done(function (response) {
                            if (!response.contents) return;

                            for (var ci in response.contents) {
                                $modules.filter("*[data-module-id=" + ci + "]")
                                    .html(response.contents[ci]);
                            }

                            createModuleControls();
                            makeSortableModules();
                            canvas.show();
                        });
                    }else{
                        createModuleControls();
                        makeSortableModules();
                        canvas.show();
                    }


                } else {
                    $("#edit-source-code").prop('disabled', false);
                    message_grid_loading.remove();
                    canvas.show();
                }


                runFilter(true);
                canvas.addClass('ge-editing');
                addAllColClasses();

                createContainerControls();
                createRowControls();
                createColControls();
                makeSortable();
                makeSortableModules();
                switchLayout(curColClassIndex);

                settings.isFirstInit = true;
            }

            function initRow() {
                addAllColClasses();
                createModuleControls();
                createContainerControls();
                createRowControls();
                createColControls();
                makeSortable();
                makeSortableModules();
                switchLayout(curColClassIndex);
            }

            function deinit() {
                canvas.removeClass('ge-editing');

                canvas.find('.ge-tools-drawer').remove();

                $('.module-wrapper').each(function(){
                    this.innerHtml = '';
                });

                /*canvas.find('.has-module').each(function () {

                    var $this = $(this);
                    var modules = {"modules": []};
                    $this.find('> .module-wrapper').each(function () {
                        var $this = $(this);


                        var module = $this.find("[class*='module-']");

                        var module_type = module.data('module-type');
                        var module_id = module.data('module-id');


                        modules.modules.push(
                            {
                                id: module_id,
                                type: module_type
                            });


                    });

                    if (modules.modules.length > 0) {
                        $this.append(JSON.stringify(modules));
                    } else {
                        $this.removeClass('has-module');
                    }

                });
                canvas.find('.module-wrapper').remove();*/

                removeSortable();

                runFilter();
            }

            function createContainerControls() {
                canvas.find('.container, .container-fluid').each(function () {
                    var $container = $(this);
                    if ($container.find('> .ge-tools-drawer').length) return;

                    var $drawer = $('<div class="ge-tools-drawer" />').prependTo($container);

                    createTool($drawer, 'Move', 'ge-move', 'fa-arrows');
                    createTool($drawer, 'Settings', '', 'fa-cog', function () {
                        details.toggle();
                    });
                    createTool($drawer, 'Remove container', '', 'fa-trash', function () {
                        swal({
                                title: settings.warning_modal.row_delete[0],
                                text: settings.warning_modal.row_delete[1],
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#FF7043",
                                confirmButtonText: settings.warning_modal.row_delete[2],
                                cancelButtonText: settings.warning_modal.row_delete[3]
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    $container.slideUp(function () {
                                        $container.remove();
                                    });
                                }
                            });
                    });
                    createTool($drawer, 'Add row', 'ge-add-row', 'fa-plus-circle', function () {
                        $container.append(createRow());
                        initRow();
                    });


                    var details = createDetails($container, settings.row_classes).appendTo($drawer);
                });
            }

            function createRowControls() {
                canvas.find('.row').each(function () {
                    var row = $(this);
                    if (row.find('> .ge-tools-drawer').length) {
                        return;
                    }

                    var drawer = $('<div class="ge-tools-drawer" />').prependTo(row);
                    createTool(drawer, 'Move', 'ge-move', 'fa-arrows');
                    createTool(drawer, 'Settings', '', 'fa-cog', function () {
                        details.toggle();
                    });
                    settings.row_tools.forEach(function (t) {
                        createTool(drawer, t.title || '', t.className || '', t.iconClass || 'fa-wrench', t.on);
                    });
                    createTool(drawer, 'Remove row', '', 'fa-trash', function () {

                        swal({
                                title: settings.warning_modal.row_delete[0],
                                text: settings.warning_modal.row_delete[1],
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#FF7043",
                                confirmButtonText: settings.warning_modal.row_delete[2],
                                cancelButtonText: settings.warning_modal.row_delete[3]
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    row.slideUp(function () {
                                        row.remove();
                                    });
                                }
                            });

                    });
                    createTool(drawer, 'Add column', 'ge-add-column', 'fa-plus-circle', function () {
                        row.append(createColumn(3));
                        initRow();
                    });


                    var details = createDetails(row, settings.row_classes).appendTo(drawer);
                });
            }

            function createColControls() {
                canvas.find('.column').each(function () {
                    var col = $(this);
                    if (col.find('> .ge-tools-drawer').length) {
                        return;
                    }

                    var drawer = $('<div class="ge-tools-drawer" />').prependTo(col);

                    createTool(drawer, 'Move', 'ge-move', 'fa-arrows');

                    createTool(drawer, 'Make column narrower\n(hold shift for min)', 'ge-decrease-col-width', 'fa-minus', function (e) {
                        var colSizes = settings.valid_col_sizes;
                        var curColClass = colClasses[curColClassIndex];
                        var curColSizeIndex = colSizes.indexOf(getColSize(col, curColClass));
                        var newSize = colSizes[clamp(curColSizeIndex - 1, 0, colSizes.length - 1)];
                        if (e.shiftKey) {
                            newSize = colSizes[0];
                        }

                        setColSize(col, curColClass, Math.max(newSize, 1));
                    });

                    createTool(drawer, 'Make column wider\n(hold shift for max)', 'ge-increase-col-width', 'fa-plus', function (e) {
                        var colSizes = settings.valid_col_sizes;
                        var curColClass = colClasses[curColClassIndex];
                        var curColSizeIndex = colSizes.indexOf(getColSize(col, curColClass));
                        var newColSizeIndex = clamp(curColSizeIndex + 1, 0, colSizes.length - 1);
                        var newSize = colSizes[newColSizeIndex];


                        if (e.shiftKey) {
                            newSize = colSizes[colSizes.length - 1];
                        }


                        setColSize(col, curColClass, Math.min(newSize, MAX_COL_SIZE));
                    });


                    createTool(drawer, 'Settings', '', 'fa-cog', function () {
                        details.toggle();
                    });


                    settings.col_tools.forEach(function (t) {
                        createTool(drawer, t.title || '', t.className || '', t.iconClass || 'fa-wrench', t.on);
                    });

                    createTool(drawer, 'Remove col', '', 'fa-trash', function () {

                        swal({
                                title: settings.warning_modal.col_delete[0],
                                text: settings.warning_modal.col_delete[1],
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#FF7043",
                                confirmButtonText: settings.warning_modal.col_delete[2],
                                cancelButtonText: settings.warning_modal.row_delete[3]
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    col.animate({
                                        opacity: 'hide',
                                        width: 'hide',
                                        height: 'hide'
                                    }, 400, function () {
                                        col.remove();
                                    });
                                }
                            });

                    });

                    createTool(drawer, 'Add row', 'ge-add-row', 'fa-plus-circle', function () {


                        var row = createRow();


                        if (col.find('.module-wrapper').length || col.hasClass('has-module')) {
                            swal({
                                title: "Opps..",
                                text: "Nemuzete pridat row ke sloupci s modulem",
                                type: "error",
                                confirmButtonColor: "#2196F3",
                            });

                        } else {
                            col.append(row);
                            // row.append(createColumn(6)).append(createColumn(6));
                            initRow();
                        }


                    });


                    createTool(drawer, 'Add module', 'ge-add-module', 'fa-window-maximize', function () {


                        col_guid = $(this).closest('.column').data('guid');


                        $(this).attr('data-toggle', 'modal').attr('data-target', '#modules_modal');

                    });


                    var details = createDetails(col, settings.col_classes).appendTo(drawer);
                });
            }

            function createModuleControls() {


                canvas.find('.module-wrapper').each(function () {


                    var $this = $(this);

                    if ($this.find('> .ge-tools-drawer').length) {
                        return;
                    }

                    var drawer = $('<div class="ge-tools-drawer" />').prependTo($this);
                    createTool(drawer, 'Move', 'ge-move', 'fa-arrows');
                    createTool(drawer, 'Edit', 'ge-edit open-modules-edit-modal', 'fa-edit', function () {

                        var $this = $(this);
                        var $wrapper = $this.closest('.module-wrapper');

                        var moduleId = parseInt($wrapper.data('module-id'));
                        var tempId = $wrapper.data('temp-id');
                        var requestUrl = '';
                        var submitUrl = '';
                        var storedValues = getModule(tempId || moduleId);
                        var isModuleTemp = Boolean(tempId && getModule(tempId));

                        if (isModuleTemp) {
                            requestUrl = getModule(tempId)._helper.formUrl;
                            submitUrl = grideditorOpts.urls.modules.submit;
                        } else {
                            requestUrl = grideditorOpts.urls.modules.edit + "/" + moduleId;
                            submitUrl = grideditorOpts.urls.modules.submit + "/" + moduleId;
                        }

                        $.ajax({
                            url: requestUrl,
                            dataType: 'html',
                            type: 'get',
                            contentType: 'application/json',
                            success: function (response) {

                                var $modal = $('#modal-template').clone()
                                    .removeAttr('id')
                                    .on('hidden.bs.modal', function(){
                                        $modal.remove();
                                    });

                                $modal.find('.modal-body').html(response);
                                var $form = $modal.find('form');

                                // updating stored module
                                if (storedValues) {
                                    var fillFunction = function () {
                                        $form.find(':input').val(function (index, value) {
                                            if (this.type === 'checkbox') {
                                                this.checked = this.value === storedValues[this.name];
                                            }
                                            return storedValues[this.name] || value;
                                        });
                                    };

                                    fillFunction();

                                    // Sync - When form is ready, triggers event to fill form
                                    $form.on('admin:form-fill-ready', function (event, fill) {
                                        if (!fill) {
                                            fillFunction();
                                        } else {
                                            fill(storedValues);
                                        }
                                    });
                                }

                                $modal.find('.modal-submit').text('Upravit modul')
                                    .on('click', function(){
                                        $form.trigger('submit');
                                    });

                                $modal.modal('show');

                                $form.on('submit', function(e){
                                    e.preventDefault();

                                    Form.removeAllErrors($form);
                                    if (!$form.lock({ Spinner: SpinnerType.OVER })) return;

                                    var formData = getFormData($form);

                                    if (isModuleTemp) {
                                        formData['entity_module_name'] = getModule(tempId)._helper.moduleName;
                                        formData._helper = storedValues._helper;
                                    }

                                    $.ajax({
                                        url: submitUrl,
                                        dataType: 'json',
                                        type: 'post',
                                        data: formData,
                                        success: function (response) {

                                            $modal.modal('hide');
                                            $('#modules_modal').modal('hide');

                                            $wrapper.html(response.content);

                                            delete formData._token;
                                            setModule(tempId || moduleId, formData);

                                            createModuleControls();
                                        },
                                        error: function (jqXhr, textStatus, errorThrown) {
                                            if (jqXhr.status === 422) {
                                                Form.addErrors($form, jqXhr.responseJSON);
                                            } else {
                                                alert(textStatus);
                                            }
                                        }
                                    }).always(function () {
                                        $form.unlock();
                                    });
                                })

                            },
                            error: function (jqXhr, textStatus, errorThrown) {
                                //   console.log(errorThrown);
                            }
                        });



                        /*var $this = $(this);

                        var wrapper = $this.closest('.module-wrapper');

                        var data = wrapper.find('[class*="module-"]');

                        var module_id = data.data('module-id');
                        var module_type = data.data('module-type');

                        $('#modules-edit-modal').find('.modal-remote').load(('/admin/ajax-grideditor/module/' + module_type + '/modal/' + module_id), function () {
                            $('#modules-edit-modal').modal('show');
                        });*/


                    });
                    createTool(drawer, 'Delete', 'ge-delete delete-module', 'fa-trash');

                });


            }

            /**
             * Add module to grid
             *
             * @param {string} id
             * @param {string }url
             * @param {string} moduleName
             */
            function addModule(id, url, moduleName) {

                var $element = $(".column[data-guid='" + id + "']");

                // TODO: Fix this callback hell!
                // Load module configuration form.
                $.ajax({
                    url: url,
                    dataType: 'html',
                    type: 'get',
                    contentType: 'application/json',
                    data: {
                        pageId: settings.page_id
                    },
                    success: function (response) {

                        var $modal = $('#modal-template').clone()
                            .removeAttr('id')
                            .on('hidden.bs.modal', function(){
                                $modal.remove();
                            });

                        $modal.find('.modal-body').html(response);
                        var $form = $modal.find('form');

                        $modal.find('.modal-submit').text('Přidat modul')
                            .on('click', function(){
                                $form.trigger('submit');
                            });

                        $modal.modal('show');

                        // Submit module configuration form.
                        $form.on('submit', function(e){
                            e.preventDefault();

                            Form.removeAllErrors($form);
                            if (!$form.lock({ Spinner: SpinnerType.OVER })) return;

                            var formData = getFormData($form);
                            formData['entity_module_name'] = moduleName;

                            // Send to validation and get module preview.
                            $.ajax({
                                url: grideditorOpts.urls.modules.submit,
                                dataType: 'json',
                                type: 'post',
                                data: formData,
                                // Module preview.
                                success: function (response) {

                                    $modal.modal('hide');
                                    $('#modules_modal').modal('hide');

                                    var tempId = GUID();
                                    formData._helper = {
                                        tempId: tempId,
                                        moduleName: moduleName,
                                        formUrl: url
                                    };
                                    delete formData._token;
                                    setModule(tempId, formData);

                                    $('<div class="module-wrapper"></div>')
                                        .attr('data-temp-id', tempId)
                                        .html(response.content)
                                        .appendTo(
                                            $element.addClass('has-module')
                                        );

                                    createModuleControls();

                                },
                                // Form is not valid.
                                error: function (jqXhr, textStatus, errorThrown) {
                                    if (jqXhr.status === 422) {
                                        Form.addErrors($form, jqXhr.responseJSON);
                                    } else {
                                        alert(textStatus);
                                    }
                                }
                            }).always(function () {
                                $form.unlock();
                            });
                        })

                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        //   console.log(errorThrown);
                    }
                });


                /*var element = $(".column[data-guid='" + id + "']");
                if (element.find('.row').length > 0) {

                    alert('Nemuzete pridat modul s row');

                } else {

                    var json = JSON.stringify(
                        {
                            "module": {
                                "type": module_type,
                                "page_id": settings.page_id
                            }
                        }
                    );

                    $.ajax({
                        url: gridEditorUrl.modules.create + '/' + module_type,
                        dataType: 'text',
                        type: 'post',
                        contentType: 'application/json',
                        data: json,
                        success: function (data) {

                            var module_id = JSON.parse(data).id;
                            $('<div class="module-wrapper"></div>').appendTo(element).load(('/admin/ajax-grideditor/module/' + module_type + '/preview/' + module_id), function () {
                                createModuleControls();
                            });

                            element.addClass('has-module');
                            $('#modules_modal').modal('hide');
                        },
                        error: function (jqXhr, textStatus, errorThrown) {
                            //   console.log(errorThrown);
                        }
                    });


                }*/


            }


            $('.add-grid-module').off('click').on('click', function (e) {
                e.preventDefault();
                addModule(col_guid, this.href, $(this).data('module-type'));
            });


            /**
             * Refresh core function when load ajax
             */
            $(document).on('click', '.delete-module', function () {
                var $this = $(this);
                swal({
                        title: settings.warning_modal.module_delete[0],
                        text: settings.warning_modal.module_delete[1],
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF7043",
                        confirmButtonText: settings.warning_modal.module_delete[2],
                        cancelButtonText: settings.warning_modal.module_delete[3]
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            var $col = $this.closest('.has-module');
                            var $wrapper = $this.closest('.module-wrapper');

                            var moduleId = parseInt($wrapper.data('module-id'));
                            var tempId = $wrapper.data('temp-id');

                            forgetModule(tempId || moduleId);

                            $wrapper.remove();

                            if (!$col.find('.module-wrapper').length) {
                                $col.removeClass('has-module');
                            }
                        }
                    });


            });


            function createTool(drawer, title, className, iconClass, eventHandlers) {
                var tool = $('<a title="' + title + '" class="' + className + '"><span class="fa ' + iconClass + '"></span></a>')
                        .appendTo(drawer)
                    ;
                if (typeof eventHandlers == 'function') {
                    tool.on('click', eventHandlers);
                }
                if (typeof eventHandlers == 'object') {
                    $.each(eventHandlers, function (name, func) {
                        tool.on(name, func);
                    });
                }
            }


            function createDetails(container, cssClasses) {
                var detailsDiv = $('<div class="ge-details" />');

                var idBtn = $('<input class="ge-id" />')
                        .attr('placeholder', 'id')
                        .val(container.data('id'))
                        .attr('title', 'Set a unique identifier')
                        .appendTo(detailsDiv)
                    ;

                $(document).on('keyup', idBtn, function () {
                    container.attr('data-id', idBtn.val());
                });

                var classBtn = $('<input class="ge-class" />')
                        .attr('placeholder', 'class')
                        .val(container.data('class'))
                        .attr('title', 'Set a class')
                        .appendTo(detailsDiv)
                    ;

                classBtn.keyup(function () {
                    var $this = $(this);
                    container.attr('data-class', $this.val())
                });

                // If .container
                if (container.hasClass('container') || container.hasClass('container-fluid')) {
                    var $fluidSwitch = $('<input class="ge-fluid" />')
                        .prop('checked', container.hasClass('container-fluid'))
                        .attr('type', 'checkbox')
                        .appendTo(detailsDiv);

                    $fluidSwitch.after('fluid');

                    $fluidSwitch.change(function () {
                        //container.data('fluid', $(this).is(':checked'));
                        if ($(this).is(':checked')) {
                            container.addClass('container-fluid').removeClass('container');
                        } else {
                            container.removeClass('container-fluid').addClass('container');
                        }
                    });

                    var $layoutDropdown = $("<select id=\"layoutDropdown\" name=\"layoutDropdown\" />").appendTo(detailsDiv);

                    $("<option />", {value: '-' , text: '-'}).appendTo($layoutDropdown);
                    $.each(window.config_style, function(i, item){
                        $("<option />", {value: i , text: i}).appendTo($layoutDropdown);

                    });

                    $.each(JSON.parse(settings.layout_types), function (i, item) {
                        $("<option />", item).appendTo($layoutDropdown);
                    });


                    $layoutDropdown.find('option').each( function() {

                        var $this = $(this);

                        if ( container.data('layout-type') ==  $this.val()) {
                            $this.attr('selected', true);
                        }
                    });




                    $layoutDropdown.change(function () {
                        container.attr('data-layout-type', $(this).val());
                        console.log($layoutDropdown.val());
                        //noinspection JSAnnotator
                        $('#grid_style').val($layoutDropdown.val());
                    });

                }


                var classGroup = $('<div class="btn-group" />').appendTo(detailsDiv);
                cssClasses.forEach(function (rowClass) {
                    // var btn = $('<a class="btn btn-xs btn-default" />')
                    //         .html(rowClass.label)
                    //         .attr('title', rowClass.title ? rowClass.title : 'Toggle "' + rowClass.label + '" styling')
                    //         .toggleClass('active btn-primary', container.hasClass(rowClass.cssClass))
                    //         .on('click', function () {
                    //             btn.toggleClass('active btn-primary');
                    //             container.toggleClass(rowClass.cssClass, btn.hasClass('active'));
                    //         })
                    //         .appendTo(classGroup)
                    //     ;
                });

                return detailsDiv;
            }


            function addAllColClasses() {
                canvas.find('.column, div[class*="col-"]').each(function () {
                    var col = $(this);
                    col.addClass('column');
                });
            }


            /**
             * Return the column size for colClass, or a size from a different
             * class if it was not found.
             * Returns null if no size whatsoever was found.
             */
            function getColSize(col, colClass) {
                var sizes = getColSizes(col);
                for (var i = 0; i < sizes.length; i++) {
                    if (sizes[i].colClass == colClass) {
                        return sizes[i].size;
                    }
                }
                if (sizes.length) {
                    return sizes[0].size;
                }
                return null;
            }

            function getColSizes(col) {
                var result = [];
                colClasses.forEach(function (colClass) {
                    var re = new RegExp(colClass + '(\\d+)', 'i');
                    if (re.test(col.attr('class'))) {
                        result.push({
                            colClass: colClass,
                            size: parseInt(re.exec(col.attr('class'))[1])
                        });
                    }
                });
                return result;
            }

            function setColSize(col, colClass, size) {
                var re = new RegExp('(' + colClass + '(\\d+))', 'i');
                var reResult = re.exec(col.attr('class'));
                if (reResult && parseInt(reResult[2]) !== size) {
                    col.switchClass(reResult[1], colClass + size, 50);
                } else {
                    col.addClass(colClass + size);
                }
            }

            function makeSortable() {

                canvas.find('.row').sortable({
                    items: '> .column',
                    connectWith: '.ge-canvas .row',
                    handle: '> .ge-tools-drawer > .ge-move',
                    start: sortStart,
                    helper: 'clone',
                });

                canvas.find('.container, .container-fluid').sortable({
                    items: '> .row',
                    connectWith: '.ge-canvas .container,.ge-canvas .container-fluid',
                    handle: '> .ge-tools-drawer > .ge-move',
                    start: sortStart,
                    helper: 'clone',
                });

                canvas.sortable({
                    items: '> .container, > .container-fluid',
                    connectWith: '.ge-canvas',
                    handle: '> .ge-tools-drawer > .ge-move',
                    start: sortStart,
                    helper: 'clone',
                });

                function sortStart(e, ui) {
                    ui.placeholder.css({height: ui.item.outerHeight()});
                }
            }

            function makeSortableModules() {

                canvas.find('.column').sortable({
                    items: '> .module-wrapper',
                    connectWith: '.ge-canvas .column, .column',
                    handle: '> .ge-tools-drawer .ge-move',
                    start: sortStart,
                    receive: recieveFn,
                    helper: 'clone',
                });

                function recieveFn(event, ui) {
                    var sourceList = ui.sender;
                    var targetList = $(this);


                    if (sourceList.find('.module-wrapper').length <= 0) {
                        sourceList.removeClass('has-module');
                    }


                    targetList.addClass('has-module');

                }

                function sortStart(e, ui) {
                    ui.placeholder.css({height: ui.item.outerHeight()});
                }


            }

            function removeSortable() {
                $('.row, .container, .container-fluid, .column', canvas).each(function () {
                    // sortable plugin left style='display:block'
                    $(this).attr('style', null);

                    if ($(this).data("ui-sortable")) $(this).sortable('destroy');
                });
                canvas.sortable('destroy');
            }

            function createRow() {
                return $('<div class="row" />');
            }

            function createContainer() {
                return $('<div class="container" />');
            }

            function createColumn(size) {

                return $('<div/>')
                    .addClass(colClasses[curColClassIndex] + size)
                    // .addClass(colClasses.map(function (c) {
                    //     return c + size;
                    // }).join(' '))
                    .attr('data-guid', GUID());
            }

            /**
             * Run custom content filter on init and deinit
             */
            function runFilter(isInit) {
                if (settings.custom_filter.length) {
                    $.each(settings.custom_filter, function (key, func) {
                        if (typeof func == 'string') {
                            func = window[func];
                        }

                        func(canvas, isInit);
                    });
                }
            }


            /**
             * Wrap column content in <div class="ge-content"> where neccesary
             */

            // function doWrap(contents) {
            //     if (contents.length) {
            //         var container = createDefaultContentWrapper().insertAfter(contents.last());
            //         contents.appendTo(container);
            //         contents = $();
            //     }
            // }
            //
            // function createDefaultContentWrapper() {
            //     return $('<div/>')
            //         .addClass(' ge-content-type-' + settingge-contents.content_types[0])
            //         .attr('data-ge-content-type', settings.content_types[0])
            //         ;
            // }


            function switchLayout(colClassIndex) {
                curColClassIndex = colClassIndex;

                var layoutClasses = ['ge-layout-desktop', 'ge-layout-large', 'ge-layout-tablet', 'ge-layout-phone'];
                layoutClasses.forEach(function (cssClass, i) {
                    canvas.toggleClass(cssClass, i == colClassIndex);
                });


            }

            function getRTE(type) {
                return $.fn.gridEditor.RTEs[type];
            }

            function clamp(input, min, max) {
                return Math.min(max, Math.max(min, input));
            }

            baseElem.data('grideditor', {
                init: init,
                deinit: deinit,
                destroy: destroy,
                getModules: getModules
            });

            /**
             * Helper function for GUID generation.
             *
             * @returns {string}
             * @constructor
             */

            function S4() {
                return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
            }

            /**
             * Generate Global unique string.
             *
             * @returns {string}
             * @constructor
             */
            function GUID() {
                return (S4() + S4() + "-" + S4() + "-4" + S4().substr(0, 3) + "-" + S4() + "-" + S4() + S4() + S4()).toLowerCase();
            }

            /**
             * Get form input data as an object.
             * @param {$|HTMLFormElement} form
             * @return {object}
             */
            function getFormData(form){
                var data = $(form).serializeArray();
                var output = {};

                $.map(data, function(n, i){
                    var regex = /(\w+)\[(\w*)\]/g;
                    var match = regex.exec(n['name']);

                    if (match !== null) {
                        var name = match[1],
                            key = match[2];

                        if (output[name]) {
                            if (key.length) {
                                output[name][key] = n['value'];
                            } else {
                                output[name].push(n['value']);
                            }
                        } else {
                            if (key.length) {
                                output[name] = {};
                                output[name][key] = n['value'];
                            } else {
                                output[name] = [n['value']];
                            }

                        }
                    } else {
                        output[n['name']] = n['value'];
                    }

                });

                return output;
            }

            /**
             * Set module data.
             * @param {string|number} id
             * @param {object} data
             */
            function setModule(id, data) {
                modules[id] = data;
            }

            /**
             * Get module data.
             * @param {string|number} id
             * @return {object|undefined}
             */
            function getModule(id) {
                return modules[id];
            }

            /**
             * Get modules in grid.
             * @return {object}
             */
            function getModules() {
                var outputModules = {},
                    $modules = canvas.find('.module-wrapper');

                $modules.each(function(){
                    var $module = $(this),
                        moduleId;

                    if ($module.data('module-id')) {
                        moduleId = Number($(this).data('module-id'));

                        outputModules[moduleId] = modules[moduleId] ?
                            modules[moduleId] : false;
                    } else if ($module.data('temp-id')) {
                        moduleId = $module.data('temp-id');

                        if (modules[moduleId]) {
                            outputModules[moduleId] = modules[moduleId];
                        }
                    }
                });

                return outputModules;
            }

            /**
             * Forget module data.
             * @param {string|number} id
             */
            function forgetModule(id) {
                delete modules[id];
            }


            /**
             * Load different version and switch.
             * @param {number} id
             */
            function loadDifferentVersion(id) {
                var $switch = $('#version-switch');
                if (!$switch.lock()) {
                    return;
                }
                $.get(settings.urls.switchVersion + '/' + id)
                    .done(function (response) {
                        switchVersion(id, response.content);
                    })
                    .always(function () {
                        $switch.unlock();
                    });
            }


            /**
             * Switch version.
             * @param {number} id
             * @param {string} content
             */
            function switchVersion(id, content) {

                // hide and destroy old grid editor
                baseElem.hide();
                destroy();

                // set html of new version
                baseElem.html(content);

                // set version id
                $(settings.versionIdInput).val(id);

                // set active version
                for (var vi in settings.versions) {
                    settings.versions[vi].isActive = Number(settings.versions[vi].id) === Number(id);
                }

                // initialize and show grid editor
                baseElem.gridEditor(settings);
                baseElem.show();
            }

        });

        return self;

    };

    $.fn.gridEditor.RTEs = {};

})(jQuery);




