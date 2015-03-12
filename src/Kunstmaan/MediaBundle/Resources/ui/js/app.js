var kunstmaanMediaBundle = kunstmaanMediaBundle || {};

kunstmaanMediaBundle.app = (function($, window, undefined) {

    var init;

    init = function() {
        kunstmaanMediaBundle.bulkUpload.init();
    };


    return {
        init: init
    };

}(jQuery, window));

$(function() {
    kunstmaanMediaBundle.app.init();
});
