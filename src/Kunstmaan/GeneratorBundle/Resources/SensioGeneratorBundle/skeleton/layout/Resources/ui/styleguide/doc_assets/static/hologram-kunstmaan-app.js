var hologramKunstmaan = hologramKunstmaan || {};

hologramKunstmaan.app = (function(window, undefined) {

    var init;

    init = function() {
        hologramKunstmaan.scrollspy.init();
    };

    return {
        init: init
    };

}(window));

document.addEventListener("DOMContentLoaded", function(event) {
    hologramKunstmaan.app.init();
});
