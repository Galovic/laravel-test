new Vue({
    el: '#wrapper',

    data: {
        /** @type {String} */
        workingDir: '/',
        /** @type {String} */
        type: null,
        /** @type {Boolean} */
        showList: false,
        /** @type {String} */
        sortType: 'alphabetic',
        /** @type {Array<String>} */
        parentDirectories: [],
        /** @type {Array<Object>} */
        files: []
    },

    // METHODS
    methods: {

        /**
         * Load items
         */
        loadItems: function () {
            var self = this;

            this.performRequest(filemanagerConfig.urls.items,
                {
                    show_list: this.showList,
                    sort_type: this.sortType,
                    working_dir: this.workingDir
                }
            ).done(function (response) {
                self.files = response.items;
            });
        },

        /**
         * Go to parent directory
         */
        goToParentDir: function () {
            this.workingDir = this.parentDirectories.pop();
            this.loadItems();
        },

        /**
         * Confirm file
         */
        openFile: function (path) {
            if (window.opener) {
                window.opener.CKEDITOR.tools.callFunction(this.getUrlParam('CKEditorFuncNum'), path);
                window.close();
            }
        },

        /**
         * Open directory
         */
        openDirectory: function (path) {
            this.parentDirectories.push(this.workingDir);
            this.workingDir = path;
            this.loadItems();
        },

        /**
         * Get value of specified parameter
         * @param {String} parameter
         * @return {String|null}
         */
        getUrlParam: function (parameter) {
            var reParam = new RegExp('(?:[\?&]|&)' + parameter + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);
            return ( match && match.length > 1 ) ? match[1] : null;
        },

        /**
         * Perform jQuery request
         * @param {String} url
         * @param {Object=} parameter
         * @param {String=} type
         * @returns {jqXHR}
         */
        performRequest: function (url, parameter, type) {
            var data = this.getDefaultParameters(),
                self = this;

            if (typeof parameter !== 'undefined' && parameter !== null) {
                $.each(parameter, function (key, value) {
                    data[key] = value;
                });
            }

            var $body = $('body');
            $body.lock({
                spinner: SpinnerType.OVER
            });

            return $.ajax({
                type: 'GET',
                dataType: type || 'json',
                url: url,
                data: data,
                cache: false
            }).fail(function (jqXHR) {
                self.displayErrorResponse(jqXHR);
            }).always(function () {
                $body.unlock();
            });
        },

        /**
         * Display request error response.
         * @param {jqXHR} jqXHR
         */
        displayErrorResponse: function (jqXHR) {
            bootbox.alert('<div style="max-height:50vh;overflow:scroll;">' + jqXHR.responseText + '</div>');
        },

        /**
         * Get default parameters of request.
         * @returns {{working_dir: String, type: String}}
         */
        getDefaultParameters: function () {
            return {
                working_dir: this.workingDir,
                type: this.type
            }
        },

        /**
         * Toggle showList property.
         */
        toggleShowList: function () {
            this.showList = !this.showList;
        },

        /**
         * Change sort type and reload items
         * @param newSortType
         */
        changeSortType: function (newSortType) {
            this.sortType = newSortType;
            this.loadItems();
        },

        /**
         * Show upload modal
         */
        showUploadModal: function () {
            $('#uploadModal').modal('show');
        },

        /**
         * Upload selected files
         */
        uploadFiles: function () {
            var $uploadForm = $('#uploadForm'),
                self = this;

            $uploadForm.lock({
                spinner: SpinnerType.OVER
            });

            $uploadForm.ajaxSubmit({
                success: function () {
                    $uploadForm.unlock();
                    $('#uploadModal').modal('hide');
                    $('input#upload').val('');
                    self.openDirectory('/uploaded');
                },
                error: function (jqXHR) {
                    $uploadForm.unlock();
                    self.displayErrorResponse(jqXHR);
                }
            });
        }

    },

    // COMPUTED
    computed: {
        /**
         * Is in root working directory?
         * @return {boolean}
         */
        isInRootDir: function () {
            return this.workingDir === '/';
        }
    },

    // FILTERS
    filters: {
        truncate: function(string, value) {
            if (string.length < value) return string;
            return string.substring(0, value) + '...';
        }
    },

    // ON DOM READY
    ready: function () {
        this.type = this.getUrlParam('type');
        bootbox.setDefaults({locale: filemanagerConfig.lang['locale-bootbox']});
        this.loadItems();
        this.performRequest(filemanagerConfig.urls.errors, null, 'text')
            .done(function (data) {
                var response = JSON.parse(data);
                for (var i = 0; i < response.length; i++) {
                    $('#alerts').append(
                        $('<div>').addClass('alert alert-warning')
                            .append($('<i>').addClass('fa fa-exclamation-circle'))
                            .append(' ' + response[i])
                    );
                }
            });

        $('#upload-btn').on('click', this.uploadFiles);
    }
});