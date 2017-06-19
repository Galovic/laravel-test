/**
 * This file is created for simplo.cz
 * @copyright (c) 2017, simplo.cz
 */

// Bootstrap v3.3.7
require('./bootstrap/bootstrap');

// JCarousel v0.3.4
require('./jcarousel/jquery.jcarousel.min');
require('./jcarousel/jcarousel.responsive');

// touchSwipe
require('./touchSwipe/touchSwipe');
require('./touchSwipe/touchSwipe-initalization');

// svgFallback
require('./scripts/svgFallback');

// PhotoSwipe
import PhotoSwipeInitializator from './photoswipe/photoswipe-initializator';
PhotoSwipeInitializator('.my-gallery');