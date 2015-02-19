var hologramKunstmaan = hologramKunstmaan || {};

hologramKunstmaan.scrollspy = (function(window, undefined) {

    // Functions
    var init, hasToc, updateScroll, updateResize, setupMenu, updateMenu, setupMenuItems, updateMenuItems;


    // Cachable vars
    var currentOffset, largeScreen,
        menu, menuStartOffset,
        mobileMenu, mobileMenuStartOffset, offsetCorrection, mobileMenuSelect,
        lastId, menuItems, scrollItems;


    // Check
    hasToc = function() {
        var toc = document.getElementById('holo-toc-kunstmaan'),
            tocStyle = window.getComputedStyle(toc, null),
            tocVisible = tocStyle.getPropertyValue('display');

        if(toc && toc.style.display !== 'none') {
            return true;
        } else {
            return false;
        }
    };


    // Init
    init = function() {

        if(hasToc()) {
            setupMenuItems();

            // Start scroll
            //updateScroll();
        }
    };


    // Bind to scroll
    updateScroll = function() {

        // if(hasToc()) {
        //     currentOffset = $(window).scrollTop();

        //     updateMenuItems();
        // }
    };


    // Bind to resize
    updateResize = function() {

        // if(hasToc()) {
        //     // Setup scroll
        //     updateScroll();
        // }
    };


    // Setup menu
    setupMenu = function() {
        // // Get menu
        // menu = document.getElementById('holo-toc-kunstmaan__nav');

        // // Get start-offset
        // menuStartOffset = menu.offsetTop;

        // // Set fixed width
        // menu.style.width = menu.offsetWidth + 'px';

        // console.log(menu);
        // console.log(mobileMenuStartOffset);
        // console.log(menu.offsetWidth);
    };


    // Setup menu items
    setupMenuItems = function() {
        // All menu items
        menuItems = menu.find('.js-toc__nav__link');

        // Anchors corresponding to menu items
        scrollItems = menuItems.map(function() {
            var item = $($(this).attr('href'));

            if(item.length) {
                return item;
            }
        });

        // Scroll to on click
        menuItems.on('click', function(e) {
            e.preventDefault();

            var target = $(this).attr('href');

            $(target).velocity('scroll', {
                duration: 500,
                offset: -20
            });
        });
    };


    // Update menu items (on scroll)
    updateMenuItems = function() {
        // // Get id of current scroll item
        // var cur = scrollItems.map(function() {

        //     // small correction on offset because of fixed toc
        //     offsetCorrection = (mobileMenu.hasClass('toc__nav--fixed')) ? mobileMenu.outerHeight() : mobileMenu.outerHeight() * 2;

        //     if($(this).offset().top < currentOffset + 50 + offsetCorrection) {
        //         return this;
        //     }
        // });

        // // Get the id of the current element
        // cur = cur[cur.length-1];
        // var id = cur && cur.length ? cur[0].id : '';

        // if (lastId !== id) {
        //     lastId = id;
        //     // Set/remove active class
        //     menuItems.removeClass('toc__nav__link--active');
        //     menuItems.filter('[href=#' + id + ']').addClass('toc__nav__link--active');

        //     if(id === '') {
        //         mobileMenuSelect.val('default');
        //     } else {
        //         mobileMenuSelect.val('#' + id);
        //     }

        // }
    };


    // Return
    return {
        init: init,
        updateScroll: updateScroll,
        updateResize: updateResize
    };

}(window));
