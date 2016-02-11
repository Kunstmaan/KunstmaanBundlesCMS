/* ==========================================================================
   Tabs

   Initialize:
   cargobay.tabs.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE9 and up
   ========================================================================== */


var cargobay = cargobay || {};

cargobay.tabs = (function($, window, undefined) {

    var activateTabs, updatePanes;

    // Config
    var tabClass = 'js-tab-link',
        tabClassActive = 'tab-link--active',
        paneClassActive = 'tab-pane--active';

    // Main tabs function
    activateTabs = function() {
        $('.' + tabClass).on('click touchstart mousedown', function(e) {
            e.preventDefault();
        }).on('touchend mouseup', function() {
            var $this = $(this),
                dataTarget = $this.data('target'),
                $target = dataTarget ? $(dataTarget) : $($this.attr('href')),
                currentTargetIsActive = $target.hasClass(tabClassActive);

            if (currentTargetIsActive) {
                // Target is active, so return
                return false;

            } else {
                // Update tabs
                $this.siblings('.' + tabClassActive).removeClass(tabClassActive);
                $this.addClass(tabClassActive);

                // Update panes
                updatePanes($target);
            }
        });
    };

    // Show target pane
    updatePanes = function($target) {
        $target.siblings().removeClass(paneClassActive);
        $target.addClass(paneClassActive);
    };

    return {
        init: activateTabs
    };

}(jQuery, window));
