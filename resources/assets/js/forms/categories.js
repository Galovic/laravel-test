new Vue({
    el: '#categories-form',

    data: {
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
        // Switchery
        $('.switchery').each(function(){
            new Switchery(this);
        });

        // Tagsinput
        var $tagsinput = $('.tags-input');
        $tagsinput.tagsinput({
            cancelConfirmKeysOnEmpty: true
        });

        $('.bootstrap-tagsinput').on('keypress', function(e){
            if (e.keyCode == 13){
                e.preventDefault();
            }
        });

        // Maxlength
        $('.maxlength').maxlength({
            alwaysShow: true
        });
    },

    methods: {

        /**
         * On title is changed
         *
         * @param title
         */
        titleChanged: function(title){
            this.title = title;

            if (!this.url.length) {
                this.url = this.makeUrlFriendly(title);
            }

            if(!this.seo.title.length){
                this.seo.title = title;
            }

            if(!this.seo.description.length){
                this.seo.description = title;
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
        preventKeydownEnter: function(e){
            console.log(this, e);
            if (e.keyCode == 13)
            {
                e.preventDefault();
            }
        }
    }

});