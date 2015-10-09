var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.autoCollapseButtons = (function($, window, undefined) {

    var init, createMoreDropdown,
        buttonsVisible,
        $autoCollapseButtons, $btnGroup, $allButtons, $buttonsRedundant, $moreButtonContainer, $moreButton, $caret, $dropdownList;

    init = function() {
        buttonsVisible = 2;

        $autoCollapseButtons = $('.js-auto-collapse-buttons');
        $btnGroup = $autoCollapseButtons.find('.btn-group');
        if ($btnGroup.parent().attr('data-visible-buttons')) {
            buttonsVisible = $btnGroup.parent().data('visible-buttons');
        }
        $allButtons = $btnGroup.children('button, a'); // select only anchors and buttons

        // add more-dropdown when there are at least 2 buttons for dropdown
        if ($allButtons.length > (buttonsVisible + 1)) {
            $buttonsRedundant = $allButtons.slice(buttonsVisible);
            createMoreDropdown();
        }
    };

    createMoreDropdown = function() {
        // create dom elements
        $moreButtonContainer = $('<div class="btn-group btn-group--more">').appendTo($btnGroup);
        $moreButton = $('<button class="btn btn-default btn--raise-on-hover dropdown-toggle" data-toggle="dropdown">').text('More ').appendTo($moreButtonContainer);
        $caret = $('<span class="fa fa-caret-down">').appendTo($moreButton);
        $dropdownList = $('<ul class="dropdown-menu dropdown-menu-right dropdown-menu--more">').appendTo($moreButtonContainer);

        // move buttons to dropdown list & remove styling
        $buttonsRedundant.each( function() {
            var $li = $('<li>');

            $(this).removeClass().addClass('btn-dropdown-menu').appendTo($li);
            $li.appendTo($dropdownList);
        });
    };

    return {
        init: init
    };

}(jQuery, window));
