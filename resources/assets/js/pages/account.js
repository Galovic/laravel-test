new Vue({
    el: '#page-account-edit',
    data: {
        defaultThumbnail: accountFormData.defaultThumbnailSrc,
        imageSrc: accountFormData.imageSrc,
        imageSelected: accountFormData.imageSelected
    },
    computed: {
        removeImageValue: function () {
            return this.imageSelected ? 'false' : 'true';
        }
    },
    methods: {
        openFileInput: function () {
            $('#image-input').click();
        },
        removeImage: function () {
            this.imageSrc = this.defaultThumbnail;
            this.imageSelected = false;
            $('#image-input').val('');
        },
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
        // Maxlength
        $('.maxlength').maxlength({
            alwaysShow: true
        });
    }
});