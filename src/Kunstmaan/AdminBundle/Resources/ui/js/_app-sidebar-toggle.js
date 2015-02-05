var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.sidebartoggle = (function(window, undefined) {

    var init,
        toggle;

    init = function() {
        toggle();
    };

    toggle = function() {
        var appMain = document.getElementById('app__main'),
            toggleButton = document.getElementById('app__sidebar-toggle');

        if(typeof toggleButton !== 'undefined' && toggleButton !== null) {
            toggleButton.addEventListener('click', function() {
                appMain.classList.toggle('app__main--altered-state');
            }, false);
        }
    };

    return {
        init: init
    };

}(window));
