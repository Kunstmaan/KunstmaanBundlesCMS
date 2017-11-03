var hologramKunstmaan = hologramKunstmaan || {};

hologramKunstmaan.app = (function(window, undefined) {

    var init,
        scrollOffset = 70,
        scrollSpeed = 300;

    init = function() {
        hologramToggle.toggle.init();

        // Update selected menu item on scrolling
        $('.styleguide__content-container > h1').waypoint({
            offset: scrollOffset,
            handler: function() {
                selectMenuItem('#' + $(this.element).attr('id'));
            }
        });

        // Initially select menu item
        if (window.location.hash) {
            setTimeout(function () {
                selectMenuItem(window.location.hash);
            }, 0);
        }

        // Smoothly scroll to clicked menu item content
        $('.styleguide__block-navigation__item').on('click', function (event) {
            var hash = $(this).attr('href'),
                pos = Math.min($(hash).offset().top - scrollOffset, $(document).height() - $(window).height() - scrollOffset);

            event.preventDefault();

            $('html,body').stop().animate({scrollTop : pos}, scrollSpeed, function (){
                selectMenuItem(hash);
            });
        });
    };

    selectMenuItem = function(hash) {
        $('.styleguide__block-navigation__item').removeClass('selected');
        $('.styleguide__block-navigation__item[href="' + hash + '"]').addClass('selected');
    };

    return {
        init: init
    };

}(window));

document.addEventListener("DOMContentLoaded", function(event) {
    hologramKunstmaan.app.init();
});
