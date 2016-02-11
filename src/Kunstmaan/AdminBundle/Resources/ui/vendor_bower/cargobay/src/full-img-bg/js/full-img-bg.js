/* ==========================================================================
   Full image background fallback with Backstretch
   https://github.com/srobbin/jquery-backstretch

   Initialize:
   cargobay.backstretch.init();
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.backstretch = (function($, window, undefined) {

    var init;

    init = function() {
	if($('.full-img-bg').length){
	    var imageUrl = $('.full-img-bg').data("backstretch-img");

	    $('.full-img-bg').backstretch(imageUrl);
	}
    };

    return {
	init: init
    };

}(jQuery, window));
