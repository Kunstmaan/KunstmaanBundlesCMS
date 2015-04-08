var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.checkIfEdited = (function($, window, undefined) {

    var init, _doUnload;

    var NeedCheck = $('body').hasClass('js-check-if-edited'),
        isEdited = false,
        oldEdited = false;


    init = function() {

        if(NeedCheck) {
            $('.js-save-btn').on('click', function() {
                window.onbeforeunload = null;
            });

            window.onbeforeunload = _doUnload;
        }
    };


    edited = function() {
        isEdited = true;
    };


    _doUnload = function() {
        if(isEdited) {
            return 'You haven\'t saved this page, are you sure you want to close it?';
        }
    };


    return {
        init: init,
        edited: edited
    };

}(jQuery, window));
