/** touchSwipe v1.6.9 **/

/**
 * JCAROUSEL INIT
 */
$(".jcarousel-wrapper").swipe({
    swipeLeft: function (event, direction, distance, duration, fingerCount) {
        $('.jcarousel-control-prev').trigger('click');
    },
    swipeRight: function (event, direction, distance, duration, fingerCount) {
        $('.jcarousel-control-next').trigger('click');
    },
    threshold: 30,
    excludedElements: "label, button, input, select, textarea, .noSwipe"
});

/**
 *  BOOTSTRAP CAROUSEL INIT
 */
$(".carousel").swipe({
    swipeLeft: function (event, direction, distance, duration, fingerCount) {
        $('.left.carousel-control').trigger('click');
    },
    swipeRight: function (event, direction, distance, duration, fingerCount) {
        $('.right.carousel-control').trigger('click');
    },
    threshold: 30,
    excludedElements: "label, button, input, select, textarea, .noSwipe"
});

