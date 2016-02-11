/* ==========================================================================
   Toggle

   Initialize:
   cargobay.toggle.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE9 and up
   ========================================================================== */


var cargobay = cargobay || {};

cargobay.toggle = (function($, window, undefined) {

    var init, toggle, show, hide, hideFast;

    // Config
    var defaultAnimationDuration = 150,
	animationDuration = 0,
	btnClass = 'js-toggle-btn',
	btnClassActive = 'toggle-btn--active',
	itemClassActive = 'toggle-item--active',
	itemContentClass = 'toggle-item__content';


    // Init
    init = function() {
	toggle();
    };


    // Main toggle function
    toggle = function() {
	$('.' + btnClass).on('click touchstart mousedown', function(e) {
	    e.preventDefault();
	}).on('touchend mouseup', function() {
	    var $this = $(this),
		$target = $($this.data('target')),
		$targetContent = $target.find('.' + itemContentClass),
		targetContentHeight = $targetContent.height(),
		currentTargetIsActive = $target.hasClass(itemClassActive),
		hideOthers = $this.data('hide-others');

	    // Check if custom animation duration has been set.
	    animationDuration = ($this.data('duration') !== undefined) ? $this.data('duration') : defaultAnimationDuration;


	    if(currentTargetIsActive) {
		// Target is active, so hide it
	       hide($this, $target);

	    } else {

		// Check if others have to be cleared.
		if(hideOthers){
		    var ownTarget = $this.data('target');
		    var currentLevel = $this.data('level');

		    $.each($('.'+ btnClass +'[data-level="' + currentLevel + '"]'), function(index, value){
			if(ownTarget !== $(value).data('target')){
			    $value = $(value);
			    var smTarget = $(value).data('target');

			    if($value.hasClass(btnClassActive)){
				// Clear others
				hideFast($value, $(smTarget));
			    }
			}
		    });
		}

		// Update target
		show($this, $target, $targetContent, targetContentHeight);
	    }
	});
    };


    // Show an item
    show = function($btn, $target, $targetContent, height) {
	$btn.addClass(btnClassActive);

	$target.velocity({
	    height: height
	}, {
	    duration: animationDuration,
	    complete: function() {
		$target.css('height', 'auto');
		$target.addClass(itemClassActive);
	    }
	});
    };


    // Hide an item
    hide = function($btn, $target) {
	$target.velocity({
	    height: 0
	}, {
	    duration: animationDuration,
	    complete: function() {
		$btn.removeClass(btnClassActive);
	    }
	});

	$target.removeClass(itemClassActive);
    };

    hideFast = function($btn, $target) {
	$btn.removeClass(btnClassActive);

	$target.css('height', 0);
	$target.removeClass(itemClassActive);
    };

    return {
	init: init
    };

}(jQuery, window));