/* ==========================================================================
   Toggle

   Initialize:
   cargobay.toggle.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE10 and up
   ========================================================================== */


var cargobay = cargobay || {};


cargobay.toggle = (function(window, undefined) {

    var init, findParent, addMultiEventistener, toggle, show, hide;

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

    // Add multiple listeners
    addMultiEventistener = function(el, s, fn) {
	var evts = s.split(' ');

	for (var i=0, iLen=evts.length; i<iLen; i++) {
	    el.addEventListener(evts[i], fn, false);
	}
    };


    // Main toggle function
    toggle = function() {
	[].forEach.call( document.querySelectorAll('.' + btnClass), function(btn) {
	    addMultiEventistener(btn, 'click touchstart mousedown', function(e){
		e.preventDefault();
	    });

	    addMultiEventistener(btn, 'touchend mouseup', function(e){
		var target = document.querySelectorAll(btn.getAttribute('data-target'))[0],
		    targetContent = target.querySelectorAll('.' + itemContentClass)[0],
		    targetContentHeight = targetContent.offsetHeight,
		    currentTargetIsActive = target.classList.contains(itemClassActive),
		    hideOthers = btn.getAttribute('data-hide-others');

		// Check animation speed.
		animationDuration = (btn.getAttribute('data-duration') !== null) ? btn.getAttribute('data-duration') : defaultAnimationDuration;

		if(currentTargetIsActive) {
		    // Target is active, so hide it
		   hide(btn, target);

		} else {

		    // Check if the others have to be cleared.
		    if(hideOthers){
			var ownTarget = btn.getAttribute('data-target');
			var currentLevel = btn.getAttribute('data-level');

			[].forEach.call( document.querySelectorAll('.' + btnClass + '[data-level="' + currentLevel + '"]'), function(btn) {
			    var btnTarget  = btn.getAttribute('data-target');
			    if(ownTarget !== btnTarget){
				if(btn.classList.contains(btnClassActive)){
				    hideFast(btn, document.querySelectorAll(btnTarget)[0]);
				}
			    }
			});
		    }

		    // Update target
		    show(this, target, targetContent, targetContentHeight);
		}
	    });
	});
    };


    // Show an item
    show = function(btn, target, targetContent, height) {
	btn.classList.add(btnClassActive);

	Velocity({
	    elements: target,
	    properties: {
		height: height
	    },
	    options: {
		duration: animationDuration,
		complete: function() {
		    target.classList.add(itemClassActive);
		    target.style.height = 'auto';
		}
	    }
	});
    };


    // Hide an item
    hide = function(btn, target) {
	Velocity({
	    elements: target,
	    properties: {
		height: 0
	    },
	    options: {
		duration: animationDuration,
		complete: function() {
		    btn.classList.remove(btnClassActive);
		}
	    }
	});

	target.classList.remove(itemClassActive);
    };

    // Quickly hide another open item on the same level when hide-others is true.
    hideFast = function(btn, target) {
	btn.classList.remove(btnClassActive);

	target.style.height = 0;
	target.classList.remove(itemClassActive);
    };


    return {
	init: init
    };

}(window));