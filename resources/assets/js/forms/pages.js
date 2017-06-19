new Vue({
    el: '#form_edit_pages', // id of form element - important!
    data: {
        defaultThumbnail: grideditorOpts.urls.defaultThumbnail,
        imageSrc: grideditorOpts.urls.imageSrc,
        imageSelected: Boolean(grideditorOpts.urls.imageSrc)
    },
    computed: {
        removeImageValue: function () {
            return this.imageSelected ? 'false' : 'true';
        }
    },
    methods: {

        /**
         * Open file input dialog
         */
        openFileInput: function () {
            $('#image-input').click();
        },

        /**
         * Remove image
         */
        removeImage: function () {
            this.imageSrc = this.defaultThumbnail;
            this.imageSelected = false;
            $('#image-input').val('');
        },

        /**
         * Create and show preview of input image
         * @param event
         */
        previewThumbnail: function (event) {
            var input = event.target;

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = function (e) {
                    vm.imageSrc = e.target.result;
                    vm.imageSelected = true;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    },
    created: function () {
        if (!this.imageSrc) {
            this.imageSrc = this.defaultThumbnail;
        }
    },
    ready: function () {
        var self = this,
            $inputPageModules = $('#page_modules');

        // Tags
        $('.tags-input').tagsinput({
            confirmKeys: [13, 44]
        });

        var gridEditorOptions = {
            new_row_layouts: [[12],
                [6, 6],
                [4, 4, 4],
                [3, 3, 3, 3],
                [2, 2, 2, 2, 2, 2],
                [2, 8, 2],
                [4, 8],
                [8, 4]],
            message_loading: 'Zpracovává se',
            page_id: grideditorOpts.pageId,
            layout_types: '[]',
            versions: grideditorOpts.versions,
            urls: grideditorOpts.urls,
            versionIdInput: '#page-content-id'
        };

        // Get initial modules
        if ($inputPageModules.val()) {
            gridEditorOptions.modules = JSON.parse($inputPageModules.val());
        }

        // Initialize grid editor
        $('#grid').gridEditor(gridEditorOptions);

        // Uniform
        $(".file-styled").uniform({
            fileButtonHtml: '<i class="fa fa-image"></i>',
            wrapperClass: 'bg-teal-400'
        });

        // Switchery
        $('.switchery').each(function(){
            new Switchery(this);
        });

        // Pickadate
        $('.pickadate-format').pickadate({
            monthsFull: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
            weekdaysShort: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
            today: 'Dnes',
            clear: 'Smazat',
            close: 'Zavřít',
            format: 'dd.mm.yyyy',
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
            firstDay: 1
        });

        // Pickatime
        $("#anytime-time,  #anytime-time-unpublish").pickatime({
            format: "H:i"
        });

        // Select2
        $('.select').select2({
            minimumResultsForSearch: Infinity
        });

        // Submit - save and leave
        $('#pages-form-submit').click(function (e) {
            e.preventDefault();

            var $form = $(this).closest('form');
            var $grid = $('#grid');

            $inputPageModules.val(JSON.stringify($grid.gridEditor('getModules')));
            $('#page_content').val($grid.gridEditor('getHtml'));

            $form.submit();
        });

        // Save - without leaving
        $('#pages-form-save').click(function (e) {
            e.preventDefault();

            var $form = $(this).closest('form');

            Form.removeAllErrors($form);

            $form.lock({
                spinner: SpinnerType.OVER,
                text: 'Ukládám...'
            });

            var $grid = $('#grid');
            $inputPageModules.val(JSON.stringify($grid.gridEditor('getModules')));
            $('#page_content').val($grid.gridEditor('getHtml'));

            var data = new FormData($form.get(0));

            $.ajax($form.attr('action'), {
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false
            }).done(function(response){
                $.jGrowl(response.message, {
                    header: 'Úspěch',
                    theme: 'bg-teal'
                });

                if (response.new_content) {
                    $grid.hide();
                    $grid.gridEditor('destroy');
                    $grid.html(response.new_content);
                    gridEditorOptions.versions = response.versions;
                    $grid.gridEditor(gridEditorOptions);
                    $grid.show();
                }

            }).fail(function(response) {
                if (response.status === 422) {
                    Form.addErrors($form, response.responseJSON);
                }
                $.jGrowl('Nepodařilo se uložit změny.', {
                    header: 'Chyba',
                    theme: 'bg-danger'
                });
            }).always(function () {
                $form.unlock();
            });
        });

        var $inputType = $('#input-type');
        var $viewInput = $('#view-input-wrapper');

        var typeChanged = function(){
            if(Number($inputType.find('option:selected').data('need-view')) === 1){
                $viewInput.show();
            }else{
                $viewInput.hide();
            }
        };

        $inputType.on('change', typeChanged);
        typeChanged();
    }
});