(function ($, p) {
    'use strict';

    /**
     * ElementLock class
     *
     * @param {Element} element
     * @param {LockOptions} options
     * @constructor
     */
    function ElementLock(element, options) {
        this.element = element;
        this.options = options;
        this.usedLock = null;
        this.originalIconClass = null;

        this._init();
    }

    // prototype shortcut
    p = ElementLock.prototype;

    /**
     * Initialize element lock
     * @private
     */
    p._init = function () {
        if (typeof ElementLock.elementLocks === 'undefined') {
            ElementLock.elementLocks = {};
        }

        this._lock();
    };

    /**
     * Lock element
     * @private
     * @returns {boolean}
     */
    p._lock = function(){
        var $element,
            spinner = this.options ? this.options.spinner : undefined;

        if(typeof spinner === 'undefined') spinner = SpinnerType.AUTO;

        if (this.options && this.options.key) {
            if (!ElementLock.elementLocks[this.options.key]) {
                ElementLock.elementLocks[this.options.key] = [];
            }

            ElementLock.elementLocks[this.options.key].push(this.element);
        }

        if(spinner === SpinnerType.NONE) return true;

        $element = $(this.element);

        if(spinner === SpinnerType.AUTO || spinner === SpinnerType.ICON) {
            var $icon = $element.find('> .fa');
            if ($icon.length) {
                this.originalIconClass = $icon.first().attr('class');

                $icon.first().attr('class', 'fa fa-refresh fa-spin');

                this.usedLock = SpinnerType.ICON;
            }
        }

        if((spinner === SpinnerType.AUTO && !this.usedLock) || spinner ===  SpinnerType.OVER) {
            var zIndex = $element.css("z-index") || 0;

            var $hover = $('<div/>', {'class': 'element-lock-hover'})
                .css("zIndex", zIndex + 1);

            var $inner = $('<div />').addClass('lock-inner')
                .append("<i class='fa fa-spinner fa-spin'></i>");

            if (this.options && this.options.text) {
                $inner.append(
                    $('<span />').text(this.options.text).addClass('spinner-text')
                );
            }

            $element.append($hover.append($inner));

            this.usedLock = SpinnerType.OVER;
        }

        $element.css({"position":"relative"});

        return true;
    };

    /**
     * Unlock and destroy element
     */
    p.unlock = function() {
        var $element = $(this.element);

        switch (this.usedLock) {
            case SpinnerType.ICON:
                $element.find('> .fa').first().attr('class', this.originalIconClass);
                break;

            case SpinnerType.OVER:
                $element.find('> .element-lock-hover').remove();
                break;
        }

        this._destroy();
    };

    /**
     * Destroy element lock
     * @private
     */
    p._destroy = function () {
        $(this.element).removeData('lock');
    };

    /**
     * Unlock all element under given key
     * @static
     * @param {String} key
     */
    ElementLock.unlock = function(key){
        if(!ElementLock.isLocked(key)) {
            return;
        }

        for (var ei in ElementLock.elementLocks[key]) {
            if (Object.prototype.hasOwnProperty.call(ElementLock.elementLocks[key], ei)) {
                var element = ElementLock.elementLocks[key][ei],
                    lock = $(element).data('lock');

                if (lock) {
                    lock.unlock();
                }
            }
        }

        delete ElementLock.elementLocks[key];
    };

    /**
     * Is key locked
     * @static
     * @param {String} key
     * @returns {Boolean}
     */
    ElementLock.isLocked = function(key){
        return Boolean(window.elementLocks && window.elementLocks[key]);
    };

    /**
     * Enum for types of spinner
     * @enum {number}
     * @readonly
     */
    window.SpinnerType = {
        NONE: 0,
        AUTO: 1,
        ICON: 2,
        OVER: 3
    };

    /**
     * @typedef {object} LockOptions
     * @property {SpinnerType|Number} spinner
     * @property {String} key
     */


    /**
     * Creates ElementLock and locks element
     *
     * @param {LockOptions=} options
     * @returns {boolean}
     */
    $.fn.lock = function (options) {
        if (typeof options === 'object' && options.key && ElementLock.isLocked(options.key)) {
            return false;
        }

        var locked = false;

        this.each(function () {
            var $element = $(this);
            var lock = $element.data('lock');
            if (!lock) {
                $element.data('lock', (lock = new ElementLock(this, options)));
            } else {
                locked = true;
            }
        });

        return !locked;
    };

    /**
     * Unlocks element
     */
    $.fn.unlock = function (key) {
        if (typeof key === 'string') {
            ElementLock.unlock(key);
        } else {
            this.each(function () {
                var lock = $(this).data('lock');

                if (lock) {
                    lock.unlock();
                }
            });
        }

        return true;
    };
}(jQuery));