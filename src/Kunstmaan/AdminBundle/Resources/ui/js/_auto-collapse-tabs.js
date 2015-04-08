var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.autoCollapseTabs = (function($, window, undefined) {

    var $tabs, $btnMore, $dropdown,
        dropdownItems, tabsHeight, children, singleTabHeight;

    init = function() {
        $tabs = $('.js-auto-collapse-tabs');
        $btnMore = $('.tab__more');
        $dropdown = $('#collapsed');
        singleTabHeight = $tabs.find('li:first-child').innerHeight(); // get single height

        doCheck();

        $(window).on('resize', function() {
            doCheck();
        }); // when window is resized
    };

    doCheck= function() {
        tabsHeight = $tabs.innerHeight();
        children = $tabs.children('li:not(:last-child):not(:first-child)'); // Don't count the 'more' tab and always show first tab

        if (tabsHeight >= singleTabHeight) {

            while (tabsHeight > singleTabHeight && children.size() > 0) {
                $btnMore.show(); // show immediately when first tab is added to dropdown

                // move tab to dropdown
                $(children[children.size()-1]).prependTo($dropdown);

                // recalculate
                tabsHeight = $tabs.innerHeight();
                children = $tabs.children('li:not(:last-child):not(:first-child)');
            }

        } else {
            dropdownItems = $dropdown.children('li');

            while (tabsHeight < singleTabHeight && dropdownItems.size() > 0) {
                $(dropdownItems[0]).insertBefore($tabs.children('li:last-child'));

                // recalculate
                tabsHeight = $tabs.innerHeight();
                dropdownItems = $dropdown.children('li');
            }

            if (tabsHeight > singleTabHeight) { // double chk height again
                this.doCheck();
            }
        }

        // hide the more button if dropdown is empty
        dropdownItems = $dropdown.children('li');
        if (dropdownItems.size() <= 0) {
            $btnMore.hide();

        } else {
            $btnMore.show();

            // check if active element is in dropdown
            if ($dropdown.children('li.active').size() > 0) {
                $btnMore.addClass('active');
            } else {
                $btnMore.removeClass('active');
            }
        }
    };

    return {
        init: init
    };

}(jQuery, window));
