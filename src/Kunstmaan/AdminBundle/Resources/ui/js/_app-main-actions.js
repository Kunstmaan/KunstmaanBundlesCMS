var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.mainActions = (function(window, undefined) {

    var updateScroll;

    updateScroll = function(currentScrollY, $menu) {

        if(currentScrollY >= 180){
            $menu.addClass('page-main-actions--top--show');
        }

        if(currentScrollY < 180){
            $menu.removeClass('page-main-actions--top--show');
        }
    };

    return {
        updateScroll: updateScroll
    };

}(window));
