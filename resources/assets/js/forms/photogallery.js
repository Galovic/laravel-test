new Vue({
    el: '#photogalleries-form',

    data: {
        isPhotogalleryLoading: false,
        laravel: photogalleryFormData,
        title: '',
        url: '',
        seo: {
            title: '',
            description: ''
        }
    },

    /**
     * On page is ready
     */
    ready: function()
    {
        var vueContext = this;

        // Tags
        $('.tags-input').tagsinput();


        // Maxlength
        $('.maxlength').maxlength({
            alwaysShow: true
        });


        // Pickadate
        $('.pickadate').pickadate({
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
        $(".pickatime").pickatime({
            format: "H:i"
        });


        // Fancybox
        $('[data-popup="lightbox"]').fancybox({
            padding: 0
        });


        // CK Editor
        CKEDITOR.replace( 'editor-full',{
            filebrowserImageBrowseUrl: photogalleryFormData.filebrowserImageBrowseUrl,
            filebrowserImageUploadUrl: photogalleryFormData.filebrowserImageUploadUrl,
            filebrowserBrowseUrl: photogalleryFormData.filebrowserBrowseUrl,
            filebrowserUploadUrl: photogalleryFormData.filebrowserUploadUrl,
            removeDialogTabs: 'link:upload;image:upload',
            height: '400px'
        });

        // Image uploader
        $(".file-uploader").pluploadQueue({
            runtimes: 'html5, html4',
            url: this.laravel.uploadPhotoUrl,
            multipart_params: {
                '_token': window.csrf_token
            },
            chunk_size: '300Kb',
            unique_names: true,
            filters: {
                max_file_size: '5000Kb',
                mime_types: [{
                    title: "Povolené obrázky",
                    extensions: "jpg,jpeg,gif,png,bmp"
                }]
            },
            init: {
                FilesAdded: function (up, files) {},
                UploadComplete: function (up, files) {
                    vueContext.displayImages();
                    up.splice();
                }
            },
        });


        $(document).on('click', '.delete-photo', function (e) {
            e.preventDefault();
            vueContext.deletePhoto(this.href);
        });

        this.displayImages();
    },

    methods: {

        displayImages: function () {
            var vueContext = this;
            vueContext.isPhotogalleryLoading = true;

            $('#result').addClass('loading');

            $.ajax({
                type: "GET",
                url: this.laravel.photoListUrl,
                async: true,
                success: function (result) {
                    $('#result').removeClass('loading');
                    $('#result').html(result);

                    vueContext.isPhotogalleryLoading = false;

                    $('.editable').editable({
                        type: 'text',
                        inputclass: 'form-control',
                        url: vueContext.laravel.updatePhotoUrl,
                        emptytext: 'Nezadáno',
                        success: function (response, newValue) {
                            if (response.status == 'error' || response.result != 'ok') {
                                $.jGrowl('Při úpravě popisku nastala chyba!', {
                                    header: ' Chyba! ',
                                    theme: ' bg-danger  alert-styled-left alert-styled-custom-danger'
                                });
                            }
                            else {
                                $.jGrowl('Popisek byl úspěšně upraven!', {
                                    header: ' Úspěch ',
                                    theme: ' bg-teal  alert-styled-left alert-styled-custom-success'
                                });
                            }
                        }
                    });
                }
            });
        },


        deletePhoto: function(url)
        {
            var displayImages = this.displayImages;

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

                    $.ajax({
                        type: "DELETE",
                        url: url,
                        async: true,
                        dataType: "json",
                        success: function (result) {
                            if (result.result == 'ok')
                            {
                                $.jGrowl('Fotografie byla úspěšně smazána!', {
                                    header: ' Úspěch ',
                                    theme: ' bg-teal  alert-styled-left alert-styled-custom-success'
                                });
                            }
                            else
                            {
                                $.jGrowl('Při mazání fotografie nastala chyba!', {
                                    header: ' Chyba! ',
                                    theme: ' bg-danger  alert-styled-left alert-styled-custom-danger'
                                });
                            }
                            vueContext.displayImages();
                        }
                    });
                }
            );



        },

        /**
         * On title is changed
         *
         * @param title
         */
        titleChanged: function(title){
            this.title = title;

            if(!this.url.length){
                this.url = this.makeUrlFriendly(title);

            }

            if(!this.seo.title.length){
                this.seo.title = title;
            }
        },


        /**
         * Transform text to URL friendly format
         *
         * @param text
         */
        makeUrlFriendly: function(text){
            // delete diacritics
            var diacritics  = "áäãàâčçďéěẽèëêíìïîĺľňñóôőöòŕšťúùůűüûýřžÁÄČĎÉĚÍĹĽŇÓÔŐÖŔŠŤÚŮŰÜÝŘŽ·/_,:; ";
            var replace     = "aaaaaccdeeeeeeiiiillnnooooorstuuuuuuyrzAACDEEILLNOOOORSTUUUUYRZ-------";

            text = text.trim();
            var out = '';

            for(var ci = 0 ; ci < text.length ; ci++){
                var char = text[ci];
                var charIndex = diacritics.indexOf(char);

                if (charIndex != -1){
                    char = replace.charAt(charIndex);
                }

                out += char;
            }

            return out.toLowerCase().replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-') // collapse dashes
                .replace(/^-+|-+$/g, ''); // remove dashes at start and end of the string
        },


        /**
         * On SEO description input keydown
         *
         * @param e
         */
        onSeoDescriptionKeyDown: function(e){
            if (e.keyCode == 13 && !e.shiftKey)
            {
                e.preventDefault();
            }
        }
    }

});